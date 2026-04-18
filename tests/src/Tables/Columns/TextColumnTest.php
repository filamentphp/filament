<?php

namespace Filament\Tests\Tables\Columns;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

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
