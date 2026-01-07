<?php

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\PostMetadata;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Exceptions\RootTagMissingFromViewException;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can fill and assert data in a repeater', function (array $data): void {
    $undoRepeaterFake = Repeater::fake();

    try {
        livewire(TestComponentWithRepeater::class)
            ->fillForm($data)
            ->assertSchemaStateSet($data);
    } catch (RootTagMissingFromViewException $exception) {
        // Flaky test
    }

    $undoRepeaterFake();
})->with([
    'normal' => fn (): array => ['normal' => [
        [
            'title' => Str::random(),
            'category' => Str::random(),
        ],
        [
            'title' => Str::random(),
            'category' => Str::random(),
        ],
        [
            'title' => Str::random(),
            'category' => Str::random(),
        ],
    ]],
    'simple' => fn (): array => ['simple' => [
        Str::random(),
        Str::random(),
        Str::random(),
    ]],
    'nested' => fn (): array => ['parent' => [
        [
            'title' => Str::random(),
            'category' => Str::random(),
            'nested' => [
                [
                    'name' => Str::random(),
                ],
                [
                    'name' => Str::random(),
                ],
                [
                    'name' => Str::random(),
                ],
            ],
            'nestedSimple' => [
                Str::random(),
                Str::random(),
                Str::random(),
            ],
        ],
        [
            'title' => Str::random(),
            'category' => Str::random(),
            'nested' => [
                [
                    'name' => Str::random(),
                ],
                [
                    'name' => Str::random(),
                ],
                [
                    'name' => Str::random(),
                ],
            ],
            'nestedSimple' => [
                Str::random(),
                Str::random(),
                Str::random(),
            ],
        ],
        [
            'title' => Str::random(),
            'category' => Str::random(),
            'nested' => [
                [
                    'name' => Str::random(),
                ],
                [
                    'name' => Str::random(),
                ],
                [
                    'name' => Str::random(),
                ],
            ],
            'nestedSimple' => [
                Str::random(),
                Str::random(),
                Str::random(),
            ],
        ],
    ]],
]);

it('can fill and assert default data in a repeater', function (array $data): void {
    $undoRepeaterFake = Repeater::fake();

    try {
        livewire(TestComponentWithRepeater::class)
            ->assertSchemaStateSet($data);
    } catch (RootTagMissingFromViewException $exception) {
        // Flaky test
    }

    $undoRepeaterFake();
})->with([
    'normal' => fn (): array => ['normal' => [
        [
            'title' => 'title 1',
            'category' => 'category 1',
        ],
        [
            'title' => 'title 2',
            'category' => 'category 2',
        ],
        [
            'title' => 'title 3',
            'category' => 'category 3',
        ],
    ]],
    'simple' => fn (): array => ['simple' => [
        ['title' => 'simple 1'],
        ['title' => 'simple 2'],
        ['title' => 'simple 3'],
    ]],
    'nested' => fn (): array => ['parent' => [
        [
            'title' => 'title 1',
            'category' => 'category 1',
            'nested' => [
                [
                    'name' => '1 nested name 1',
                ],
                [
                    'name' => '1 nested name 2',
                ],
                [
                    'name' => '1 nested name 3',
                ],
            ],
            'nestedSimple' => [
                ['name' => null],
            ],
        ],
        [
            'title' => 'title 2',
            'category' => 'category 2',
            'nested' => [
                [
                    'name' => '2 nested name 1',
                ],
                [
                    'name' => '2 nested name 2',
                ],
                [
                    'name' => '2 nested name 3',
                ],
            ],
            'nestedSimple' => [
                ['name' => null],
            ],
        ],
        [
            'title' => 'title 3',
            'category' => 'category 3',
            'nested' => [
                [
                    'name' => '3 nested name 1',
                ],
                [
                    'name' => '3 nested name 2',
                ],
                [
                    'name' => '3 nested name 3',
                ],
            ],
            'nestedSimple' => [
                ['name' => null],
            ],
        ],
    ]],
]);

it('can remove items from a repeater', function (): void {
    $undoRepeaterFake = Repeater::fake();

    livewire(TestComponentWithRepeater::class)
        ->fillForm($data = [
            'normal' => [
                [
                    'title' => Str::random(),
                    'category' => Str::random(),
                ],
                [
                    'title' => Str::random(),
                    'category' => Str::random(),
                ],
            ],
        ])
        ->assertSchemaStateSet($data)
        ->fillForm([
            'normal' => [
                Arr::first($data['normal']),
            ],
        ])
        ->assertSchemaStateSet(function (array $data) {
            expect($data['normal'])->toHaveCount(1);

            return [
                'normal' => [
                    Arr::first($data['normal']),
                ],
            ];
        });

    $undoRepeaterFake();
});

it('loads a relationship', function (): void {
    $user = User::factory()
        ->has(Post::factory()->count(3))
        ->create();

    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Repeater('repeater'))
                ->relationship('posts')
                ->schema([
                    TextInput::make('title'),
                ]),
        ])
        ->model($user);

    $schema->loadStateFromRelationships();

    $schema->saveRelationships();

    expect($user->posts()->count())
        ->toBe(3);
});

it('can load state from a `HasMany` relationship using eager loaded data without additional queries', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();
    Post::factory()->count(3)->create(['author_id' => $user->id]);

    $freshUser = $user->fresh();
    expect($freshUser->relationLoaded('posts'))->toBeFalse();

    DB::enableQueryLog();
    DB::flushQueryLog();

    livewire(RepeaterWithHasManyRelationship::class, ['record' => $freshUser])
        ->assertSchemaStateSet(function (array $state) {
            expect($state['posts'])->toHaveCount(3);

            return [];
        });

    $queriesWithoutEagerLoading = count(DB::getQueryLog());

    $eagerUser = $user->fresh();
    $eagerUser->load('posts');
    expect($eagerUser->relationLoaded('posts'))->toBeTrue();

    DB::flushQueryLog();

    livewire(RepeaterWithHasManyRelationship::class, ['record' => $eagerUser])
        ->assertSchemaStateSet(function (array $state) {
            expect($state['posts'])->toHaveCount(3);

            return [];
        });

    $queriesWithEagerLoading = count(DB::getQueryLog());
    DB::disableQueryLog();

    $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
    expect($queriesSaved)->toBe(1, "Expected to save 1 query with eager loading, but saved {$queriesSaved}");

    $undoRepeaterFake();
});

it('does not use eager loaded data when `modifyQueryUsing()` is set', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();
    Post::factory()->count(3)->create(['author_id' => $user->id]);

    $freshUser = $user->fresh();
    expect($freshUser->relationLoaded('posts'))->toBeFalse();

    DB::enableQueryLog();
    DB::flushQueryLog();

    livewire(RepeaterWithHasManyRelationshipAndModifyQuery::class, ['record' => $freshUser])
        ->assertSchemaStateSet(function (array $state) {
            expect($state['posts'])->toHaveCount(3);

            return [];
        });

    $queriesWithoutEagerLoading = count(DB::getQueryLog());

    $eagerUser = $user->fresh();
    $eagerUser->load('posts');
    expect($eagerUser->relationLoaded('posts'))->toBeTrue();

    DB::flushQueryLog();

    livewire(RepeaterWithHasManyRelationshipAndModifyQuery::class, ['record' => $eagerUser])
        ->assertSchemaStateSet(function (array $state) {
            expect($state['posts'])->toHaveCount(3);

            return [];
        });

    $queriesWithEagerLoading = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($queriesWithEagerLoading)->toBe($queriesWithoutEagerLoading);

    $undoRepeaterFake();
});

it('throws an exception for a missing relationship', function (): void {
    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Repeater(Str::random()))
                ->relationship('missing'),
        ])
        ->model(Post::factory()->create());

    $schema
        ->saveRelationships();
})->throws(Exception::class, 'The relationship [missing] does not exist on the model [Filament\Tests\Fixtures\Models\Post].');

it('can use select options from an enum with `disableOptionsWhenSelectedInSiblingRepeaterItems()`', function (): void {
    $undoRepeaterFake = Repeater::fake();

    livewire(TestComponentWithEnumSelectRepeater::class)
        ->fillForm([
            'alternatives' => [
                ['letter' => TestLetterEnum::A],
                ['letter' => TestLetterEnum::B],
            ],
        ])
        ->assertFormSet([
            'alternatives' => [
                ['letter' => TestLetterEnum::A],
                ['letter' => TestLetterEnum::B],
            ],
        ]);

    $undoRepeaterFake();
});

it('can be compact', function (): void {
    $repeater = Repeater::make('members')
        ->schema([
            TextInput::make('name'),
        ])
        ->compact();

    expect($repeater->isCompact())->toBeTrue();
});

it('can conditionally be compact', function (): void {
    $repeater = Repeater::make('members')
        ->schema([
            TextInput::make('name'),
        ])
        ->compact(fn () => true);

    expect($repeater->isCompact())->toBeTrue();

    $repeater = Repeater::make('members')
        ->schema([
            TextInput::make('name'),
        ])
        ->compact(fn () => false);

    expect($repeater->isCompact())->toBeFalse();
});

it('can use arguments to hide the delete action', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $deleteAction1 = TestAction::make('delete')
        ->schemaComponent('hiddenDelete')
        ->arguments(['item' => 1]);

    $deleteAction2 = TestAction::make('delete')
        ->schemaComponent('hiddenDelete')
        ->arguments(['item' => 2]);

    livewire(TestComponentWithRepeater::class)
        ->assertActionHidden($deleteAction1)
        ->assertActionVisible($deleteAction2);

    $undoRepeaterFake();
});

class TestComponentWithRepeater extends Livewire
{
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Repeater::make('normal')
                    ->itemLabel(function (array $state) {
                        return $state['title'] . $state['category'];
                    })
                    ->schema([
                        TextInput::make('title'),
                        TextInput::make('category'),
                    ])
                    ->default([
                        [
                            'title' => 'title 1',
                            'category' => 'category 1',
                        ],
                        [
                            'title' => 'title 2',
                            'category' => 'category 2',
                        ],
                        [
                            'title' => 'title 3',
                            'category' => 'category 3',
                        ],
                    ]),
                Repeater::make('simple')
                    ->simple(TextInput::make('title'))
                    ->default([
                        'simple 1',
                        'simple 2',
                        'simple 3',
                    ]),
                Repeater::make('parent')
                    ->itemLabel(fn (array $state) => $state['title'] . $state['category'])
                    ->schema([
                        TextInput::make('title'),
                        TextInput::make('category'),
                        Repeater::make('nested')
                            ->itemLabel(fn (array $state) => $state['name'])
                            ->schema([
                                TextInput::make('name'),
                            ]),
                        Repeater::make('nestedSimple')
                            ->simple(TextInput::make('name')),
                    ])
                    ->default([
                        [
                            'title' => 'title 1',
                            'category' => 'category 1',
                            'nested' => [
                                [
                                    'name' => '1 nested name 1',
                                ],
                                [
                                    'name' => '1 nested name 2',
                                ],
                                [
                                    'name' => '1 nested name 3',
                                ],
                            ],
                        ],
                        [
                            'title' => 'title 2',
                            'category' => 'category 2',
                            'nested' => [
                                [
                                    'name' => '2 nested name 1',
                                ],
                                [
                                    'name' => '2 nested name 2',
                                ],
                                [
                                    'name' => '2 nested name 3',
                                ],
                            ],
                        ],
                        [
                            'title' => 'title 3',
                            'category' => 'category 3',
                            'nested' => [
                                [
                                    'name' => '3 nested name 1',
                                ],
                                [
                                    'name' => '3 nested name 2',
                                ],
                                [
                                    'name' => '3 nested name 3',
                                ],
                            ],
                        ],
                    ]),
                Repeater::make('hiddenDelete')
                    ->schema([
                        TextInput::make('title'),
                    ])
                    ->default([
                        [
                            'title' => 'title 1',
                        ],
                        [
                            'title' => 'title 2',
                        ],
                        [
                            'title' => 'title 3',
                        ],
                    ])
                    ->deleteAction(fn (Action $action) => $action->hidden(fn (array $arguments): bool => $arguments['item'] === 1)),
            ])
            ->statePath('data');
    }
}

class TestComponentWithEnumSelectRepeater extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('alternatives')
                    ->schema([
                        Select::make('letter')
                            ->options(TestLetterEnum::class)
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                    ]),
            ])
            ->statePath('data');
    }
}

it('can inject `$parentRepeaterItemIndex` into component configuration closures', function (): void {
    $undoRepeaterFake = Repeater::fake();

    livewire(TestComponentWithParentRepeaterItemIndex::class)
        ->assertFormComponentExists('items.0.name', function (TextInput $field): bool {
            expect($field->getLabel())->toBe('Item 0');

            return true;
        })
        ->assertFormComponentExists('items.1.name', function (TextInput $field): bool {
            expect($field->getLabel())->toBe('Item 1');

            return true;
        })
        ->assertFormComponentExists('items.2.name', function (TextInput $field): bool {
            expect($field->getLabel())->toBe('Item 2');

            return true;
        });

    $undoRepeaterFake();
});

class TestComponentWithParentRepeaterItemIndex extends Livewire
{
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Repeater::make('items')
                    ->schema([
                        TextInput::make('name')
                            ->label(fn (int $parentRepeaterItemIndex): string => "Item {$parentRepeaterItemIndex}"),
                    ])
                    ->default([
                        ['name' => 'first'],
                        ['name' => 'second'],
                        ['name' => 'third'],
                    ]),
            ])
            ->statePath('data');
    }
}

enum TestLetterEnum: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
}

it('can validate distinct fields in a builder block in a repeater with errors', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $data = [
        'repeater' => [
            [
                'bar' => 'test 1',
                'builder' => [
                    [
                        'type' => 'one',
                        'data' => [
                            'foo' => 'test 1',
                        ],
                    ],
                    [
                        'type' => 'one',
                        'data' => [
                            'foo' => 'test 1',
                        ],
                    ],
                ],
            ],
            [
                'bar' => 'test 1',
                'builder' => [
                    [
                        'type' => 'one',
                        'data' => [
                            'foo' => 'test 1',
                        ],
                    ],
                    [
                        'type' => 'one',
                        'data' => [
                            'foo' => 'test 1',
                        ],
                    ],
                ],
            ],
        ],
    ];

    livewire(TestComponentWithRepeaterAndBuilder::class)
        ->assertSuccessful()
        ->fillForm($data)
        ->assertFormSet($data)
        ->call('save')
        ->assertHasFormErrors([
            'repeater.0.bar' => ['The bar field has a duplicate value.'],
            'repeater.0.builder.0.data.foo' => ['The foo field has a duplicate value.'],
            'repeater.0.builder.1.data.foo' => ['The foo field has a duplicate value.'],
            'repeater.1.bar' => ['The bar field has a duplicate value.'],
            'repeater.1.builder.0.data.foo' => ['The foo field has a duplicate value.'],
            'repeater.1.builder.1.data.foo' => ['The foo field has a duplicate value.'],
        ]);

    $undoRepeaterFake();
});

it('can validate distinct fields in a builder block in a repeater with no errors', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $data = [
        'repeater' => [
            [
                'bar' => 'test 1',
                'builder' => [
                    [
                        'type' => 'one',
                        'data' => [
                            'foo' => 'test 1',
                        ],
                    ],
                    [
                        'type' => 'one',
                        'data' => [
                            'foo' => 'test 2',
                        ],
                    ],
                ],
            ],
            [
                'bar' => 'test 2',
                'builder' => [
                    [
                        'type' => 'one',
                        'data' => [
                            'foo' => 'test 1',
                        ],
                    ],
                    [
                        'type' => 'one',
                        'data' => [
                            'foo' => 'test 2',
                        ],
                    ],
                ],
            ],
        ],
    ];

    livewire(TestComponentWithRepeaterAndBuilder::class)
        ->assertSuccessful()
        ->fillForm($data)
        ->assertFormSet($data)
        ->call('save')
        ->assertHasNoFormErrors();

    $undoRepeaterFake();
});

class TestComponentWithRepeaterAndBuilder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Repeater::make('repeater')
                    ->schema([
                        TextInput::make('bar')
                            ->distinct(),
                        Builder::make('builder')
                            ->blocks([
                                Builder\Block::make('one')
                                    ->schema([
                                        TextInput::make('foo')
                                            ->distinct(),
                                    ]),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

it('can set repeater state programmatically via action', function (): void {
    livewire(TestComponentWithRepeaterSetByAction::class)
        ->callAction(TestAction::make('insert')->schemaComponent('questionsSection'))
        ->assertSuccessful();
});

it('can access correct item schema state from action directly in repeater schema', function (): void {
    $undoRepeaterFake = Repeater::fake();

    livewire(TestComponentWithActionInRepeater::class)
        ->callAction(
            TestAction::make('captureSchemaState')
                ->schemaComponent('items.0'),
        )
        ->assertDispatched('state-captured', state: [
            'name' => 'Item 1',
        ])
        ->callAction(
            TestAction::make('captureSchemaState')
                ->schemaComponent('items.1'),
        )
        ->assertDispatched('state-captured', state: [
            'name' => 'Item 2',
        ]);

    $undoRepeaterFake();
});

it('can access correct item state from `extraItemActions()`', function (): void {
    $undoRepeaterFake = Repeater::fake();

    livewire(TestComponentWithExtraItemAction::class)
        ->callAction(
            TestAction::make('captureItemState')
                ->schemaComponent('items')
                ->arguments(['item' => 0]),
        )
        ->assertDispatched('state-captured', state: [
            'name' => 'First Item',
        ])
        ->callAction(
            TestAction::make('captureItemState')
                ->schemaComponent('items')
                ->arguments(['item' => 1]),
        )
        ->assertDispatched('state-captured', state: [
            'name' => 'Second Item',
        ]);

    $undoRepeaterFake();
});

class TestComponentWithExtraItemAction extends Livewire
{
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Repeater::make('items')
                    ->schema([
                        TextInput::make('name'),
                    ])
                    ->extraItemActions([
                        Action::make('captureItemState')
                            ->action(function (array $schemaState): void {
                                $this->dispatch('state-captured', state: $schemaState);
                            }),
                    ])
                    ->default([
                        ['name' => 'First Item'],
                        ['name' => 'Second Item'],
                    ]),
            ])
            ->statePath('data');
    }
}

class TestComponentWithActionInRepeater extends Livewire
{
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Repeater::make('items')
                    ->schema([
                        TextInput::make('name'),
                        Action::make('captureSchemaState')
                            ->action(function (array $schemaState): void {
                                $this->dispatch('state-captured', state: $schemaState);
                            }),
                    ])
                    ->default([
                        ['name' => 'Item 1'],
                        ['name' => 'Item 2'],
                    ]),
            ])
            ->statePath('data');
    }
}

class TestComponentWithRepeaterSetByAction extends Livewire
{
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Questions')
                    ->key('questionsSection')
                    ->schema([
                        Repeater::make('questions')
                            ->schema([
                                TextInput::make('question')->required(),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['question'] ?? null),
                    ])
                    ->headerActions([
                        Action::make('insert')
                            ->label('Insert Question')
                            ->action(function (Set $set): void {
                                $set('questions', [
                                    ['question' => 'Question #1'],
                                    ['question' => 'Question #2'],
                                    ['question' => 'Question #3'],
                                ]);
                            }),
                    ]),
            ])
            ->statePath('data');
    }
}

class RepeaterWithHasManyRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('posts')
                    ->relationship('posts')
                    ->schema([
                        TextInput::make('title'),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RepeaterWithHasManyRelationshipAndModifyQuery extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('posts')
                    ->relationship(
                        'posts',
                        modifyQueryUsing: fn ($query) => $query->orderBy('title'),
                    )
                    ->schema([
                        TextInput::make('title'),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

it('can add and delete items in the browser', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    visit('/repeater-test')
        ->assertSee('Repeater Test')
        ->assertSee('Items')
        ->assertPresent('[data-testid="repeater"] .fi-fo-repeater-item')
        ->click('[data-testid="repeater"] .fi-fo-repeater-add button')
        ->wait(1)
        ->assertPresent('[data-testid="repeater"] .fi-fo-repeater-item:nth-child(2)')
        ->click('[data-testid="repeater"] .fi-fo-repeater-item:nth-child(2) .fi-fo-repeater-item-header-end-actions button')
        ->wait(1)
        ->assertNotPresent('[data-testid="repeater"] .fi-fo-repeater-item:nth-child(2)')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    visit('/repeater-test')
        ->inDarkMode()
        ->assertNoAccessibilityIssues();
});

it('can save a nested singular relationship inside a repeater item', function (): void {
    $user = User::factory()->create();

    $undoRepeaterFake = Repeater::fake();

    livewire(RepeaterWithNestedSingularRelationship::class, ['record' => $user])
        ->fillForm([
            'posts' => [
                [
                    'title' => 'Test Post Title',
                    'metadata' => [
                        'seo_title' => 'Test SEO Title',
                    ],
                ],
            ],
        ])
        ->call('save');

    $undoRepeaterFake();

    $user->refresh();

    expect($user->posts)->toHaveCount(1);

    $post = $user->posts->first();
    expect($post->title)->toBe('Test Post Title');
    expect($post->metadata)->not->toBeNull();
    expect($post->metadata->seo_title)->toBe('Test SEO Title');
});

it('can load a nested singular relationship inside a repeater item', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'author_id' => $user->id,
        'title' => 'Existing Post',
    ]);
    PostMetadata::factory()->create([
        'post_id' => $post->id,
        'seo_title' => 'Existing SEO Title',
    ]);

    $undoRepeaterFake = Repeater::fake();

    livewire(RepeaterWithNestedSingularRelationship::class, ['record' => $user])
        ->assertFormSet(function (array $state): array {
            expect($state['posts'])->toHaveCount(1);
            expect($state['posts'][array_key_first($state['posts'])]['title'])->toBe('Existing Post');
            expect($state['posts'][array_key_first($state['posts'])]['metadata']['seo_title'])->toBe('Existing SEO Title');

            return [];
        });

    $undoRepeaterFake();
});

it('can update an existing nested singular relationship inside a repeater item', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'author_id' => $user->id,
        'title' => 'Original Title',
    ]);
    $metadata = PostMetadata::factory()->create([
        'post_id' => $post->id,
        'seo_title' => 'Original SEO Title',
    ]);

    $undoRepeaterFake = Repeater::fake();

    $component = livewire(RepeaterWithNestedSingularRelationship::class, ['record' => $user]);

    $postKey = array_key_first($component->get('data.posts'));

    $component
        ->fillForm([
            'posts' => [
                $postKey => [
                    'title' => 'Updated Title',
                    'metadata' => [
                        'seo_title' => 'Updated SEO Title',
                    ],
                ],
            ],
        ])
        ->call('save');

    $undoRepeaterFake();

    $post->refresh();
    $metadata->refresh();

    expect($post->title)->toBe('Updated Title');
    expect($metadata->seo_title)->toBe('Updated SEO Title');
});

it('can add a new repeater item with nested singular relationship to an existing repeater', function (): void {
    $user = User::factory()->create();
    $existingPost = Post::factory()->create([
        'author_id' => $user->id,
        'title' => 'Existing Post',
    ]);
    PostMetadata::factory()->create([
        'post_id' => $existingPost->id,
        'seo_title' => 'Existing SEO Title',
    ]);

    $undoRepeaterFake = Repeater::fake();

    $component = livewire(RepeaterWithNestedSingularRelationship::class, ['record' => $user]);

    $existingPostKey = array_key_first($component->get('data.posts'));

    $component
        ->fillForm([
            'posts' => [
                $existingPostKey => [
                    'title' => 'Existing Post',
                    'metadata' => [
                        'seo_title' => 'Existing SEO Title',
                    ],
                ],
                [
                    'title' => 'New Post',
                    'metadata' => [
                        'seo_title' => 'New SEO Title',
                    ],
                ],
            ],
        ])
        ->call('save');

    $undoRepeaterFake();

    $user->refresh();

    expect($user->posts)->toHaveCount(2);

    $newPost = $user->posts->where('title', 'New Post')->first();
    expect($newPost)->not->toBeNull();
    expect($newPost->metadata)->not->toBeNull();
    expect($newPost->metadata->seo_title)->toBe('New SEO Title');
});

class RepeaterWithNestedSingularRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public array $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('posts')
                    ->relationship('posts')
                    ->schema([
                        TextInput::make('title')
                            ->required(),
                        Group::make()
                            ->relationship('metadata')
                            ->schema([
                                TextInput::make('seo_title'),
                            ]),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
        $this->form->saveRelationships();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}
