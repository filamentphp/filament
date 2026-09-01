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
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Stringable;

use function Filament\Tests\livewire;

uses(TestCase::class);

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

    it('renders `date()` formatted state', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithDate::class)
            ->assertSuccessful()
            ->assertSeeHtml('2025-06-15');
    });

    it('wraps the state in an `<a>` when `url()` is set', function (): void {
        Post::factory()->create();

        livewire(RenderTextColumnWithUrl::class)
            ->assertSuccessful()
            ->assertSeeHtml('href="https://example.test/foo"');
    });

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

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->url(static fn (): string => 'https://example.test/foo'),
        ]);
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
