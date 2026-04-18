<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Component;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;
use Livewire\Component as LivewireComponent;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('`Component` base class', function (): void {
    it('defaults `isCollapsible()` to `false`', function (): void {
        $stack = Stack::make([]);

        expect($stack->isCollapsible())->toBeFalse();
    });

    it('can set `collapsible()`', function (): void {
        $stack = Stack::make([])
            ->collapsible();

        expect($stack->isCollapsible())->toBeTrue();
    });

    it('can set `collapsible()` to `false`', function (): void {
        $stack = Stack::make([])
            ->collapsible()
            ->collapsible(false);

        expect($stack->isCollapsible())->toBeFalse();
    });

    it('defaults `isCollapsed()` to `true`', function (): void {
        $stack = Stack::make([]);

        expect($stack->isCollapsed())->toBeTrue();
    });

    it('calling `collapsed()` implies `collapsible()`', function (): void {
        $stack = Stack::make([])
            ->collapsed();

        expect($stack->isCollapsible())->toBeTrue();
        expect($stack->isCollapsed())->toBeTrue();
    });

    it('can set `collapsed()` to `false`', function (): void {
        $stack = Stack::make([])
            ->collapsed(false);

        expect($stack->isCollapsed())->toBeFalse();
    });

    it('can set `collapsed()` with a `Closure`', function (): void {
        $stack = Stack::make([])
            ->collapsed(static fn (): bool => false);

        expect($stack->isCollapsed())->toBeFalse();
    });

    it('returns fluent `$this` from `schema()`', function (): void {
        $stack = Stack::make([]);

        expect($stack->schema([]))->toBe($stack);
    });

    it('returns fluent `$this` from `components()`', function (): void {
        $stack = Stack::make([]);

        expect($stack->components([]))->toBe($stack);
    });

    it('throws `LogicException` from `getTable()` when not mounted', function (): void {
        $stack = Stack::make([]);

        expect(static fn () => $stack->getTable())
            ->toThrow(LogicException::class);
    });
});

describe('`Grid` layout', function (): void {
    it('can be constructed with a default column count', function (): void {
        $grid = Grid::make();

        expect($grid->getGridColumns())->toHaveKey('lg', 2);
    });

    it('can be constructed with a custom column count', function (): void {
        $grid = Grid::make(3);

        expect($grid->getGridColumns())->toHaveKey('lg', 3);
    });

    it('can be constructed with a responsive array', function (): void {
        $grid = Grid::make(['sm' => 1, 'lg' => 3]);

        expect($grid->getGridColumns())->toHaveKey('sm', 1);
        expect($grid->getGridColumns())->toHaveKey('lg', 3);
    });

    it('can merge columns with multiple `columns()` calls', function (): void {
        $grid = Grid::make(2)
            ->columns(['sm' => 1]);

        $columns = $grid->getGridColumns();

        expect($columns)->toHaveKey('lg', 2);
        expect($columns)->toHaveKey('sm', 1);
    });
});

describe('`Panel` layout', function (): void {
    it('can be constructed with a schema', function (): void {
        $panel = Panel::make([]);

        expect($panel)->toBeInstanceOf(Panel::class);
        expect($panel)->toBeInstanceOf(Component::class);
    });

    it('can accept columns in schema', function (): void {
        $panel = Panel::make([
            TextColumn::make('title'),
        ]);

        expect($panel)->toBeInstanceOf(Panel::class);
    });
});

describe('`Split` layout', function (): void {
    it('can be constructed with a schema', function (): void {
        $split = Split::make([]);

        expect($split)->toBeInstanceOf(Split::class);
        expect($split)->toBeInstanceOf(Component::class);
    });

    it('returns `null` for `getFromBreakpoint()` by default', function (): void {
        $split = Split::make([]);

        expect($split->getFromBreakpoint())->toBeNull();
    });

    it('can set `from()` breakpoint', function (): void {
        $split = Split::make([])
            ->from('md');

        expect($split->getFromBreakpoint())->toBe('md');
    });
});

describe('`Stack` layout', function (): void {
    it('can be constructed with a schema', function (): void {
        $stack = Stack::make([]);

        expect($stack)->toBeInstanceOf(Stack::class);
        expect($stack)->toBeInstanceOf(Component::class);
    });

    it('returns `null` for `getAlignment()` by default', function (): void {
        $stack = Stack::make([]);

        expect($stack->getAlignment())->toBeNull();
    });

    it('can set `alignment()`', function (): void {
        $stack = Stack::make([])
            ->alignment(Alignment::Center);

        expect($stack->getAlignment())->toBe(Alignment::Center);
    });

    it('returns `null` for `getSpace()` by default', function (): void {
        $stack = Stack::make([]);

        expect($stack->getSpace())->toBeNull();
    });

    it('can set `space()`', function (): void {
        $stack = Stack::make([])
            ->space(2);

        expect($stack->getSpace())->toBe(2);
    });
});

describe('`View` layout', function (): void {
    it('can be constructed with a view name', function (): void {
        $view = View::make('simple-component');

        expect($view)->toBeInstanceOf(View::class);
        expect($view)->toBeInstanceOf(Component::class);
    });
});

describe('rendering', function (): void {
    it('can render `Stack` layout with `alignment()`', function (): void {
        Post::factory()->create();
        livewire(RenderTableWithStackAlignment::class)->assertSuccessful();
    });

    it('can render `Stack` layout with `space()`', function (): void {
        Post::factory()->create();
        livewire(RenderTableWithStackSpace::class)->assertSuccessful();
    });

    it('can render `Stack` layout with `collapsible()`', function (): void {
        Post::factory()->create();
        livewire(RenderTableWithStackCollapsible::class)->assertSuccessful();
    });

    it('can render `Stack` layout with `collapsed()`', function (): void {
        Post::factory()->create();
        livewire(RenderTableWithStackCollapsed::class)->assertSuccessful();
    });

    it('can render `Grid` layout with custom columns', function (): void {
        Post::factory()->create();
        livewire(RenderTableWithGridColumns::class)->assertSuccessful();
    });

    it('can render `Grid` layout with responsive columns', function (): void {
        Post::factory()->create();
        livewire(RenderTableWithGridResponsive::class)->assertSuccessful();
    });

    it('can render `Split` layout', function (): void {
        Post::factory()->create();
        livewire(RenderTableWithSplit::class)->assertSuccessful();
    });

    it('can render `Split` layout with `from()` breakpoint', function (): void {
        Post::factory()->create();
        livewire(RenderTableWithSplitFrom::class)->assertSuccessful();
    });

    it('can render `Panel` layout', function (): void {
        Post::factory()->create();
        livewire(RenderTableWithPanel::class)->assertSuccessful();
    });
});

class RenderTableWithStackAlignment extends LivewireComponent implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Stack::make([TextColumn::make('title')])->alignment(Alignment::Center),
        ]);
    }

    public function render(): Illuminate\Contracts\View\View
    {
        return view('livewire.table');
    }
}

class RenderTableWithStackSpace extends LivewireComponent implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Stack::make([TextColumn::make('title')])->space(2),
        ]);
    }

    public function render(): Illuminate\Contracts\View\View
    {
        return view('livewire.table');
    }
}

class RenderTableWithStackCollapsible extends LivewireComponent implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Stack::make([TextColumn::make('title')])->collapsible(),
        ]);
    }

    public function render(): Illuminate\Contracts\View\View
    {
        return view('livewire.table');
    }
}

class RenderTableWithStackCollapsed extends LivewireComponent implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Stack::make([TextColumn::make('title')])->collapsed(),
        ]);
    }

    public function render(): Illuminate\Contracts\View\View
    {
        return view('livewire.table');
    }
}

class RenderTableWithGridColumns extends LivewireComponent implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Grid::make(3)->schema([TextColumn::make('title'), TextColumn::make('content')]),
        ]);
    }

    public function render(): Illuminate\Contracts\View\View
    {
        return view('livewire.table');
    }
}

class RenderTableWithGridResponsive extends LivewireComponent implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Grid::make(['sm' => 1, 'lg' => 3])->schema([TextColumn::make('title')]),
        ]);
    }

    public function render(): Illuminate\Contracts\View\View
    {
        return view('livewire.table');
    }
}

class RenderTableWithSplit extends LivewireComponent implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Split::make([TextColumn::make('title'), TextColumn::make('content')]),
        ]);
    }

    public function render(): Illuminate\Contracts\View\View
    {
        return view('livewire.table');
    }
}

class RenderTableWithSplitFrom extends LivewireComponent implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Split::make([TextColumn::make('title'), TextColumn::make('content')])->from('md'),
        ]);
    }

    public function render(): Illuminate\Contracts\View\View
    {
        return view('livewire.table');
    }
}

class RenderTableWithPanel extends LivewireComponent implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Panel::make([TextColumn::make('title')]),
        ]);
    }

    public function render(): Illuminate\Contracts\View\View
    {
        return view('livewire.table');
    }
}
