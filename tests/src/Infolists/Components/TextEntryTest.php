<?php

namespace Filament\Tests\Infolists\Components;

use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Enums\TextSize;
use Filament\Tests\Fixtures\Enums\NavigationGroupEnum;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Mockery;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithTextEntry::class)
        ->assertSuccessful()
        ->assertSeeText('Test Name');
});

it('can format state using `formatStateUsing()`', function (): void {
    livewire(TestComponentWithFormattedTextEntry::class)
        ->assertSuccessful()
        ->assertSeeText('HELLO WORLD');
});

it('renders array state as JSON instead of crashing', function (): void {
    livewire(TestComponentWithArrayStateTextEntry::class)
        ->assertSuccessful()
        ->assertSeeText('{"key":"value"}');
});

it('can display multiple values', function (): void {
    livewire(TestComponentWithMultipleTextEntry::class)
        ->assertSuccessful()
        ->assertSeeText('Tag 1')
        ->assertSeeText('Tag 2')
        ->assertSeeText('Tag 3');
});

it('can set `badge()`', function (): void {
    $entry = TextEntry::make('name');
    expect($entry->isBadge())->toBeFalse();
    $entry->badge();
    expect($entry->isBadge())->toBeTrue();
});

it('can set `badge()` to false to undo', function (): void {
    $entry = TextEntry::make('name')->badge()->badge(false);
    expect($entry->isBadge())->toBeFalse();
});

it('can set `bulleted()`', function (): void {
    $entry = TextEntry::make('name');
    expect($entry->isBulleted())->toBeFalse();
    $entry->bulleted();
    expect($entry->isBulleted())->toBeTrue();
});

it('can set `listWithLineBreaks()`', function (): void {
    $entry = TextEntry::make('name');
    expect($entry->isListWithLineBreaks())->toBeFalse();
    $entry->listWithLineBreaks();
    expect($entry->isListWithLineBreaks())->toBeTrue();
});

it('`isListWithLineBreaks()` returns true when `bulleted()` is set', function (): void {
    $entry = TextEntry::make('name')->bulleted();
    expect($entry->isListWithLineBreaks())->toBeTrue();
});

it('can set `limitList()`', function (): void {
    $entry = TextEntry::make('name')->limitList(5);
    expect($entry->getListLimit())->toBe(5);
});

it('returns the default limit of `3` for `getListLimit()` when calling `limitList()` with no argument', function (): void {
    $entry = TextEntry::make('name')->limitList();
    expect($entry->getListLimit())->toBe(3);
});

it('returns `null` for `getListLimit()` by default', function (): void {
    $entry = TextEntry::make('name');
    expect($entry->getListLimit())->toBeNull();
});

it('can set `prose()`', function (): void {
    livewire(TestComponentWithProseTextEntry::class)
        ->assertSuccessful();
});

it('can set `size()`', function (): void {
    $entry = TextEntry::make('name')->size(TextSize::Large);
    expect($entry->getSize(null))->toBe(TextSize::Large);
});

it('returns `TextSize::Small` for `getSize()` when no size is set', function (): void {
    $entry = TextEntry::make('name');
    expect($entry->getSize(null))->toBe(TextSize::Small);
});

it('converts `"base"` string to `TextSize::Medium` in `getSize()`', function (): void {
    $entry = TextEntry::make('name')->size('base');
    expect($entry->getSize(null))->toBe(TextSize::Medium);
});

it('can set `expandableLimitedList()`', function (): void {
    $entry = TextEntry::make('name');
    expect($entry->isLimitedListExpandable())->toBeFalse();
    $entry->expandableLimitedList();
    expect($entry->isLimitedListExpandable())->toBeTrue();
});

it('returns `true` for `canWrapByDefault()`', function (): void {
    $entry = TextEntry::make('name');
    expect($entry->canWrapByDefault())->toBeTrue();
});

it('can set `badge()` with a `Closure`', function (): void {
    $entry = TextEntry::make('title')
        ->badge(static fn (): bool => true);

    expect($entry->isBadge())->toBeTrue();
});

it('can set `limitList()` with a `Closure`', function (): void {
    $entry = TextEntry::make('tags')
        ->limitList(static fn (): int => 5);

    expect($entry->getListLimit())->toBe(5);
});

it('can set `size()` with a `Closure`', function (): void {
    $entry = TextEntry::make('title')
        ->size(static fn (): TextSize => TextSize::Large);

    expect($entry->getSize(null))->toBe(TextSize::Large);
});

it('can set `size()` with a string enum value', function (): void {
    $entry = TextEntry::make('title')
        ->size('lg');

    expect($entry->getSize(null))->toBe(TextSize::Large);
});

it('can set `expandableLimitedList()` with a `Closure`', function (): void {
    $entry = TextEntry::make('tags')
        ->expandableLimitedList(static fn (): bool => true);

    expect($entry->isLimitedListExpandable())->toBeTrue();
});

it('returns fluent `$this` from aggregate methods', function (): void {
    $entry = TextEntry::make('posts_count');

    expect($entry->avg('posts', 'rating'))->toBe($entry);
    expect($entry->counts('posts'))->toBe($entry);
    expect($entry->max('posts', 'rating'))->toBe($entry);
    expect($entry->min('posts', 'rating'))->toBe($entry);
    expect($entry->sum('posts', 'rating'))->toBe($entry);
});

it('defaults `isBulleted()` to `false`', function (): void {
    expect(TextEntry::make('tags')->isBulleted())->toBeFalse();
});

it('defaults `isListWithLineBreaks()` to `false`', function (): void {
    expect(TextEntry::make('tags')->isListWithLineBreaks())->toBeFalse();
});

it('defaults `isLimitedListExpandable()` to `false`', function (): void {
    expect(TextEntry::make('tags')->isLimitedListExpandable())->toBeFalse();
});

describe('formatting trust boundaries', function (): void {
    it('keeps plain formatter output and string affixes untrusted', function (): void {
        $entry = TextEntry::make('content')
            ->container(Schema::make())
            ->formatStateUsing(static fn (string $state): string => '<script>' . $state . '</script>')
            ->prefix('<b>Before &</b>')
            ->suffix('<i>After</i>');

        expect($entry->formatState('alert(1)'))
            ->toBe('<b>Before &</b><script>alert(1)</script><i>After</i>');
    });

    it('sanitizes formatter output in rich modes without truncating HTML', function (string $mode, string $input, string $expected): void {
        $entry = TextEntry::make('content')
            ->container(Schema::make())
            ->{$mode}(static fn (): bool => true)
            ->formatStateUsing(static fn (): string => $input)
            ->limit(2)
            ->words(1)
            ->prefix('<before>')
            ->suffix('<after>');

        $formatted = $entry->formatState('Original safe value');

        expect($formatted)->toBeInstanceOf(HtmlString::class)
            ->and($formatted->toHtml())->toBe('&lt;before&gt;' . $expected . '&lt;after&gt;');
    })->with([
        'HTML' => ['html', '<strong onclick="alert(1)">Alpha beta</strong><script>alert(2)</script><a href="javascript:alert(3)">Unsafe link</a>', '<strong>Alpha beta</strong><a>Unsafe link</a>'],
        'prose alone' => ['prose', '<strong onclick="alert(1)">Alpha beta</strong><script>alert(2)</script><a href="javascript:alert(3)">Unsafe link</a>', '<strong>Alpha beta</strong><a>Unsafe link</a>'],
        'Markdown' => ['markdown', '**Alpha beta** <i onclick="alert(1)">Gamma</i> [Unsafe link](javascript:alert%283%29)', "<p><strong>Alpha beta</strong> <i>Gamma</i> <a>Unsafe link</a></p>\n"],
    ]);

    it('honors disabled rich modes', function (): void {
        $entry = TextEntry::make('content')
            ->container(Schema::make())
            ->html()->html(static fn (): bool => false)
            ->markdown()->markdown(false)
            ->prose()->prose(false);

        expect($entry->formatState('<b onclick="alert(1)">Plain</b>'))
            ->toBe('<b onclick="alert(1)">Plain</b>');
    });

    it('preserves explicitly trusted formatter output even in rich modes', function (string $mode): void {
        $content = new class implements HasLabel, Htmlable
        {
            public function toHtml(): string
            {
                return '<span onclick="trusted()">**Alpha beta**</span>';
            }

            public function getLabel(): string
            {
                return 'Unused label';
            }
        };

        $entry = TextEntry::make('content')
            ->container(Schema::make())
            ->{$mode}()
            ->formatStateUsing(static fn (): Htmlable => $content)
            ->limit(2)
            ->words(1);

        expect($entry->formatState('Original')->toHtml())
            ->toBe('<span onclick="trusted()">**Alpha beta**</span>');
    })->with(['html', 'markdown', 'prose']);

    it('sanitizes `RichContentAttribute` before the general `Htmlable` bypass', function (bool $isMarkdown): void {
        // Return unsafe HTML at the boundary so the renderer's own sanitizer cannot mask a regression here.
        $content = Mockery::mock(RichContentAttribute::class);
        $content->shouldReceive('toHtml')->once()
            ->andReturn('<p onclick="alert(1)">Alpha beta</p><script>alert(2)</script>');

        $entry = TextEntry::make('content')
            ->container(Schema::make())
            ->markdown($isMarkdown)
            ->limit(2)
            ->words(1);

        expect($entry->formatState($content)->toHtml())->toBe('<p>Alpha beta</p>');
    })->with([false, true]);

    it('resolves enum labels after the formatter but before rich formatting', function (string $mode, string $expected): void {
        $entry = TextEntry::make('content')
            ->container(Schema::make())
            ->{$mode}()
            ->formatStateUsing(static function (NavigationGroupEnum $state): NavigationGroupEnum {
                expect($state)->toBe(NavigationGroupEnum::Users);

                return NavigationGroupEnum::Settings;
            });

        expect($entry->formatState(NavigationGroupEnum::Users)->toHtml())->toBe($expected);
    })->with([
        ['html', 'System Settings'],
        ['prose', 'System Settings'],
        ['markdown', "<p>System Settings</p>\n"],
    ]);

    it('formats non-stringable labels according to their trust in rich modes', function (string $mode, string | Htmlable | null $label, string $expected): void {
        $state = new class($label) implements HasLabel
        {
            public function __construct(protected string | Htmlable | null $label) {}

            public function getLabel(): string | Htmlable | null
            {
                return $this->label;
            }
        };

        $entry = TextEntry::make('content')
            ->container(Schema::make())
            ->{$mode}()
            ->prefix('<before>');

        expect($entry->formatState($state)->toHtml())->toBe($expected);
    })->with([
        ['html', '<b onclick="alert(1)">Label</b><script>alert(2)</script>', '&lt;before&gt;<b>Label</b>'],
        ['html', null, '&lt;before&gt;'],
        ['markdown', null, '&lt;before&gt;'],
        ['markdown', new HtmlString('<b onclick="trusted()">**Label**</b>'), '&lt;before&gt;<b onclick="trusted()">**Label**</b>'],
    ]);

    it('preserves plain-mode label escaping and truncation', function (?int $limit, ?string $prefix, bool $isTrusted, string $expected): void {
        $state = new class implements HasLabel
        {
            public function getLabel(): HtmlString
            {
                return new HtmlString('<b onclick="trusted()">Alpha beta</b>');
            }
        };

        $entry = TextEntry::make('content')
            ->container(Schema::make())
            ->limit($limit)
            ->prefix($prefix);

        $formatted = $entry->formatState($state);

        expect($formatted instanceof Htmlable)->toBe($isTrusted)
            ->and(e($formatted))->toBe($expected);

        if (! $isTrusted) {
            expect($formatted)->toBeString();
        }
    })->with([
        'unformatted' => [null, null, true, '<b onclick="trusted()">Alpha beta</b>'],
        'non-truncating limit' => [100, null, true, '<b onclick="trusted()">Alpha beta</b>'],
        'truncating limit' => [2, null, false, '&lt;b...'],
        'string prefix' => [null, 'Label: ', false, 'Label: &lt;b onclick=&quot;trusted()&quot;&gt;Alpha beta&lt;/b&gt;'],
    ]);

    it('preserves sanitized `Stringable` output instead of switching to a trusted label', function (string $mode, string $expected): void {
        $state = Mockery::mock(HasLabel::class);
        $state->shouldReceive('__toString')->andReturn('<b onclick="alert(1)">Original</b>');
        $state->shouldNotReceive('getLabel');

        $entry = TextEntry::make('content')
            ->container(Schema::make())
            ->{$mode}();

        expect($entry->formatState($state)->toHtml())->toBe($expected);
    })->with([
        ['html', '<b>Original</b>'],
        ['prose', '<b>Original</b>'],
        ['markdown', "<p><b>Original</b></p>\n"],
    ]);

    it('escapes only untrusted parts when affixes promote plain state to HTML', function (bool $trustedPrefix, bool $trustedSuffix, string $expected): void {
        $entry = TextEntry::make('content')
            ->container(Schema::make())
            ->prefix(static fn (): string | HtmlString => $trustedPrefix ? new HtmlString('<b onclick="before()">Before</b>') : '<b>Before &</b>')
            ->suffix(static fn (): string | HtmlString => $trustedSuffix ? new HtmlString('<i onclick="after()">After</i>') : '<i>After "</i>');

        expect($entry->formatState('<script>alert(1)</script>&')->toHtml())->toBe($expected);
    })->with([
        'trusted prefix' => [true, false, '<b onclick="before()">Before</b>&lt;script&gt;alert(1)&lt;/script&gt;&amp;&lt;i&gt;After &quot;&lt;/i&gt;'],
        'trusted suffix' => [false, true, '&lt;b&gt;Before &amp;&lt;/b&gt;&lt;script&gt;alert(1)&lt;/script&gt;&amp;<i onclick="after()">After</i>'],
        'both trusted' => [true, true, '<b onclick="before()">Before</b>&lt;script&gt;alert(1)&lt;/script&gt;&amp;<i onclick="after()">After</i>'],
    ]);

    it('applies character then word limits before escaping and adding trusted affixes', function (int $limit, string $expected): void {
        $entry = TextEntry::make('content')
            ->container(Schema::make())
            ->formatStateUsing(static fn (): string => 'alpha beta gamma delta')
            ->limit(static fn (): int => $limit, end: static fn (): string => '<cut>')
            ->words(static fn (): int => 1, end: static fn (): string => '<word>')
            ->prefix(new HtmlString('<i>Before</i>'))
            ->suffix('<tail>');

        expect($entry->formatState('Original')->toHtml())->toBe($expected);
    })->with([
        'character limit inside first word' => [3, '<i>Before</i>alp&lt;cut&gt;&lt;tail&gt;'],
        'word limit after character limit' => [8, '<i>Before</i>alpha&lt;word&gt;&lt;tail&gt;'],
    ]);

    it('escapes plain state without double escaping trusted content across render paths', function (string $path): void {
        $entry = TextEntry::make('content')
            ->container(Schema::make(Livewire::make()))
            ->state($path === 'single' ? '<script>alert(1)</script>&' : ['<script>alert(1)</script>&', new HtmlString('<em>Trusted second</em>')])
            ->prefix('<before>')
            ->suffix('<after>');

        match ($path) {
            'badges' => $entry->badge(),
            'bullets' => $entry->bulleted(),
            'expandable' => $entry->listWithLineBreaks()->limitList(1)->expandableLimitedList(),
            'linked' => $entry->url('https://example.com'),
            default => null,
        };

        $html = $entry->toHtml();

        expect($html)
            ->toContain('&lt;before&gt;&lt;script&gt;alert(1)&lt;/script&gt;&amp;&lt;after&gt;')
            ->not->toContain('<script>', '<before>', '<after>');

        if ($path !== 'single') {
            expect($html)->toContain('&lt;before&gt;<em>Trusted second</em>&lt;after&gt;');
        }
    })->with(['single', 'collapsed', 'badges', 'bullets', 'expandable', 'linked']);
});

describe('rendering', function (): void {
    it('can render with `badge()`', function (): void {
        livewire(RenderTextEntryWithBadge::class)->assertSuccessful();
    });

    it('can render with `badge()` set via `Closure`', function (): void {
        livewire(RenderTextEntryWithClosureBadge::class)->assertSuccessful();
    });

    it('can render with `badge(false)` undone', function (): void {
        livewire(RenderTextEntryWithBadgeFalse::class)->assertSuccessful();
    });

    it('can render with `bulleted()`', function (): void {
        livewire(RenderTextEntryWithBulleted::class)->assertSuccessful();
    });

    it('can render with `listWithLineBreaks()`', function (): void {
        livewire(RenderTextEntryWithListWithLineBreaks::class)->assertSuccessful();
    });

    it('can render with `limitList()`', function (): void {
        livewire(RenderTextEntryWithLimitList::class)->assertSuccessful();
    });

    it('can render with `limitList()` set via `Closure`', function (): void {
        livewire(RenderTextEntryWithClosureLimitList::class)->assertSuccessful();
    });

    it('can render with `limitList()` default', function (): void {
        livewire(RenderTextEntryWithDefaultLimitList::class)->assertSuccessful();
    });

    it('can render with `size()` enum', function (): void {
        livewire(RenderTextEntryWithSizeEnum::class)->assertSuccessful();
    });

    it('can render with `size()` set via `Closure`', function (): void {
        livewire(RenderTextEntryWithClosureSize::class)->assertSuccessful();
    });

    it('can render with `size()` string "base"', function (): void {
        livewire(RenderTextEntryWithSizeBase::class)->assertSuccessful();
    });

    it('can render with `size()` string enum value', function (): void {
        livewire(RenderTextEntryWithSizeString::class)->assertSuccessful();
    });

    it('can render with `expandableLimitedList()`', function (): void {
        livewire(RenderTextEntryWithExpandableLimitedList::class)->assertSuccessful();
    });

    it('can render with `expandableLimitedList()` set via `Closure`', function (): void {
        livewire(RenderTextEntryWithClosureExpandableLimitedList::class)->assertSuccessful();
    });
});

it('renders rich-mode enum labels without changing plain-mode label escaping in light and dark modes', function (): void {
    Artisan::call('filament:assets');

    retry(10, function (): void {
        Post::factory()->create();

        $this->actingAs(User::factory()->create());

        foreach ([false, true] as $isDarkMode) {
            $page = visit('/infolist-entries-browser-test');

            if ($isDarkMode) {
                $page = $page->inDarkMode();
            }

            $page
                ->assertNoSmoke()
                ->assertScript('document.querySelector(\'[data-testid="enum-label"]\').textContent.trim()', 'User Management')
                ->assertScript('document.querySelector(\'[data-testid="trusted-label"]\').textContent.trim()', 'Label: <strong>Alpha beta</strong> (label)')
                ->assertScript('document.querySelector(\'[data-testid="trusted-label"] strong\') === null')
                ->assertNoAccessibilityIssues();
        }
    });
});

class TestComponentWithTextEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'name' => 'Test Name',
            ])
            ->components([
                TextEntry::make('name'),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

class TestComponentWithFormattedTextEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'message' => 'hello world',
            ])
            ->components([
                TextEntry::make('message')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

class TestComponentWithArrayStateTextEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'data' => 'placeholder',
            ])
            ->components([
                TextEntry::make('data')
                    ->formatStateUsing(fn (): array => ['key' => 'value']),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

class TestComponentWithMultipleTextEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'tags' => ['Tag 1', 'Tag 2', 'Tag 3'],
            ])
            ->components([
                TextEntry::make('tags'),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

class TestComponentWithProseTextEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'description' => '<p>Hello world</p>',
            ])
            ->components([
                TextEntry::make('description')->prose(),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

class RenderTextEntryWithBadge extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['name' => 'Test'])->components([TextEntry::make('name')->badge()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithClosureBadge extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['name' => 'Test'])->components([TextEntry::make('name')->badge(static fn (): bool => true)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithBadgeFalse extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['name' => 'Test'])->components([TextEntry::make('name')->badge()->badge(false)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithBulleted extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['tags' => ['A', 'B']])->components([TextEntry::make('tags')->bulleted()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithListWithLineBreaks extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['tags' => ['A', 'B']])->components([TextEntry::make('tags')->listWithLineBreaks()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithLimitList extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['tags' => ['A', 'B', 'C', 'D']])->components([TextEntry::make('tags')->limitList(2)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithClosureLimitList extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['tags' => ['A', 'B', 'C']])->components([TextEntry::make('tags')->limitList(static fn (): int => 5)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithDefaultLimitList extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['tags' => ['A', 'B', 'C', 'D']])->components([TextEntry::make('tags')->limitList()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithSizeEnum extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['name' => 'Test'])->components([TextEntry::make('name')->size(TextSize::Large)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithClosureSize extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['name' => 'Test'])->components([TextEntry::make('name')->size(static fn (): TextSize => TextSize::Large)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithSizeBase extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['name' => 'Test'])->components([TextEntry::make('name')->size('base')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithSizeString extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['name' => 'Test'])->components([TextEntry::make('name')->size('lg')]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithExpandableLimitedList extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['tags' => ['A', 'B', 'C', 'D']])->components([
            TextEntry::make('tags')->listWithLineBreaks()->limitList(2)->expandableLimitedList(),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTextEntryWithClosureExpandableLimitedList extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['tags' => ['A', 'B', 'C', 'D']])->components([
            TextEntry::make('tags')->listWithLineBreaks()->limitList(2)->expandableLimitedList(static fn (): bool => true),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
