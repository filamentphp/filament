<?php

namespace Filament\Tests\Tables\Columns;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\IconSize;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can set `boolean()` and get with `isBoolean()`', function (): void {
    expect(IconColumn::make('active')->boolean()->isBoolean())->toBeTrue();
});

it('can set `size()` and get with `getSize()`', function (): void {
    expect(IconColumn::make('active')->size(IconSize::Small)->getSize(null))->toBe(IconSize::Small);
});

it('defaults `getSize()` to `null` when not set', function (): void {
    expect(IconColumn::make('active')->getSize(null))->toBeNull();
});

it('can set `falseColor()` and get with `getFalseColor()`', function (): void {
    expect(IconColumn::make('active')->falseColor('warning')->getFalseColor())->toBe('warning');
});

it('defaults `getFalseColor()` to `danger`', function (): void {
    expect(IconColumn::make('active')->boolean()->getFalseColor())->toBe('danger');
});

it('can set `falseIcon()` and get with `getFalseIcon()`', function (): void {
    expect(IconColumn::make('active')->falseIcon('heroicon-o-x-mark')->getFalseIcon())->toBe('heroicon-o-x-mark');
});

it('can set `trueColor()` and get with `getTrueColor()`', function (): void {
    expect(IconColumn::make('active')->trueColor('info')->getTrueColor())->toBe('info');
});

it('defaults `getTrueColor()` to `success`', function (): void {
    expect(IconColumn::make('active')->boolean()->getTrueColor())->toBe('success');
});

it('can set `trueIcon()` and get with `getTrueIcon()`', function (): void {
    expect(IconColumn::make('active')->trueIcon('heroicon-o-check')->getTrueIcon())->toBe('heroicon-o-check');
});

it('can set `listWithLineBreaks()` and get with `isListWithLineBreaks()`', function (): void {
    expect(IconColumn::make('active')->listWithLineBreaks()->isListWithLineBreaks())->toBeTrue();
});

it('defaults `isListWithLineBreaks()` to `false`', function (): void {
    expect(IconColumn::make('active')->isListWithLineBreaks())->toBeFalse();
});

it('can render', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithIconColumn::class)
        ->assertSuccessful()
        ->assertCanRenderTableColumn('is_published');
});

it('can display true state', function (): void {
    Post::factory()->create(['is_published' => true]);

    livewire(TestTableWithIconColumn::class)
        ->assertSuccessful();
});

it('can display false state', function (): void {
    Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithIconColumn::class)
        ->assertSuccessful();
});

it('can undo `boolean()` with `false`', function (): void {
    expect(IconColumn::make('active')->boolean()->boolean(false)->isBoolean())->toBeFalse();
});

it('can set `boolean()` with a `Closure`', function (): void {
    expect(IconColumn::make('active')->boolean(static fn (): bool => true)->isBoolean())->toBeTrue();
});

it('can set `true()` with combined icon and color', function (): void {
    $column = IconColumn::make('active')
        ->true('heroicon-o-star', 'warning');

    expect($column->getTrueIcon())->toBe('heroicon-o-star');
    expect($column->getTrueColor())->toBe('warning');
});

it('can set `false()` with combined icon and color', function (): void {
    $column = IconColumn::make('active')
        ->false('heroicon-o-x-circle', 'gray');

    expect($column->getFalseIcon())->toBe('heroicon-o-x-circle');
    expect($column->getFalseColor())->toBe('gray');
});

it('can resolve `getIcon()` for true state in boolean mode', function (): void {
    $column = IconColumn::make('active')
        ->boolean();

    $icon = $column->getIcon(true);

    expect($icon)->not->toBeNull();
});

it('can resolve `getIcon()` for false state in boolean mode', function (): void {
    $column = IconColumn::make('active')
        ->boolean();

    $icon = $column->getIcon(false);

    expect($icon)->not->toBeNull();
});

it('can set `size()` with a `Closure`', function (): void {
    expect(IconColumn::make('active')->size(static fn (): IconSize => IconSize::Large)->getSize(null))->toBe(IconSize::Large);
});

it('can set `size()` with a string enum value', function (): void {
    expect(IconColumn::make('active')->size('lg')->getSize(null))->toBe(IconSize::Large);
});

it('returns fluent `$this` from `options()`', function (): void {
    $column = IconColumn::make('active');

    $result = $column->options(['active' => 'heroicon-o-check', 'inactive' => 'heroicon-o-x-mark']);

    expect($result)->toBe($column);
});

describe('rendering', function (): void {
    it('can render with `size()`', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithSize::class)->assertSuccessful();
    });

    it('can render with `size()` set via `Closure`', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithClosureSize::class)->assertSuccessful();
    });

    it('can render with `size()` string enum value', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithStringSize::class)->assertSuccessful();
    });

    it('can render with `falseColor()`', function (): void {
        Post::factory()->create(['is_published' => false]);

        livewire(RenderIconColumnWithFalseColor::class)->assertSuccessful();
    });

    it('can render with `trueColor()`', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithTrueColor::class)->assertSuccessful();
    });

    it('can render with `falseIcon()`', function (): void {
        Post::factory()->create(['is_published' => false]);

        livewire(RenderIconColumnWithFalseIcon::class)->assertSuccessful();
    });

    it('can render with `trueIcon()`', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithTrueIcon::class)->assertSuccessful();
    });

    it('can render with `listWithLineBreaks()`', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithListWithLineBreaks::class)->assertSuccessful();
    });

    it('can render with `boolean()` set via `Closure`', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithClosureBoolean::class)->assertSuccessful();
    });

    it('can render with `true()` combined icon and color', function (): void {
        Post::factory()->create(['is_published' => true]);

        livewire(RenderIconColumnWithTrueCombined::class)->assertSuccessful();
    });

    it('can render with `false()` combined icon and color', function (): void {
        Post::factory()->create(['is_published' => false]);

        livewire(RenderIconColumnWithFalseCombined::class)->assertSuccessful();
    });
});

class TestTableWithIconColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                IconColumn::make('is_published')
                    ->boolean(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithSize extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->size(IconSize::Small),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithClosureSize extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->size(static fn (): IconSize => IconSize::Large),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithStringSize extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->size('lg'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithFalseColor extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->falseColor('warning'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithTrueColor extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->trueColor('info'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithFalseIcon extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->falseIcon('heroicon-o-x-mark'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithTrueIcon extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->trueIcon('heroicon-o-check'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithListWithLineBreaks extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->listWithLineBreaks(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithClosureBoolean extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean(static fn (): bool => true),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithTrueCombined extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->true('heroicon-o-star', 'warning'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderIconColumnWithFalseCombined extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            IconColumn::make('is_published')->boolean()->false('heroicon-o-x-circle', 'gray'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
