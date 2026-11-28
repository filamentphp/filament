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
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\ColumnsBrowserTest;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component as LivewireComponent;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('collapsing', function (): void {
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

});

describe('schema and mounting', function (): void {
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

describe('layout visibility', function (): void {
    it('hides layout controls while allowing visible rows to be edited and called', function (): void {
        Artisan::call('filament:assets');
        $this->actingAs(User::factory()->create());
        $hidden = Post::factory()->create(['title' => 'Hidden', 'is_published' => false]);
        $visible = Post::factory()->create(['title' => 'Visible', 'content' => 'Publish', 'is_published' => true]);

        $page = visit('/columns-browser-test?hiddenLayout=record');
        $input = '[data-testid="layout-title-' . $visible->getKey() . '"] input:not([type="hidden"])';
        $page->assertMissing('[data-testid="layout-title-' . $hidden->getKey() . '"]')
            ->assertMissing('[data-testid="layout-action-' . $hidden->getKey() . '"]')
            ->assertValue($input, 'Visible')
            ->fill($input, 'Updated')
            ->keys($input, 'Tab')
            ->assertValue($input, 'Updated')
            ->assertAttributeMissing($input, 'aria-busy')
            ->click('[data-testid="layout-action-' . $visible->getKey() . '"]')
            ->assertSeeIn('[data-testid="layout-action-' . $visible->getKey() . '"]', 'Action called')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        expect($visible->fresh())->title->toBe('Updated')->content->toBe('Action called');
        expect($hidden->fresh())->title->toBe('Hidden')->content->not->toBe('Action called');

        $page->inDarkMode()->assertNoAccessibilityIssues();
    });

    it('inherits static visibility through nested layouts without changing responsive visibility', function (): void {
        $column = TextColumn::make('title');
        $layout = Stack::make([Split::make([$column])])->hidden();
        $layout->getColumns();

        expect($column->isHidden())->toBeTrue()
            ->and($column->renderInLayout())->toBeNull();

        $layout->hidden(false)->visible(false);
        expect($column->isHidden())->toBeTrue();

        $layout->visible()->hiddenFrom('md');
        expect($column->isVisible())->toBeTrue();

        $column->layout(null);
        expect($column->isVisible())->toBeTrue();
    });

    it('uses the child record rather than a previously rendered layout record', function (): void {
        $column = TextColumn::make('title');
        $layout = Stack::make([Split::make([$column])])
            ->hidden(static fn (?Post $record): bool => $record !== null && (! $record->is_published));
        $layout->getColumns();
        $published = Post::factory()->make(['is_published' => true]);
        $draft = Post::factory()->make(['is_published' => false]);

        $layout->record($published);
        expect($column->record($draft)->isHidden())->toBeTrue();

        $layout->record($draft);
        expect($column->record($published)->isVisible())->toBeTrue();
    });

    it('honors layout visibility in direct column calls after binding the target record', function (bool $recordDependent): void {
        $hidden = Post::factory()->create(['title' => 'Hidden', 'is_published' => false]);
        $visible = Post::factory()->create(['title' => 'Visible', 'is_published' => true]);

        $component = livewire(ColumnsBrowserTest::class, ['hiddenLayout' => $recordDependent ? 'record' : 'static']);

        $component->call('updateTableColumnState', 'title', (string) $hidden->getKey(), 'Changed')
            ->assertReturned(['error' => __('filament-tables::table.columns.editable.unavailable')])
            ->call('callTableColumnAction', 'content', (string) $hidden->getKey())
            ->call('callTableColumnMethod', 'rating', (string) $hidden->getKey(), 'getOptionsForJs')
            ->assertReturned(null);

        expect($hidden->fresh())->title->toBe('Hidden')->content->not->toBe('Action called');

        if ($recordDependent) {
            $component->call('updateTableColumnState', 'title', (string) $visible->getKey(), 'Changed')
                ->call('callTableColumnAction', 'content', (string) $visible->getKey())
                ->call('callTableColumnMethod', 'rating', (string) $visible->getKey(), 'getOptionsForJs')
                ->assertReturned([['label' => 'Low', 'value' => '1', 'isDisabled' => false]]);

            expect($visible->fresh())->title->toBe('Changed')->content->toBe('Action called');
        }
    })->with([false, true]);

    it('excludes statically hidden children from search and sort but not record-dependent children', function (): void {
        $first = Post::factory()->create(['title' => 'Zulu', 'is_published' => false]);
        $second = Post::factory()->create(['title' => 'Alpha', 'is_published' => true]);

        livewire(ColumnsBrowserTest::class, ['hiddenLayout' => 'static'])
            ->sortTable('title')
            ->assertCanSeeTableRecords([$first, $second], inOrder: true)
            ->searchTable('Alpha')
            ->assertCanNotSeeTableRecords([$first, $second])
            ->searchTable('')
            ->searchTableColumns(['title' => 'Alpha'])
            ->assertCanSeeTableRecords([$first, $second]);

        $component = livewire(ColumnsBrowserTest::class, ['hiddenLayout' => 'record']);
        $component->instance()->getTable()->getColumn('title')->record($first);
        $component->sortTable('title')
            ->assertCanSeeTableRecords([$second, $first], inOrder: true)
            ->searchTable('Alpha')
            ->assertCanSeeTableRecords([$second])
            ->assertCanNotSeeTableRecords([$first]);
    });

    it('uses the null-record visibility default for table-wide capabilities without changing row visibility', function (bool $isHiddenWithoutRecord): void {
        $table = livewire(ColumnsBrowserTest::class, ['hiddenLayout' => 'record'])->instance()->getTable();
        $column = $table->getColumn('title');
        $layout = $column->getLayout()->getLayout();
        $record = Post::factory()->make(['is_published' => false]);
        $layout->hidden(static fn (?Post $record): bool => $record ? (! $record->is_published) : $isHiddenWithoutRecord)
            ->record($record);
        $table->flushCachedVisibleColumns();

        expect($column->record($record)->isHidden())->toBeTrue()
            ->and($table->getSortableVisibleColumn('title'))->toBe($isHiddenWithoutRecord ? null : $column)
            ->and($table->isSearchableByColumn())->toBe(! $isHiddenWithoutRecord)
            ->and(array_key_exists('title', $table->getVisibleColumns()))->toBe(! $isHiddenWithoutRecord)
            ->and($column->getRecord())->toBe($record)
            ->and($layout->getRecord())->toBe($record);
    })->with([false, true]);

    it('preserves table and Livewire injection for table-wide layout callbacks', function (): void {
        $component = livewire(ColumnsBrowserTest::class, ['hiddenLayout' => 'record'])->instance();
        $table = $component->getTable();
        $table->getColumn('title')->getLayout()
            ->hidden(static fn (Table $table, Tables\Contracts\HasTable $livewire): bool => $table === $livewire->getTable());

        expect($table->getSortableVisibleColumn('title'))->toBeNull()
            ->and($table->isSearchableByColumn())->toBeFalse();
    });

    it('does not reuse a rendered row loop for table-wide nested layout visibility', function (): void {
        Post::factory()->create(['title' => 'First visible']);
        Post::factory()->create(['title' => 'Last hidden']);

        $component = livewire(ColumnsBrowserTest::class, ['hiddenLayout' => 'rowLoop'])
            ->assertSee('First visible')
            ->assertDontSee('Last hidden');
        $table = $component->instance()->getTable();

        expect($table->getSortableVisibleColumn('title'))->not->toBeNull()
            ->and($table->isSearchableByColumn())->toBeTrue();
    });

    it('does not reuse a rendered row loop for direct nested column calls', function (string $method, string $column, array $arguments, bool $isHiddenWithoutRowLoop): void {
        $first = Post::factory()->create(['title' => 'First visible']);
        $last = Post::factory()->create(['title' => 'Last hidden', 'content' => 'Unchanged']);

        $component = livewire(ColumnsBrowserTest::class, ['hiddenLayout' => $isHiddenWithoutRowLoop ? 'rowLoopHiddenWithoutLoop' : 'rowLoop'])
            ->assertSee('First visible')
            ->assertDontSee('Last hidden')
            ->instance();

        $response = $component->{$method}($column, (string) $last->getKey(), ...$arguments);

        match ($method) {
            'updateTableColumnState' => expect($last->fresh()->title)->toBe($isHiddenWithoutRowLoop ? 'Last hidden' : 'Changed'),
            'callTableColumnAction' => expect($last->fresh()->content)->toBe($isHiddenWithoutRowLoop ? 'Unchanged' : 'Action called'),
            'callTableColumnMethod' => expect($response)->toBe($isHiddenWithoutRowLoop ? null : [['label' => 'Low', 'value' => '1', 'isDisabled' => false]]),
        };

        expect($first->fresh()->title)->toBe('First visible');
    })->with([
        ['updateTableColumnState', 'title', ['Changed']],
        ['callTableColumnAction', 'content', []],
        ['callTableColumnMethod', 'rating', ['getOptionsForJs']],
    ])->with([false, true]);
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
