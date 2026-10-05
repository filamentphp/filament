<?php

namespace Filament\Tests\Tables\Columns;

use BackedEnum;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasIcon as HasIconContract;
use Filament\Support\Contracts\HasLabel as HasLabelContract;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Enums\NavigationGroupEnum;
use Filament\Tests\Fixtures\Models\Company;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Mockery;
use Stringable;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('keeps inherited one-argument callers of `toOptimizedHtml()` compatible', function (): void {
    expect(TextColumnWithPublicOptimizedRenderer::make('title')->renderOptimized('Title'))
        ->toContain('Title');
});

describe('label formatting compatibility', function (): void {
    it('resolves enum labels after the formatter but before rich formatting', function (string $mode, string $expected): void {
        $column = TextColumn::make('status')
            ->{$mode}()
            ->formatStateUsing(static function (NavigationGroupEnum $state): NavigationGroupEnum {
                expect($state)->toBe(NavigationGroupEnum::Users);

                return NavigationGroupEnum::Settings;
            });

        expect($column->formatState(NavigationGroupEnum::Users)->toHtml())->toBe($expected);
    })->with([
        ['html', 'System Settings'],
        ['markdown', "<p>System Settings</p>\n"],
    ]);

    it('formats non-stringable labels according to their trust in rich modes', function (string $mode, string | Htmlable | null $label, string $expected): void {
        $state = new class($label) implements HasLabelContract
        {
            public function __construct(protected string | Htmlable | null $label) {}

            public function getLabel(): string | Htmlable | null
            {
                return $this->label;
            }
        };

        $column = TextColumn::make('status')->{$mode}()->prefix('<before>');

        expect($column->formatState($state)->toHtml())->toBe($expected);
    })->with([
        ['html', '<b onclick="alert(1)">Label</b><script>alert(2)</script>', '&lt;before&gt;<b>Label</b>'],
        ['html', null, '&lt;before&gt;'],
        ['markdown', null, '&lt;before&gt;'],
        ['markdown', new HtmlString('<b onclick="trusted()">**Label**</b>'), '&lt;before&gt;<b onclick="trusted()">**Label**</b>'],
    ]);

    it('preserves plain-mode label escaping and truncation', function (?int $limit, ?string $prefix, bool $isTrusted, string $expected): void {
        $state = new class implements HasLabelContract
        {
            public function getLabel(): HtmlString
            {
                return new HtmlString('<b onclick="trusted()">Alpha beta</b>');
            }
        };

        $formatted = TextColumn::make('status')->limit($limit)->prefix($prefix)->formatState($state);

        expect($formatted instanceof Htmlable)->toBe($isTrusted)
            ->and(e($formatted))->toBe($expected);

        if (! $isTrusted) {
            expect($formatted)->toBeString();
        }
    })->with([
        'unformatted fast path' => [null, null, true, '<b onclick="trusted()">Alpha beta</b>'],
        'non-truncating limit' => [100, null, true, '<b onclick="trusted()">Alpha beta</b>'],
        'truncating limit' => [2, null, false, '&lt;b...'],
        'string prefix' => [null, 'Label: ', false, 'Label: &lt;b onclick=&quot;trusted()&quot;&gt;Alpha beta&lt;/b&gt;'],
    ]);

    it('preserves existing `Stringable` and `Htmlable` precedence over labels in rich modes', function (bool $isHtmlable, string $expected): void {
        if ($isHtmlable) {
            $state = new class implements HasLabelContract, Htmlable
            {
                public function toHtml(): string
                {
                    return '<b onclick="trusted()">Original</b>';
                }

                public function getLabel(): string
                {
                    return 'Unused label';
                }
            };
        } else {
            $state = Mockery::mock(HasLabelContract::class);
            $state->shouldReceive('__toString')->andReturn('<b onclick="trusted()">Original</b>');
            $state->shouldNotReceive('getLabel');
        }

        expect(TextColumn::make('status')->html()->formatState($state)->toHtml())->toBe($expected);
    })->with([
        [false, '<b>Original</b>'],
        [true, '<b onclick="trusted()">Original</b>'],
    ]);

    it('keeps string labels untrusted without rich formatting', function (): void {
        $state = Mockery::mock(HasLabelContract::class);
        $state->shouldReceive('getLabel')->once()->andReturn('<b>Label</b>');

        $column = TextColumn::make('status')->prefix('Label: ');

        expect($column->isHtml())->toBeFalse()
            ->and($column->formatState($state))->toBe('Label: <b>Label</b>');
    });
});

it('renders labels and wrapper URLs accessibly in light and dark modes', function (): void {
    Artisan::call('filament:assets');
    Post::factory()->create(['title' => 'Quarterly report', 'content' => 'Review summary']);
    $this->actingAs(User::factory()->create());

    foreach ([false, true] as $isDarkMode) {
        $page = visit('/columns-browser-test');

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        $page
            ->assertNoSmoke()
            ->assertScript('document.querySelector(\'[data-testid="enum-label-column"]\').textContent.trim()', 'User Management')
            ->assertScript('document.querySelectorAll(\'[data-testid="linked-column"] a\').length', 1)
            ->assertScript('document.querySelectorAll(\'[data-testid="disabled-column"] a\').length', 0)
            ->assertNoAccessibilityIssues()
            ->click('[data-testid="toggle-reordering"]')
            ->assertScript('document.querySelectorAll(\'[data-testid="linked-column"] a\').length', 0)
            ->assertNoAccessibilityIssues();
    }
});

it('can set `badge()`', function (): void {
    expect(TextColumn::make('name')->badge()->isBadge())->toBeTrue();
});

it('defaults `isBadge()` to `false`', function (): void {
    expect(TextColumn::make('name')->isBadge())->toBeFalse();
});

it('can set `bulleted()`', function (): void {
    expect(TextColumn::make('name')->bulleted()->isBulleted())->toBeTrue();
});

it('defaults `isBulleted()` to `false`', function (): void {
    expect(TextColumn::make('name')->isBulleted())->toBeFalse();
});

it('can set `listWithLineBreaks()`', function (): void {
    expect(TextColumn::make('name')->listWithLineBreaks()->isListWithLineBreaks())->toBeTrue();
});

it('defaults `isListWithLineBreaks()` to `false`', function (): void {
    expect(TextColumn::make('name')->isListWithLineBreaks())->toBeFalse();
});

it('can set `limitList()` and get with `getListLimit()`', function (): void {
    expect(TextColumn::make('name')->limitList(5)->getListLimit())->toBe(5);
});

it('defaults `getListLimit()` to `null`', function (): void {
    expect(TextColumn::make('name')->getListLimit())->toBeNull();
});

it('can set `size()` with enum and get with `getSize()`', function (): void {
    expect(TextColumn::make('name')->size(TextSize::Large)->getSize(null))->toBe(TextSize::Large);
});

it('defaults `getSize()` to `TextSize::Small` when not set', function (): void {
    expect(TextColumn::make('name')->getSize(null))->toBe(TextSize::Small);
});

it('can set `expandableLimitedList()` and get with `isLimitedListExpandable()`', function (): void {
    expect(TextColumn::make('name')->expandableLimitedList()->isLimitedListExpandable())->toBeTrue();
});

it('defaults `isLimitedListExpandable()` to `false`', function (): void {
    expect(TextColumn::make('name')->isLimitedListExpandable())->toBeFalse();
});

it('can set `badge()` with a `Closure`', function (): void {
    expect(TextColumn::make('name')->badge(static fn (): bool => true)->isBadge())->toBeTrue();
});

it('can undo `badge()` with `false`', function (): void {
    expect(TextColumn::make('name')->badge()->badge(false)->isBadge())->toBeFalse();
});

it('can set `limitList()` with a `Closure`', function (): void {
    expect(TextColumn::make('name')->limitList(static fn (): int => 10)->getListLimit())->toBe(10);
});

it('uses `3` as default limit for `limitList()` when called without argument', function (): void {
    expect(TextColumn::make('name')->limitList()->getListLimit())->toBe(3);
});

it('can set `size()` with a `Closure`', function (): void {
    expect(TextColumn::make('name')->size(static fn (): TextSize => TextSize::Large)->getSize(null))->toBe(TextSize::Large);
});

it('maps `"base"` string to `TextSize::Medium` in `getSize()`', function (): void {
    expect(TextColumn::make('name')->size('base')->getSize(null))->toBe(TextSize::Medium);
});

it('can set `size()` with a string enum value', function (): void {
    expect(TextColumn::make('name')->size('lg')->getSize(null))->toBe(TextSize::Large);
});

it('can set `expandableLimitedList()` with a `Closure`', function (): void {
    expect(TextColumn::make('name')->expandableLimitedList(static fn (): bool => true)->isLimitedListExpandable())->toBeTrue();
});

describe('rendering', function (): void {
    it('can render', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumn::class)->assertSuccessful();
    });

    it('can render with `badge()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumnWithBadge::class)->assertSuccessful();
    });

    it('can render with `badge()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumnWithClosureBadge::class)->assertSuccessful();
    });

    it('can render with `badge(false)` undone', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumnWithBadgeUndone::class)->assertSuccessful();
    });

    it('can render with `bulleted()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumnWithBulleted::class)->assertSuccessful();
    });

    it('can render with `listWithLineBreaks()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumnWithListWithLineBreaks::class)->assertSuccessful();
    });

    it('can render with `limitList()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumnWithLimitList::class)->assertSuccessful();
    });

    it('can render with `limitList()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumnWithClosureLimitList::class)->assertSuccessful();
    });

    it('can render with `limitList()` default', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumnWithDefaultLimitList::class)->assertSuccessful();
    });

    it('can render with `size()` enum', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumnWithSizeEnum::class)->assertSuccessful();
    });

    it('can render with `size()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumnWithClosureSize::class)->assertSuccessful();
    });

    it('can render with `size()` string "base"', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumnWithSizeBase::class)->assertSuccessful();
    });

    it('can render with `size()` string enum value', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumnWithSizeString::class)->assertSuccessful();
    });

    it('can render with `expandableLimitedList()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumnWithExpandableLimitedList::class)->assertSuccessful();
    });

    it('can render with `expandableLimitedList()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderTextColumnWithClosureExpandableLimitedList::class)->assertSuccessful();
    });

    it('renders the `getLabel()` value when state implements `HasLabel`', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithHasLabelState::class)
            ->assertSuccessful()
            ->assertSeeHtml('User Management')
            ->assertDontSeeHtml('>users<');
    });

    it('renders the icon from state when state implements `HasIcon`', function (): void {
        Post::factory()->create();

        // Heroicons render as inline `<svg>` (no name in markup), but every icon
        // emits the `fi-icon` class via `generate_icon_html()`.
        livewire(RenderTextColumnWithHasIconState::class)
            ->assertSuccessful()
            ->assertSeeHtml('<svg')
            ->assertSeeHtml('fi-icon');
    });

    it('renders an `Htmlable` state via `toHtml()`', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithHtmlableState::class)
            ->assertSuccessful()
            ->assertSeeHtml('<strong>Bold value</strong>');
    });

    it('renders a `prefix()` before the state', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithPrefix::class)
            ->assertSuccessful()
            ->assertSeeHtml('Mr. ');
    });

    it('renders a `suffix()` after the state', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithSuffix::class)
            ->assertSuccessful()
            ->assertSeeHtml(' kg');
    });

    it('renders a `formatStateUsing()` closure result', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithFormatStateUsing::class)
            ->assertSuccessful()
            ->assertSeeHtml('FORMATTED-VALUE');
    });

    it('truncates with `limit()` and the default ellipsis', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithLimit::class)
            ->assertSuccessful()
            ->assertSeeHtml('Short...')
            ->assertDontSeeHtml('ShortStateThatGetsTruncated');
    });

    it('truncates with `words()` and the default ellipsis', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithWords::class)
            ->assertSuccessful()
            ->assertSeeHtml('one two...')
            ->assertDontSeeHtml('one two three four five');
    });

    it('renders an `html()` state through Filament\'s sanitizer', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithHtml::class)
            ->assertSuccessful()
            ->assertSeeHtml('<em>safe</em>')
            ->assertDontSeeHtml('<script>');
    });

    it('renders a `markdown()` state as HTML', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithMarkdown::class)
            ->assertSuccessful()
            ->assertSeeHtml('<strong>bold</strong>');
    });

    it('renders `money()` formatted state', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithMoney::class)
            ->assertSuccessful()
            ->assertSeeHtml('$1,234.56');
    });

    it('renders `numeric()` formatted state', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithNumeric::class)
            ->assertSuccessful()
            ->assertSeeHtml('1,234');
    });

    it('injects `$state` and `$relatedRecord` into nested numeric configuration', function (): void {
        $relatedRecord = new User(['email' => 'user@example.com']);

        $formattedState = TextColumn::make('cost')
            ->numeric(
                decimalPlaces: static fn (float $state, User $relatedRecord): int => (($state === 1234.5) && ($relatedRecord->email === 'user@example.com')) ? 1 : 0,
                decimalSeparator: static fn (float $state, User $relatedRecord): string => (($state === 1234.5) && ($relatedRecord->email === 'user@example.com')) ? '.' : ',',
                thousandsSeparator: static fn (float $state, User $relatedRecord): string => (($state === 1234.5) && ($relatedRecord->email === 'user@example.com')) ? '' : ',',
            )
            ->formatState(1234.5, $relatedRecord);

        expect($formattedState)->toBe('1234.5');
    });

    it('renders `date()` formatted state', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithDate::class)
            ->assertSuccessful()
            ->assertSeeHtml('2025-06-15');
    });

    it('keeps wrapper `url()` links exclusive and respects click suppression', function (string $urlType, string $rendering): void {
        $post = Post::factory()->create(['title' => 'Quarterly report']);
        $url = ($urlType === 'record') ? "https://example.test/posts/{$post->getKey()}" : 'https://example.test/foo';

        $component = livewire(RenderTextColumnWithUrl::class, compact('urlType', 'rendering'))
            ->assertSuccessful();
        $column = $component->instance()->getTable()->getColumn('title')->record($post);

        expect(preg_match_all('/<a\b/', $component->html()))->toBe(1)
            ->and(substr_count($component->html(), 'href="' . $url . '"'))->toBe(1)
            ->and(preg_match_all('/<a\b/', $column->renderInLayout()->toHtml()))->toBe(1)
            ->and($column->getUrl())->toBe($url)
            ->and($column->getUrl(null))->toBeNull()
            ->and($column->getUrl('Alpha', new User))->toBeNull();

        $component->set('disableColumnClick', true);
        expect(preg_match_all('/<a\b/', $component->html()))->toBe(0);

        $component->set('disableColumnClick', false)->call('toggleTableReordering')->assertSet('isTableReordering', true);
        expect(preg_match_all('/<a\b/', $component->html()))->toBe(0);
    })->with(['constant', 'closure', 'record'])->with(['optimized', 'rich', 'list', 'collapsed']);

    it('resolves item `url()` closures only for explicit item arguments', function (string $urlType): void {
        $column = TextColumn::make('title')
            ->record(new Post(['title' => 'Parent']))
            ->url(match ($urlType) {
                'state' => static fn (?string $state): string => '/items/' . ($state ?? 'empty'),
                'related' => static fn (?User $relatedRecord): string => '/items/' . ($relatedRecord?->name ?? 'empty'),
                'combined' => static fn (?string $state, Post $record, ?User $relatedRecord): string => "/items/{$record->title}/" . ($state ?? 'empty') . '/' . ($relatedRecord?->name ?? 'empty'),
            });

        expect($column->getUrl())->toBeNull()
            ->and($column->getUrl(null))->toBe(($urlType === 'combined') ? '/items/Parent/empty/empty' : '/items/empty')
            ->and($column->getUrl('Alpha', new User(['name' => 'Beta'])))->toBe(match ($urlType) {
                'state' => '/items/Alpha',
                'related' => '/items/Beta',
                'combined' => '/items/Parent/Alpha/Beta',
            });
    })->with(['state', 'related', 'combined']);

    it('adds `target="_blank"` when `openUrlInNewTab()` is set', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithUrlInNewTab::class)
            ->assertSuccessful()
            ->assertSeeHtml('target="_blank"');
    });

    it('renders a `tooltip()` as an `x-tooltip` attribute', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithTooltip::class)
            ->assertSuccessful()
            ->assertSeeHtml('x-tooltip');
    });

    it('marks a `copyable()` cell with the copyable class', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithCopyable::class)
            ->assertSuccessful()
            ->assertSeeHtml('fi-copyable');
    });

    it('uses the displayed collapsed-list state for the clipboard', function (): void {
        $post = Post::factory()->create();

        $column = livewire(RenderTextColumnWithCopyable::class)
            ->instance()
            ->getTable()
            ->getColumn('title')
            ->state([1, 2])
            ->numeric(decimalSeparator: '.', thousandsSeparator: ',')
            ->prefix('!')
            ->copyable()
            ->record($post);
        $column->clearCachedState();

        expect($column->toEmbeddedHtml())->toContain("clipboard.writeText('!1, !2')");
    });

    it('renders an `icon()` as an SVG/`fi-icon` element', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithIcon::class)
            ->assertSuccessful()
            ->assertSeeHtml('fi-icon');
    });

    it('renders a `weight()` as a `fi-font-*` class', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithWeight::class)
            ->assertSuccessful()
            ->assertSeeHtml('fi-font-bold');
    });

    it('renders a string `color()` as a `fi-color-*` class', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithStringColor::class)
            ->assertSuccessful()
            ->assertSeeHtml('fi-color-danger');
    });

    it('renders an array `color()` as `fi-color` plus inline custom-color styles', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithArrayColor::class)
            ->assertSuccessful()
            ->assertSeeHtml('fi-color')
            ->assertSeeHtml('--color-');
    });

    it('renders a `description()` below the state', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithDescription::class)
            ->assertSuccessful()
            ->assertSeeHtml('fi-ta-text-description')
            ->assertSeeHtml('Some helper description');
    });

    it('renders a `description()` above the state when `position: above` is given', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithDescriptionAbove::class)
            ->assertSuccessful()
            ->assertSeeHtml('fi-ta-text-description')
            ->assertSeeHtml('Above description');
    });

    it('renders a `lineClamp()` as a `--line-clamp` style', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithLineClamp::class)
            ->assertSuccessful()
            ->assertSeeHtml('--line-clamp: 2');
    });

    it('renders a `wrap()` as the `fi-wrapped` class', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithWrap::class)
            ->assertSuccessful()
            ->assertSeeHtml('fi-wrapped');
    });

    it('renders an `alignment()` as a `fi-align-*` class', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithAlignmentCenter::class)
            ->assertSuccessful()
            ->assertSeeHtml('fi-align-center');
    });

    it('renders a `fontFamily()` as a `fi-font-*` class', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithFontFamily::class)
            ->assertSuccessful()
            ->assertSeeHtml('fi-font-mono');
    });

    it('renders `bulleted()` lists as `fi-bulleted`', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithBulletedAndArrayState::class)
            ->assertSuccessful()
            ->assertSeeHtml('fi-bulleted');
    });

    it('renders `listWithLineBreaks()` lists as `fi-ta-text-has-line-breaks`', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithListAndArrayState::class)
            ->assertSuccessful()
            ->assertSeeHtml('fi-ta-text-has-line-breaks');
    });

    it('renders extra cell attributes via `extraAttributes()`', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithExtraAttributes::class)
            ->assertSuccessful()
            ->assertSeeHtml('data-test-marker="present"');
    });

    it('renders a placeholder when state is empty', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithEmptyStateAndPlaceholder::class)
            ->assertSuccessful()
            ->assertSeeHtml('No data');
    });

    it('renders a `badge()` with the `fi-badge` class', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnBadgeContents::class)
            ->assertSuccessful()
            ->assertSeeHtml('fi-badge');
    });

    it('injects the related model for each relationship state item without changing `$record`', function (): void {
        $team = Team::factory()->create(['name' => 'Framework team']);
        $firstUser = User::factory()->create(['name' => 'Duplicate name']);
        $secondUser = User::factory()->create(['name' => 'Duplicate name']);
        $emptyTeam = Team::factory()->create(['name' => 'Empty team']);

        $team->users()->attach([$firstUser, $secondUser]);

        livewire(RenderTextColumnWithRelatedRecords::class)
            ->assertSuccessful()
            ->assertSeeText("Duplicate name:{$firstUser->getKey()}:Framework team:{$firstUser->email}:Duplicate name")
            ->assertSeeText("Duplicate name:{$secondUser->getKey()}:Framework team:{$secondUser->email}:Duplicate name")
            ->assertSeeText("{$emptyTeam->name}:none:No users");
    });

    it('injects the terminal `$relatedRecord` for each nested relationship state item', function (): void {
        $company = Company::factory()->create(['name' => 'Acme']);
        $firstTeam = Team::factory()->create(['company_id' => $company->getKey()]);
        $secondTeam = Team::factory()->create(['company_id' => $company->getKey()]);
        $firstUser = User::factory()->create(['name' => 'First user', 'email' => 'first@example.com']);
        $secondUser = User::factory()->create(['name' => 'Second user', 'email' => 'second@example.com']);

        $firstTeam->users()->attach($firstUser);
        $secondTeam->users()->attach($secondUser);

        livewire(RenderTextColumnWithNestedRelatedRecords::class)
            ->assertSuccessful()
            ->assertSeeText("{$company->name}:{$firstUser->email}:{$firstUser->name}")
            ->assertSeeText("{$company->name}:{$secondUser->email}:{$secondUser->name}");
    });

    it('does not cache nested relationship cardinality from a record with a missing intermediate relationship', function (): void {
        $postWithoutAuthor = Post::factory()->create(['author_id' => null]);
        $author = User::factory()->create();
        $team = Team::factory()->create(['name' => 'Framework team']);
        $author->teams()->attach($team);
        $postWithAuthor = Post::factory()->create(['author_id' => $author->getKey()]);

        $column = TextColumn::make('author.teams.name')->record($postWithoutAuthor);

        expect($column->getStateFromRecord())->toBeNull();

        $column->record($postWithAuthor);

        expect($column->getStateFromRecord())->toBe(['Framework team']);
    });

    it('injects the related model when rendering an optimized singular relationship state', function (): void {
        $author = User::factory()->create(['name' => 'Related author']);
        $post = Post::factory()->create([
            'author_id' => $author->getKey(),
            'title' => 'Parent post',
        ]);

        livewire(RenderOptimizedTextColumnWithRelatedRecord::class)
            ->assertSuccessful()
            ->assertSeeText("{$post->title}:{$author->email}:{$author->name}");
    });

    it('does not reuse cached state or relationship records when rebound using only `record()`', function (): void {
        $firstTeam = Team::factory()->create();
        $firstUser = User::factory()->create(['name' => 'First user']);
        $firstTeam->users()->attach($firstUser);

        $secondTeam = Team::factory()->create();
        $secondUser = User::factory()->create(['name' => 'Second user']);
        $secondTeam->users()->attach($secondUser);

        $column = livewire(RenderTextColumnWithRelatedRecords::class)
            ->instance()
            ->getTable()
            ->getColumn('users.name');
        $column->clearCachedState();

        $column->record($firstTeam)->recordKey((string) $firstTeam->getKey());

        expect($column->getState())->toBe(['First user'])
            ->and(collect($column->getRelatedRecords())->map->getKey()->all())->toBe([$firstUser->getKey()]);

        $column->record($secondTeam);

        expect($column->getState())->toBe(['Second user'])
            ->and(collect($column->getRelatedRecords())->map->getKey()->all())->toBe([$secondUser->getKey()]);

        $column->record($firstTeam);

        expect($column->getState())->toBe(['First user'])
            ->and(collect($column->getRelatedRecords())->map->getKey()->all())->toBe([$firstUser->getKey()]);
    });

    it('injects the correct related model throughout every relationship item rendering path', function (): void {
        $team = Team::factory()->create(['name' => 'Framework team']);
        $users = collect([
            User::factory()->create([
                'name' => '',
                'email' => 'first@example.com',
                'json' => ['color' => '#ff0000', 'duplicate' => '', 'icon' => 'first', 'image' => 'https://example.com/first.jpg'],
            ]),
            User::factory()->create([
                'name' => 'Duplicate name',
                'email' => 'second@example.com',
                'json' => ['color' => '#00ff00', 'duplicate' => 'Duplicate', 'icon' => 'second', 'image' => 'https://example.com/second.jpg'],
            ]),
            User::factory()->create([
                'name' => 'Duplicate name',
                'email' => 'third@example.com',
                'json' => ['color' => '#0000ff', 'duplicate' => 'Duplicate', 'icon' => 'third', 'image' => 'https://example.com/third.jpg'],
            ]),
            User::factory()->create([
                'name' => 'Unique name',
                'email' => 'fourth@example.com',
                'json' => ['color' => '#ffff00', 'duplicate' => 'Unique', 'icon' => 'fourth', 'image' => 'https://example.com/fourth.jpg'],
            ]),
        ]);

        $team->users()->attach($users);

        livewire(RenderColumnsWithRelatedRecords::class)
            ->assertSuccessful()
            ->assertSeeText('Framework team:second@example.com:Duplicate name')
            ->assertSeeText('Framework team:third@example.com:Duplicate name')
            ->assertSeeHtml('href="/users/' . $users[1]->getKey() . '"')
            ->assertSeeHtml('href="/users/' . $users[2]->getKey() . '"')
            ->assertSeeText($users->map(fn (User $user): string => "{$user->getKey()}:{$user->email}:{$user->email}")->implode(', '))
            ->assertSeeText('distinct:second@example.com:Duplicate')
            ->assertDontSeeText('distinct:third@example.com:Duplicate')
            ->assertSeeText('distinct:fourth@example.com:Unique')
            ->assertSeeHtml('alt="image-' . $users[0]->getKey() . '-' . $users[0]->json['image'] . '"')
            ->assertSeeHtml('alt="image-' . $users[1]->getKey() . '-' . $users[1]->json['image'] . '"')
            ->assertDontSeeHtml('alt="image-' . $users[2]->getKey() . '-' . $users[2]->json['image'] . '"')
            ->assertSeeHtml('icon-' . $users[2]->getKey())
            ->assertSeeHtml('color-' . $users[2]->getKey())
            ->assertSeeHtml('copy-' . $users[2]->getKey());
    });
});

class RenderTextColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithRelatedRecords extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Team::query())->columns([
            TextColumn::make('users.name')
                ->badge()
                ->default('No users')
                ->prefix(static fn (mixed $state, ?User $relatedRecord): string => $relatedRecord ? "{$state}:{$relatedRecord->getKey()}:" : '')
                ->formatStateUsing(static fn (Team $record, ?User $relatedRecord, string $state): string => "{$record->name}:" . ($relatedRecord?->email ?? 'none') . ":{$state}"),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithNestedRelatedRecords extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Company::query())->columns([
            TextColumn::make('teams.users.name')
                ->badge()
                ->formatStateUsing(static fn (Company $record, User $relatedRecord, string $state): string => "{$record->name}:{$relatedRecord->email}:{$state}"),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderOptimizedTextColumnWithRelatedRecord extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('author.name')
                ->formatStateUsing(static fn (Post $record, User $relatedRecord, string $state): string => "{$record->title}:{$relatedRecord->email}:{$state}"),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnsWithRelatedRecords extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Team::query())->columns([
            TextColumn::make('users.name')
                ->badge()
                ->colors(static fn (string $state, User $relatedRecord): array => [
                    'success' => static fn (string $state, User $relatedRecord): bool => $state === $relatedRecord->name,
                ])
                ->icons(static fn (string $state, User $relatedRecord): array => [
                    'heroicon-o-check' => static fn (string $state, User $relatedRecord): bool => $state === $relatedRecord->name,
                ])
                ->formatStateUsing(static fn (Team $record, User $relatedRecord, string $state): string => "{$record->name}:{$relatedRecord->email}:{$state}")
                ->url(static fn (string $state, User $relatedRecord): string => ($state === $relatedRecord->name) ? "/users/{$relatedRecord->getKey()}" : '/invalid')
                ->tooltip(static fn (string $state, User $relatedRecord): string => "text-{$relatedRecord->getKey()}-{$state}"),
            TextColumn::make('users.email')
                ->copyable()
                ->prefix(static fn (string $state, User $relatedRecord): string => "{$relatedRecord->getKey()}:{$state}:"),
            TextColumn::make('users.json.duplicate')
                ->distinctList()
                ->badge()
                ->formatStateUsing(static fn (User $relatedRecord, string $state): string => "distinct:{$relatedRecord->email}:{$state}"),
            ImageColumn::make('users.json.image')
                ->limit(2)
                ->checkFileExistence(static fn (string $state, User $relatedRecord): bool => $state !== $relatedRecord->json['image'])
                ->alt(static fn (string $state, User $relatedRecord): string => "image-{$relatedRecord->getKey()}-{$state}"),
            IconColumn::make('users.json.icon')
                ->icon(Heroicon::Check)
                ->tooltip(static fn (string $state, User $relatedRecord): string => "icon-{$relatedRecord->getKey()}-{$state}"),
            ColorColumn::make('users.json.color')
                ->copyable(static fn (string $state, User $relatedRecord): bool => $state === $relatedRecord->json['color'])
                ->copyableState(static fn (string $state, User $relatedRecord): string => "copy-{$relatedRecord->getKey()}-{$state}")
                ->tooltip(static fn (string $state, User $relatedRecord): string => "color-{$relatedRecord->getKey()}-{$state}"),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithBadge extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->badge(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithClosureBadge extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->badge(static fn (): bool => true),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithBadgeUndone extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->badge()->badge(false),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithBulleted extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->bulleted(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithListWithLineBreaks extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->listWithLineBreaks(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithLimitList extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->limitList(5),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithClosureLimitList extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->limitList(static fn (): int => 10),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithDefaultLimitList extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->limitList(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithSizeEnum extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->size(TextSize::Large),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithClosureSize extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->size(static fn (): TextSize => TextSize::Large),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithSizeBase extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->size('base'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithSizeString extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->size('lg'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithExpandableLimitedList extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->listWithLineBreaks()->limitList(1)->expandableLimitedList(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithClosureExpandableLimitedList extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->listWithLineBreaks()->limitList(1)->expandableLimitedList(static fn (): bool => true),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

enum TextColumnHasLabelEnum: string implements HasLabelContract
{
    case Users = 'users';

    public function getLabel(): string
    {
        return 'User Management';
    }
}

class TextColumnHasIconState implements HasIconContract, Stringable
{
    public function __construct(protected string $value) {}

    public function __toString(): string
    {
        return $this->value;
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-star';
    }
}

class RenderTextColumnWithHasLabelState extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('status')
                ->state(static fn (): TextColumnHasLabelEnum => TextColumnHasLabelEnum::Users),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithHasIconState extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('status')
                ->state(static fn (): TextColumnHasIconState => new TextColumnHasIconState('Active')),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithHtmlableState extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('content')
                ->state(static fn (): HtmlString => new HtmlString('<strong>Bold value</strong>')),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithPrefix extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->state(static fn (): string => 'John')->prefix('Mr. '),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithSuffix extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->state(static fn (): string => '42')->suffix(' kg'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithFormatStateUsing extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')
                ->state(static fn (): string => 'raw')
                ->formatStateUsing(static fn (string $state): string => 'FORMATTED-VALUE'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithLimit extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')
                ->state(static fn (): string => 'ShortStateThatGetsTruncated')
                ->limit(5),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithWords extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')
                ->state(static fn (): string => 'one two three four five')
                ->words(2),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithHtml extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')
                ->state(static fn (): string => '<em>safe</em><script>alert(1)</script>')
                ->html(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithMarkdown extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')
                ->state(static fn (): string => '**bold**')
                ->markdown(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithMoney extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->state(static fn (): float => 1234.56)->money('USD'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithNumeric extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->state(static fn (): int => 1234)->numeric(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithDate extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')
                ->state(static fn (): string => '2025-06-15 10:30:00')
                ->date('Y-m-d'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithUrl extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public string $urlType = 'closure';

    public string $rendering = 'optimized';

    public bool $disableColumnClick = false;

    public function table(Table $table): Table
    {
        $column = TextColumn::make('title')
            ->url(match ($this->urlType) {
                'constant' => 'https://example.test/foo',
                'closure' => static fn (): string => 'https://example.test/foo',
                'record' => static fn (Post $record): string => "https://example.test/posts/{$record->getKey()}",
            })
            ->disabledClick(fn (): bool => $this->disableColumnClick);

        match ($this->rendering) {
            'rich' => $column->tooltip('Details'),
            'list' => $column->state(['Alpha', 'Beta'])->listWithLineBreaks(),
            'collapsed' => $column->state(['Alpha', 'Beta']),
            default => null,
        };

        return $table->query(Post::query())->reorderable('id')->columns([$column]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithUrlInNewTab extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')
                ->url(static fn (): string => 'https://example.test/foo', shouldOpenInNewTab: true),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithTooltip extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->tooltip('Helpful tip'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithCopyable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->copyable(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithIcon extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->icon('heroicon-o-star'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithWeight extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->weight(FontWeight::Bold),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithStringColor extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->color('danger'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithArrayColor extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->color(Color::Blue),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithDescription extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->description('Some helper description'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithDescriptionAbove extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->description('Above description', position: 'above'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithLineClamp extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->lineClamp(2),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithWrap extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->wrap(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithAlignmentCenter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->alignment(Alignment::Center),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithFontFamily extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->fontFamily(FontFamily::Mono),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithBulletedAndArrayState extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('tags')
                ->state(static fn (): array => ['one', 'two'])
                ->bulleted(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithListAndArrayState extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('tags')
                ->state(static fn (): array => ['one', 'two'])
                ->listWithLineBreaks(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithExtraAttributes extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->extraAttributes(['data-test-marker' => 'present']),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnWithEmptyStateAndPlaceholder extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->state(static fn (): ?string => null)->placeholder('No data'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextColumnBadgeContents extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->badge(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TextColumnWithPublicOptimizedRenderer extends TextColumn
{
    public function renderOptimized(mixed $state): string
    {
        return $this->toOptimizedHtml($state);
    }
}
