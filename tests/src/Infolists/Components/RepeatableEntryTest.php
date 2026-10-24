<?php

namespace Filament\Tests\Infolists\Components;

use ArrayIterator;
use ArrayObject;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Generator;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Livewire\Component;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('returns `null` for `getTableColumns()` by default', function (): void {
    $entry = RepeatableEntry::make('items');
    expect($entry->getTableColumns())->toBeNull();
});

test('`isTable()` returns `false` when no table columns are set', function (): void {
    $entry = RepeatableEntry::make('items');
    expect($entry->isTable())->toBeFalse();
});

it('can set `table()` columns', function (): void {
    $columns = [
        TableColumn::make('Name'),
        TableColumn::make('Email'),
    ];

    $entry = RepeatableEntry::make('items')->table($columns);

    expect($entry->getTableColumns())->toBe($columns);
});

test('`isTable()` returns `true` when table columns are set', function (): void {
    $entry = RepeatableEntry::make('items')->table([TableColumn::make('Name')]);
    expect($entry->isTable())->toBeTrue();
});

it('can set `table()` columns using a `Closure`', function (): void {
    $entry = RepeatableEntry::make('items')
        ->table(static fn (): array => [TableColumn::make('Name')]);

    expect($entry->getTableColumns())->toHaveCount(1);
});

it('can clear `table()` columns with `null`', function (): void {
    $entry = RepeatableEntry::make('items')
        ->table([TableColumn::make('Name')])
        ->table(null);

    expect($entry->isTable())->toBeFalse();
    expect($entry->getTableColumns())->toBeNull();
});

it('returns fluent `$this` from `table()`', function (): void {
    $entry = RepeatableEntry::make('items');

    expect($entry->table([]))->toBe($entry);
});

describe('containment', function (): void {
    it('defaults `isContained()` to `true`', function (): void {
        $entry = RepeatableEntry::make('items');

        expect($entry->isContained())->toBeTrue();
    });

    it('can set `contained()` to `false`', function (): void {
        $entry = RepeatableEntry::make('items')
            ->contained(false);

        expect($entry->isContained())->toBeFalse();
    });

    it('can set `contained()` with a `Closure`', function (): void {
        $entry = RepeatableEntry::make('items')
            ->contained(static fn (): bool => false);

        expect($entry->isContained())->toBeFalse();
    });
});

describe('grid layout', function (): void {
    it('can set `grid()` with int', function (): void {
        $entry = RepeatableEntry::make('items')
            ->grid(3);

        expect($entry->getGridColumns('lg'))->toBe(3);
    });

    it('can set `grid()` with array', function (): void {
        $entry = RepeatableEntry::make('items')
            ->grid(['default' => 1, 'sm' => 2, 'lg' => 3]);

        expect($entry->getGridColumns('default'))->toBe(1);
        expect($entry->getGridColumns('sm'))->toBe(2);
        expect($entry->getGridColumns('lg'))->toBe(3);
    });

    it('can set `grid()` with a `Closure`', function (): void {
        $entry = RepeatableEntry::make('items')
            ->grid(static fn (): int => 4);

        expect($entry->getGridColumns('lg'))->toBe(4);
    });
});

describe('item collections', function (): void {
    it('can read a state `Closure` using `$get()` repeatedly without recursion', function (): void {
        $stateEvaluationCount = 0;
        $livewire = Livewire::make();
        $livewire->data = [
            'results' => [
                'rows' => [
                    ['name' => 'First result'],
                ],
            ],
        ];

        $schema = Schema::make($livewire)
            ->statePath('data')
            ->components([
                $entry = RepeatableEntry::make('results')
                    ->state(function (Get $get) use (&$stateEvaluationCount): array {
                        $stateEvaluationCount++;

                        if ($stateEvaluationCount > 5) {
                            throw new RuntimeException('The state closure was evaluated recursively.');
                        }

                        return $get('results.rows');
                    })
                    ->schema([
                        TextEntry::make('name'),
                    ]),
            ]);

        $schema->getComponents();

        expect($entry->getItems())
            ->toHaveCount(1)
            ->and($entry->getItems())
            ->toHaveCount(1);
    });

    it('builds one schema per item keyed by the item key', function (): void {
        $schema = Schema::make(Livewire::make())
            ->state([
                'items' => [
                    ['name' => 'First'],
                    ['name' => 'Second'],
                    ['name' => 'Third'],
                ],
            ])
            ->components([
                RepeatableEntry::make('items')
                    ->schema([
                        TextEntry::make('name'),
                    ]),
            ]);

        $entry = $schema->getComponents()[0];

        $items = $entry->getItems();

        expect($items)->toHaveCount(3)
            ->and(array_keys($items))->toBe(array_keys($entry->getState()))
            ->and(array_values($items)[0])->toBeInstanceOf(Schema::class);
    });

    it('rebuilds `getItems()` when an object item collection is mutated', function (): void {
        $itemsState = (object) [
            'first' => ['name' => 'First'],
            'second' => ['name' => 'Second'],
        ];
        $schema = Schema::make(Livewire::make())
            ->state(['items' => $itemsState])
            ->components([
                RepeatableEntry::make('items')
                    ->schema([
                        TextEntry::make('name'),
                    ]),
            ]);

        $entry = $schema->getComponents()[0];
        expect($entry->getItems())->toHaveCount(2);

        unset($itemsState->second);

        expect($entry->getItems())
            ->toHaveCount(1)
            ->toHaveKey('first')
            ->not->toHaveKey('second');
    });

    it('builds and rebuilds `getItems()` from an `ArrayObject` item collection', function (): void {
        $itemsState = new ArrayObject([
            'first' => ['name' => 'First'],
            'second' => ['name' => 'Second'],
        ]);
        $schema = Schema::make(Livewire::make())
            ->state(['items' => $itemsState])
            ->components([
                RepeatableEntry::make('items')
                    ->schema([
                        TextEntry::make('name'),
                    ]),
            ]);

        $entry = $schema->getComponents()[0];
        $firstItems = $entry->getItems();

        expect($firstItems)->toHaveCount(2);

        unset($itemsState['second']);

        expect($entry->getItems())
            ->toHaveCount(1)
            ->toHaveKey('first')
            ->not->toHaveKey('second');
    });

    it('reuses a materialized `Generator` item collection', function (): void {
        $itemsState = (static function (): Generator {
            yield 'first' => ['name' => 'First'];
        })();
        $entry = RepeatableEntry::make('items')
            ->state($itemsState)
            ->schema([
                TextEntry::make('name'),
            ]);
        $schema = Schema::make(Livewire::make())
            ->components([
                $source = TextEntry::make('source'),
                $entry,
            ]);

        $schema->getComponents();

        $firstItems = $entry->getItems();
        $firstItems['first']->getComponents();

        expect($entry->getItems())
            ->toHaveCount(1)
            ->toHaveKey('first')
            ->and($source->makeGetUtility()('/items.first.name'))
            ->toBe('First');

        $entry->state((static function (): Generator {
            yield 'second' => ['name' => 'Second'];
        })());

        expect($entry->getItems())
            ->toHaveKey('second')
            ->not->toHaveKey('first');
    });

    it('reads object-backed item values through repeated `$get()` calls', function (): void {
        $itemsState = (object) collect(range(0, 9))
            ->mapWithKeys(static fn (int $itemIndex): array => ["item{$itemIndex}" => ['name' => "Item {$itemIndex}"]])
            ->all();
        $entry = RepeatableEntry::make('items');
        $schema = Schema::make(Livewire::make())
            ->state(['items' => $itemsState])
            ->components([
                $source = TextEntry::make('source'),
                $entry->schema([
                    TextEntry::make('name'),
                ]),
            ]);

        $schema->getComponents();

        foreach ($entry->getItems() as $item) {
            $item->getComponents();
        }

        $get = $source->makeGetUtility();

        foreach (range(0, 9) as $itemIndex) {
            expect($get("/items.item{$itemIndex}.name"))->toBe("Item {$itemIndex}");
        }

        foreach (range(0, 9) as $itemIndex) {
            expect($get("/items.item{$itemIndex}.name"))->toBe("Item {$itemIndex}");
        }
    });

    it('updates `getItems()` to reflect a new item count', function (): void {
        $schema = Schema::make(Livewire::make())
            ->state([
                'items' => [
                    ['name' => 'First'],
                    ['name' => 'Second'],
                ],
            ])
            ->components([
                RepeatableEntry::make('items')
                    ->schema([
                        TextEntry::make('name'),
                    ]),
            ]);

        $entry = $schema->getComponents()[0];

        $firstItems = $entry->getItems();

        expect($firstItems)->toHaveCount(2);

        $schema->state([
            'items' => [
                ['name' => 'First'],
                ['name' => 'Second'],
                ['name' => 'Third'],
            ],
        ]);

        expect($entry->getItems())
            ->toHaveCount(3);
    });
});

describe('processing item limits', function (): void {
    it('allows increasing, decreasing and disabling a global `maxItemsForProcessing()`', function (): void {
        expect(RepeatableEntry::make('items')->getMaxItemsForProcessing())->toBeNull();

        RepeatableEntry::configureUsing(
            static fn (RepeatableEntry $entry) => $entry->maxItemsForProcessing(10),
            during: function (): void {
                expect(RepeatableEntry::make('items')->getMaxItemsForProcessing())->toBe(10);

                foreach ([5, 20, null] as $limit) {
                    $entry = RepeatableEntry::make('items');

                    expect($entry->maxItemsForProcessing($limit))->toBe($entry)
                        ->and($entry->getMaxItemsForProcessing())->toBe($limit);
                }
            },
        );
    });

    it('rejects excessive item counts before evaluating schemas and preserves supported state types', function (mixed $state): void {
        $entry = RepeatableEntry::make('items')
            ->state($state)
            ->maxItemsForProcessing(2)
            ->schema(static fn () => throw new RuntimeException('Item schemas must not be evaluated.'));

        Schema::make(Livewire::make())->components([$entry])->getComponents();

        expect(fn () => $entry->getItems())->toThrow(function (HttpException $exception): void {
            expect($exception->getStatusCode())->toBe(413);
        });

        expect($entry->getState())->toBe($state);

        $entry->maxItemsForProcessing(3)->schema([TextEntry::make('name')]);

        expect(array_keys($entry->getItems()))->toBe(['first', 'second', 'third'])
            ->and($entry->getItems()['third']->getComponents()[0]->getState())->toBe('Third')
            ->and($entry->getItems())->toHaveCount(3);
    })->with([
        'array' => fn (): array => ['first' => ['name' => 'First'], 'second' => ['name' => 'Second'], 'third' => ['name' => 'Third']],
        'collection' => fn (): Collection => collect(['first' => ['name' => 'First'], 'second' => ['name' => 'Second'], 'third' => ['name' => 'Third']]),
        'lazy collection' => fn (): LazyCollection => LazyCollection::make(['first' => ['name' => 'First'], 'second' => ['name' => 'Second'], 'third' => ['name' => 'Third']]),
        'array object' => fn (): ArrayObject => new ArrayObject(['first' => ['name' => 'First'], 'second' => ['name' => 'Second'], 'third' => ['name' => 'Third']]),
        'iterator' => fn (): ArrayIterator => new ArrayIterator(['first' => ['name' => 'First'], 'second' => ['name' => 'Second'], 'third' => ['name' => 'Third']]),
        'generator' => function (): Generator {
            yield 'first' => ['name' => 'First'];
            yield 'second' => ['name' => 'Second'];
            yield 'third' => ['name' => 'Third'];
        },
        'object' => fn (): object => (object) ['first' => ['name' => 'First'], 'second' => ['name' => 'Second'], 'third' => ['name' => 'Third']],
    ]);

    it('rejects item growth after caching and allows disabling the processing limit', function (): void {
        $state = (object) ['first' => ['name' => 'First']];
        $entry = RepeatableEntry::make('items')
            ->state($state)
            ->maxItemsForProcessing(1)
            ->schema([TextEntry::make('name')]);

        Schema::make(Livewire::make())->components([$entry])->getComponents();

        expect($entry->getItems())->toHaveCount(1);

        $state->second = ['name' => 'Second'];

        expect(fn () => $entry->getItems())->toThrow(function (HttpException $exception): void {
            expect($exception->getStatusCode())->toBe(413);
        });

        $entry->maxItemsForProcessing(null);

        expect($entry->getItems())->toHaveCount(2);

        $entry->state([])->maxItemsForProcessing(0);

        expect($entry->getItems())->toBe([]);
    });

    it('applies processing limits when rendering ordinary and table repeatable entries', function (string $component): void {
        RepeatableEntry::configureUsing(
            static fn (RepeatableEntry $entry) => $entry->maxItemsForProcessing(1),
            during: fn () => livewire($component)->assertSuccessful(),
        );

        RepeatableEntry::configureUsing(
            static fn (RepeatableEntry $entry) => $entry->maxItemsForProcessing(0),
            during: fn () => livewire($component)->assertStatus(413),
        );
    })->with([
        'ordinary' => [RenderRepeatableEntryWithContainedFalse::class],
        'table' => [RenderRepeatableEntryWithTable::class],
    ]);

    it('limits nested repeatable entries independently of the parent repeater', function (): void {
        Repeater::fake();

        $component = livewire(RepeatableEntryWithProcessingLimit::class)
            ->set('data.items.0.children', [['name' => 'First'], ['name' => 'Second']])
            ->assertSuccessful()
            ->assertSeeText('Second');

        $component->set('data.items.0.children', [['name' => 'First'], ['name' => 'Second'], ['name' => 'Third']])
            ->assertStatus(413);
    });
});

describe('nested state resolution', function (): void {
    it('can resolve state for `RepeatableEntry` within nested sections with `statePath()`', function (): void {
        livewire(TestRepeatableEntryInNestedSections::class)
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('page.block.items.0.name', 'Item One', 'infolist')
            ->assertSeeText('Item One')
            ->assertSchemaComponentStateSet('page.block.items.1.name', 'Item Two', 'infolist')
            ->assertSeeText('Item Two');
    });

    it('can resolve state for nested `RepeatableEntry`', function (): void {
        livewire(TestNestedRepeatableEntryState::class)
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('users.0.name', 'User Alice', 'infolist')
            ->assertSeeText('User Alice')
            ->assertSchemaComponentStateSet('users.0.comments.0.text', 'Comment from Alice: Hello', 'infolist')
            ->assertSeeText('Comment from Alice: Hello')
            ->assertSchemaComponentStateSet('users.0.comments.1.text', 'Comment from Alice: World', 'infolist')
            ->assertSeeText('Comment from Alice: World')
            ->assertSchemaComponentStateSet('users.1.name', 'User Bob', 'infolist')
            ->assertSeeText('User Bob')
            ->assertSchemaComponentStateSet('users.1.comments.0.text', 'Comment from Bob: Hi', 'infolist')
            ->assertSeeText('Comment from Bob: Hi');
    });

    it('can resolve state for deeply nested `RepeatableEntry` within sections', function (): void {
        livewire(TestDeeplyNestedRepeatableEntry::class)
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('store.categories.0.name', 'Category A', 'infolist')
            ->assertSeeText('Category A')
            ->assertSchemaComponentStateSet('store.categories.0.products.0.name', 'Product 1', 'infolist')
            ->assertSeeText('Product 1')
            ->assertSchemaComponentStateSet('store.categories.0.products.0.variants.0.sku', 'SKU-001', 'infolist')
            ->assertSeeText('SKU-001')
            ->assertSchemaComponentStateSet('store.categories.0.products.0.variants.1.sku', 'SKU-002', 'infolist')
            ->assertSeeText('SKU-002')
            ->assertSchemaComponentStateSet('store.categories.0.products.1.name', 'Product 2', 'infolist')
            ->assertSeeText('Product 2');
    });

});

describe('relationships', function (): void {
    it('can rebuild items when a loaded relationship `Collection` is mutated in place', function (): void {
        $user = User::factory()
            ->has(Post::factory()->count(2), 'posts')
            ->create()
            ->load('posts');

        $schema = Schema::make(Livewire::make())
            ->record($user)
            ->components([
                RepeatableEntry::make('posts')
                    ->schema([
                        TextEntry::make('title'),
                    ]),
            ]);

        $entry = $schema->getComponents()[0];

        expect($entry->getItems())->toHaveCount(2);

        $user->posts->pop();

        expect($entry->getState())->toHaveCount(1)
            ->and($entry->getItems())->toHaveCount(1);
    });

    it('can resolve state for relationship-based `RepeatableEntry`', function (): void {
        $user = User::factory()
            ->has(Post::factory()->count(2)->sequence(
                ['title' => 'First Post', 'content' => 'Content 1'],
                ['title' => 'Second Post', 'content' => 'Content 2'],
            ), 'posts')
            ->create(['name' => 'Test User']);

        livewire(TestRelationshipRepeatableEntry::class, ['user' => $user])
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('name', 'Test User', 'infolist')
            ->assertSeeText('Test User')
            ->assertSchemaComponentStateSet('posts.0.title', 'First Post', 'infolist')
            ->assertSeeText('First Post')
            ->assertSchemaComponentStateSet('posts.1.title', 'Second Post', 'infolist')
            ->assertSeeText('Second Post');
    });

    it('can resolve state for relationship-based `RepeatableEntry` within nested sections', function (): void {
        $user = User::factory()
            ->has(Post::factory()->count(2)->sequence(
                ['title' => 'Post A'],
                ['title' => 'Post B'],
            ), 'posts')
            ->create();

        livewire(TestRelationshipRepeatableEntryInSection::class, ['user' => $user])
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('posts.0.title', 'Post A', 'infolist')
            ->assertSeeText('Post A')
            ->assertSchemaComponentStateSet('posts.1.title', 'Post B', 'infolist')
            ->assertSeeText('Post B');
    });

    it('does not render a related record\'s row after an `Action` deletes it, without a page refresh', function (): void {
        $user = User::factory()
            ->has(Post::factory()->count(2)->sequence(
                ['title' => 'Delete me without a refresh'],
                ['title' => 'Keep me rendered'],
            ), 'posts')
            ->create();

        $component = livewire(TestRelationshipRepeatableEntryWithDeleteAction::class, ['user' => $user])
            ->assertSeeText('Delete me without a refresh')
            ->assertSeeText('Keep me rendered')
            ->callAction(TestAction::make('deletePost')->schemaComponent('posts.0.title'));

        expect(Post::query()->where('title', 'Delete me without a refresh')->exists())->toBeFalse();
        expect($user->posts()->count())->toBe(1);

        $component
            ->assertSeeText('Keep me rendered')
            ->assertDontSeeText('Delete me without a refresh');
    });

});

it('correctly asserts entry state within `RepeatableEntry` using `assertSchemaComponentStateSet()`', function (): void {
    livewire(TestRepeatableEntryStateAssertions::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('items.0.name', 'First', 'infolist')
        ->assertSeeText('First')
        ->assertSchemaComponentStateSet('items.1.name', 'Second', 'infolist')
        ->assertSeeText('Second');
});

it('correctly asserts nested `RepeatableEntry` state using `assertSchemaComponentStateSet()`', function (): void {
    livewire(TestNestedRepeatableEntryStateAssertions::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('users.0.name', 'Alice', 'infolist')
        ->assertSeeText('Alice')
        ->assertSchemaComponentStateSet('users.0.posts.0.title', 'Post A', 'infolist')
        ->assertSeeText('Post A')
        ->assertSchemaComponentStateSet('users.0.posts.1.title', 'Post B', 'infolist')
        ->assertSeeText('Post B');
});

it('can render', function (): void {
    livewire(TestComponentWithRepeatableEntry::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('items.0.name', 'First Item', 'infolist')
        ->assertSeeText('First Item')
        ->assertSchemaComponentStateSet('items.1.name', 'Second Item', 'infolist')
        ->assertSeeText('Second Item');
});

it('can render nested entries', function (): void {
    livewire(TestComponentWithNestedRepeatableEntry::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('users.0.name', 'John', 'infolist')
        ->assertSeeText('John')
        ->assertSchemaComponentStateSet('users.0.email', 'john@example.com', 'infolist')
        ->assertSeeText('john@example.com')
        ->assertSchemaComponentStateSet('users.1.name', 'Jane', 'infolist')
        ->assertSeeText('Jane')
        ->assertSchemaComponentStateSet('users.1.email', 'jane@example.com', 'infolist')
        ->assertSeeText('jane@example.com');
});

describe('rendering', function (): void {
    it('can render with `contained(false)`', function (): void {
        livewire(RenderRepeatableEntryWithContainedFalse::class)->assertSuccessful();
    });

    it('can render with `contained()` set via `Closure`', function (): void {
        livewire(RenderRepeatableEntryWithClosureContained::class)->assertSuccessful();
    });

    it('can render with `grid()` int', function (): void {
        livewire(RenderRepeatableEntryWithGridInt::class)->assertSuccessful();
    });

    it('can render with `grid()` array', function (): void {
        livewire(RenderRepeatableEntryWithGridArray::class)->assertSuccessful();
    });

    it('can render with `grid()` set via `Closure`', function (): void {
        livewire(RenderRepeatableEntryWithClosureGrid::class)->assertSuccessful();
    });

    it('can render with `table()` columns', function (): void {
        livewire(RenderRepeatableEntryWithTable::class)->assertSuccessful();
    });

    it('can render with `table()` columns set via `Closure`', function (): void {
        livewire(RenderRepeatableEntryWithClosureTable::class)->assertSuccessful();
    });
});

it('can render `RepeatableEntry` within form schema', function (): void {
    livewire(TestRepeatableEntryInForm::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('tags.0.label', 'Tag One', 'form')
        ->assertSeeText('Tag One')
        ->assertSchemaComponentStateSet('tags.1.label', 'Tag Two', 'form')
        ->assertSeeText('Tag Two');
});

class RepeatableEntryWithProcessingLimit extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill(['items' => [['children' => [['name' => 'First']]]]]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Repeater::make('items')->maxItemsForProcessing(1)->schema([
                RepeatableEntry::make('children')->maxItemsForProcessing(2)->schema([TextEntry::make('name')]),
            ]),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->form }}</div>';
    }
}

class TestRelationshipRepeatableEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->user)
            ->components([
                TextEntry::make('name'),
                RepeatableEntry::make('posts')
                    ->schema([
                        TextEntry::make('title'),
                    ]),
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

class TestRelationshipRepeatableEntryInSection extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->user)
            ->components([
                Section::make('User Details')
                    ->schema([
                        TextEntry::make('name'),
                        Section::make('Posts')
                            ->schema([
                                RepeatableEntry::make('posts')
                                    ->schema([
                                        TextEntry::make('title'),
                                    ]),
                            ]),
                    ]),
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

class TestRelationshipRepeatableEntryWithDeleteAction extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->user)
            ->components([
                RepeatableEntry::make('posts')
                    ->schema([
                        TextEntry::make('title')
                            ->registerActions([
                                Action::make('deletePost')
                                    ->action(function (Post $record): void {
                                        $record->delete();

                                        $this->user->setRelation('posts', $this->user->posts()->get());
                                    }),
                            ]),
                    ]),
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

class TestRepeatableEntryInNestedSections extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'page' => [
                    'block' => [
                        'items' => [
                            ['name' => 'Item One'],
                            ['name' => 'Item Two'],
                        ],
                    ],
                ],
            ])
            ->components([
                Section::make('Page')
                    ->statePath('page')
                    ->schema([
                        Section::make('Block')
                            ->statePath('block')
                            ->schema([
                                RepeatableEntry::make('items')
                                    ->schema([
                                        TextEntry::make('name'),
                                    ]),
                            ]),
                    ]),
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

class TestNestedRepeatableEntryState extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'users' => [
                    [
                        'name' => 'User Alice',
                        'comments' => [
                            ['text' => 'Comment from Alice: Hello'],
                            ['text' => 'Comment from Alice: World'],
                        ],
                    ],
                    [
                        'name' => 'User Bob',
                        'comments' => [
                            ['text' => 'Comment from Bob: Hi'],
                        ],
                    ],
                ],
            ])
            ->components([
                RepeatableEntry::make('users')
                    ->schema([
                        TextEntry::make('name'),
                        RepeatableEntry::make('comments')
                            ->schema([
                                TextEntry::make('text'),
                            ]),
                    ]),
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

class TestDeeplyNestedRepeatableEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'store' => [
                    'categories' => [
                        [
                            'name' => 'Category A',
                            'products' => [
                                [
                                    'name' => 'Product 1',
                                    'variants' => [
                                        ['sku' => 'SKU-001'],
                                        ['sku' => 'SKU-002'],
                                    ],
                                ],
                                [
                                    'name' => 'Product 2',
                                    'variants' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->components([
                Section::make('Store')
                    ->statePath('store')
                    ->schema([
                        RepeatableEntry::make('categories')
                            ->schema([
                                TextEntry::make('name'),
                                RepeatableEntry::make('products')
                                    ->schema([
                                        TextEntry::make('name'),
                                        RepeatableEntry::make('variants')
                                            ->schema([
                                                TextEntry::make('sku'),
                                            ]),
                                    ]),
                            ]),
                    ]),
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

class TestComponentWithRepeatableEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'items' => [
                    ['name' => 'First Item'],
                    ['name' => 'Second Item'],
                ],
            ])
            ->components([
                RepeatableEntry::make('items')
                    ->schema([
                        TextEntry::make('name'),
                    ]),
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

class TestComponentWithNestedRepeatableEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'users' => [
                    ['name' => 'John', 'email' => 'john@example.com'],
                    ['name' => 'Jane', 'email' => 'jane@example.com'],
                ],
            ])
            ->components([
                RepeatableEntry::make('users')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                    ]),
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

class TestRepeatableEntryStateAssertions extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'items' => [
                    ['name' => 'First'],
                    ['name' => 'Second'],
                ],
            ])
            ->components([
                RepeatableEntry::make('items')
                    ->schema([
                        TextEntry::make('name'),
                    ]),
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

class TestNestedRepeatableEntryStateAssertions extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'users' => [
                    [
                        'name' => 'Alice',
                        'posts' => [
                            ['title' => 'Post A'],
                            ['title' => 'Post B'],
                        ],
                    ],
                ],
            ])
            ->components([
                RepeatableEntry::make('users')
                    ->schema([
                        TextEntry::make('name'),
                        RepeatableEntry::make('posts')
                            ->schema([
                                TextEntry::make('title'),
                            ]),
                    ]),
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

class TestRepeatableEntryInForm extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => 'Test Record',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('name'),
                Section::make('Tags')
                    ->schema([
                        RepeatableEntry::make('tags')
                            ->state([
                                ['label' => 'Tag One'],
                                ['label' => 'Tag Two'],
                            ])
                            ->schema([
                                TextEntry::make('label'),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->form }}
            </div>
            BLADE;
    }
}

class RenderRepeatableEntryWithContainedFalse extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['items' => [['name' => 'A']]])->components([
            RepeatableEntry::make('items')->schema([TextEntry::make('name')])->contained(false),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderRepeatableEntryWithClosureContained extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['items' => [['name' => 'A']]])->components([
            RepeatableEntry::make('items')->schema([TextEntry::make('name')])->contained(static fn (): bool => false),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderRepeatableEntryWithGridInt extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['items' => [['name' => 'A']]])->components([
            RepeatableEntry::make('items')->schema([TextEntry::make('name')])->grid(3),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderRepeatableEntryWithGridArray extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['items' => [['name' => 'A']]])->components([
            RepeatableEntry::make('items')->schema([TextEntry::make('name')])->grid(['default' => 1, 'sm' => 2, 'lg' => 3]),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderRepeatableEntryWithClosureGrid extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['items' => [['name' => 'A']]])->components([
            RepeatableEntry::make('items')->schema([TextEntry::make('name')])->grid(static fn (): int => 4),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderRepeatableEntryWithTable extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['items' => [['name' => 'A', 'email' => 'a@test.com']]])->components([
            RepeatableEntry::make('items')
                ->schema([TextEntry::make('name'), TextEntry::make('email')])
                ->table([TableColumn::make('name'), TableColumn::make('email')]),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderRepeatableEntryWithClosureTable extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state(['items' => [['name' => 'A']]])->components([
            RepeatableEntry::make('items')
                ->schema([TextEntry::make('name')])
                ->table(static fn (): array => [TableColumn::make('name')]),
        ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
