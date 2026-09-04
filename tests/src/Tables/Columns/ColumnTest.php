<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can be constructed with `make()` and a name', function (): void {
    $column = TextColumn::make('title');

    expect($column)->toBeInstanceOf(Column::class);
    expect($column->getName())->toBe('title');
});

it('throws `LogicException` from `make()` when name is blank', function (): void {
    expect(static fn () => TextColumn::make(''))
        ->toThrow(LogicException::class, 'must have a unique name');
});

it('returns `null` for `getDefaultName()`', function (): void {
    expect(Column::getDefaultName())->toBeNull();
});

it('throws `LogicException` from `getTable()` when not mounted', function (): void {
    $column = TextColumn::make('title');

    expect(static fn () => $column->getTable())
        ->toThrow(LogicException::class, 'is not mounted to a table');
});

describe('label', function (): void {
    it('generates a label from the column name', function (): void {
        $column = TextColumn::make('first_name');

        expect($column->getLabel())->toBe('First name');
    });

    it('can set a custom label', function (): void {
        $column = TextColumn::make('title')
            ->label('Custom Title');

        expect($column->getLabel())->toBe('Custom Title');
    });
});

describe('alignment', function (): void {
    it('returns `null` for `getAlignment()` by default', function (): void {
        $column = TextColumn::make('title');

        expect($column->getAlignment())->toBeNull();
    });

    it('can set `alignment()`', function (): void {
        $column = TextColumn::make('title')
            ->alignment(Alignment::Center);

        expect($column->getAlignment())->toBe(Alignment::Center);
    });
});

describe('sortable', function (): void {
    it('is not sortable by default', function (): void {
        $column = TextColumn::make('title');

        expect($column->isSortable())->toBeFalse();
    });

    it('can be made sortable', function (): void {
        $column = TextColumn::make('title')
            ->sortable();

        expect($column->isSortable())->toBeTrue();
    });

    it('uses `orderByRelationAggregate()` provided by an unsupported Eloquent query builder', function (): void {
        $modifyRelationQueryUsing = static fn (EloquentBuilder $query): EloquentBuilder => $query->where('is_approved', true);
        $configurations = [
            [TextColumn::make('comments_avg_rating')->avg('comments', 'rating'), ['comments', 'rating', 'avg', 'desc', null]],
            [TextColumn::make('comments_count')->counts('comments'), ['comments', '*', 'count', 'desc', null]],
            [TextColumn::make('comments_exists')->exists('comments'), ['comments', '*', 'exists', 'desc', null]],
            [TextColumn::make('comments_max_rating')->max('comments', 'rating'), ['comments', 'rating', 'max', 'desc', null]],
            [TextColumn::make('comments_min_rating')->min('comments', 'rating'), ['comments', 'rating', 'min', 'desc', null]],
            [TextColumn::make('comments_sum_rating')->sum('comments', 'rating'), ['comments', 'rating', 'sum', 'desc', null]],
            [TextColumn::make('approved_comments_count')->counts(['comments as approved_comments_count' => $modifyRelationQueryUsing]), ['comments', '*', 'count', 'desc', $modifyRelationQueryUsing]],
            [TextColumn::make('comments_count')->counts(['comments', 'tags']), ['comments', '*', 'count', 'desc', null]],
            [TextColumn::make('approved_comments_count')->counts(['comments as approved_comments_count', 'tags']), ['comments', '*', 'count', 'desc', null]],
            [TextColumn::make('comments_count')->counts(['comments' => $modifyRelationQueryUsing, 'tags']), ['comments', '*', 'count', 'desc', $modifyRelationQueryUsing]],
            [TextColumn::make('comments_count')->avg('comments', 'rating')->counts('comments'), ['comments', '*', 'count', 'desc', null]],
        ];

        foreach ($configurations as [$column, $expectedRelationshipAggregateOrder]) {
            $databaseConnection = Mockery::mock(Connection::class);
            $databaseConnection->shouldReceive('getDriverName')->andReturn('mongodb');

            $query = new class(new QueryBuilder($databaseConnection, new Grammar($databaseConnection), new Processor)) extends EloquentBuilder
            {
                /** @var array{relation: string, aggregateColumn: string | Expression, function: string, direction: string, modifyQueryUsing: Closure | null} | null */
                public ?array $relationshipAggregateOrder = null;

                public function orderByRelationAggregate(string $relation, string | Expression $aggregateColumn, string $function, string $direction, ?Closure $modifyQueryUsing = null): static
                {
                    $this->relationshipAggregateOrder = [
                        'relation' => $relation,
                        'aggregateColumn' => $aggregateColumn,
                        'function' => $function,
                        'direction' => $direction,
                        'modifyQueryUsing' => $modifyQueryUsing,
                    ];

                    return $this;
                }
            };
            $query->setModel(new Post);

            expect($column->sortable()->applySort($query, 'desc'))
                ->toBe($query)
                ->and(array_values($query->relationshipAggregateOrder))
                ->toBe($expectedRelationshipAggregateOrder);
        }
    });

    it('does not use `orderByRelationAggregate()` for a supported database driver', function (): void {
        $query = new class(Post::query()->toBase()) extends EloquentBuilder
        {
            public bool $usedRelationshipAggregateOrder = false;

            public function orderByRelationAggregate(string $relation, string | Expression $aggregateColumn, string $function, string $direction, ?Closure $modifyQueryUsing = null): static
            {
                $this->usedRelationshipAggregateOrder = true;

                return $this;
            }
        };
        $query->setModel(new Post);

        expect(TextColumn::make('comments_count')->counts('comments')->sortable()->applySort($query, 'desc'))
            ->toBe($query)
            ->and($query->usedRelationshipAggregateOrder)
            ->toBeFalse()
            ->and($query->getQuery()->orders[0])
            ->toMatchArray([
                'column' => 'comments_count',
                'direction' => 'desc',
            ]);
    });
});

describe('searchable', function (): void {
    it('is not searchable by default', function (): void {
        $column = TextColumn::make('title');

        expect($column->isSearchable())->toBeFalse();
    });

    it('can be made searchable', function (): void {
        $column = TextColumn::make('title')
            ->searchable();

        expect($column->isSearchable())->toBeTrue();
    });

    it('is not searchable when the `searchable()` closure returns false', function (): void {
        $column = TextColumn::make('title')
            ->searchable(static fn (): bool => false);

        expect($column->isSearchable())->toBeFalse();
    });
});

describe('visibility', function (): void {
    it('is visible by default', function (): void {
        $column = TextColumn::make('title');

        expect($column->isVisible())->toBeTrue();
    });

    it('can be hidden', function (): void {
        $column = TextColumn::make('title')
            ->hidden();

        expect($column->isHidden())->toBeTrue();
    });
});

describe('toggleability', function (): void {
    it('is not toggleable by default', function (): void {
        $column = TextColumn::make('title');

        expect($column->isToggleable())->toBeFalse();
    });

    it('can be made toggleable', function (): void {
        $column = TextColumn::make('title')
            ->toggleable();

        expect($column->isToggleable())->toBeTrue();
    });
});

describe('placeholder', function (): void {
    it('returns `null` for `getPlaceholder()` by default', function (): void {
        $column = TextColumn::make('title');

        expect($column->getPlaceholder())->toBeNull();
    });

    it('can set `placeholder()`', function (): void {
        $column = TextColumn::make('title')
            ->placeholder('N/A');

        expect($column->getPlaceholder())->toBe('N/A');
    });
});

describe('width', function (): void {
    it('returns `null` for `getWidth()` by default', function (): void {
        $column = TextColumn::make('title');

        expect($column->getWidth())->toBeNull();
    });

    it('can set `width()`', function (): void {
        $column = TextColumn::make('title')
            ->width('200px');

        expect($column->getWidth())->toBe('200px');
    });
});

describe('grow', function (): void {
    it('can check `canGrow()` default', function (): void {
        // TextColumn defaults to grow=true; other columns may differ
        $column = TextColumn::make('title');

        expect($column->canGrow())->toBeTrue();
    });

    it('can set `grow()`', function (): void {
        $column = TextColumn::make('title')
            ->grow();

        expect($column->canGrow())->toBeTrue();
    });
});

describe('rendering', function (): void {
    it('can render with custom `label()`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithCustomLabel::class)
            ->assertSuccessful()
            ->assertSeeHtml('Custom Title');
    });

    it('can render with `alignment()`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithAlignment::class)->assertSuccessful();
    });

    it('can render with `placeholder()`', function (): void {
        Post::factory()->create(['content' => null]);
        livewire(RenderColumnWithPlaceholder::class)
            ->assertSuccessful()
            ->assertSeeHtml('N/A');
    });

    it('can render with `width()`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithWidth::class)->assertSuccessful();
    });

    it('can render with `hidden()`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithHidden::class)->assertSuccessful();
    });

    it('can render with `sortable()`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithSortable::class)->assertSuccessful();
    });

    it('can render with `searchable()`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithSearchable::class)->assertSuccessful();
    });

    it('can render with `searchable()` using a closure', function (): void {
        $post = Post::factory()->create();

        livewire(RenderColumnWithSearchableClosure::class)
            ->searchTable($post->title)
            ->assertCanSeeTableRecords([$post]);
    });

    it('can render with `toggleable()`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithToggleable::class)->assertSuccessful();
    });

    it('can render with `grow(false)`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithGrowFalse::class)->assertSuccessful();
    });
});

class RenderColumnWithCustomLabel extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->label('Custom Title'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithAlignment extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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

class RenderColumnWithPlaceholder extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('content')->placeholder('N/A'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithWidth extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->width('200px'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithHidden extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title'),
            TextColumn::make('content')->hidden(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithSortable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->sortable(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithSearchable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->searchable(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithSearchableClosure extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->searchable(static fn (): bool => true),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithToggleable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->toggleable(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithGrowFalse extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->grow(false),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
