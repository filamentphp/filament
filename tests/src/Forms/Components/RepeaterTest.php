<?php

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\PostMetadata;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Translatable\RecordingTranslatableContentDriver;
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

describe('relationships', function (): void {
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

    it('does not delete out-of-scope records when clearing a Repeater bound to a scoped relationship', function (): void {
        $undoRepeaterFake = Repeater::fake();

        $user = User::factory()->create();
        $publishedPost = Post::factory()->create([
            'author_id' => $user->id,
            'is_published' => true,
            'title' => 'Published Title',
        ]);
        $outOfScopePost = Post::factory()->create([
            'author_id' => $user->id,
            'is_published' => false,
            'title' => 'Unpublished Title',
        ]);

        $component = livewire(RepeaterWithPublishedPostsRelationship::class, ['record' => $user]);

        // Clear all repeater items, simulating a user emptying the field.
        $component->set('data.posts', []);
        $component->call('save');

        $undoRepeaterFake();

        // The in-scope post was deleted (intended behavior — it was in the visible set
        // and the user removed it from state).
        expect(Post::query()->whereKey($publishedPost->id)->exists())->toBeFalse();

        // The out-of-scope post must NOT be deleted — it was never in `$existingRecords`
        // because `modifyQueryUsing` filtered it out, so the deletion loop never sees it.
        expect(Post::query()->whereKey($outOfScopePost->id)->exists())->toBeTrue();
        expect($outOfScopePost->fresh()->title)->toBe('Unpublished Title');
        expect($outOfScopePost->fresh()->is_published)->toBeFalse();
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
});

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

describe('`distinct()` validation on boolean fields', function (): void {
    it('does not force an optional `distinct()` boolean field to be selected when sibling items exist', function (string $component): void {
        livewire($component)
            ->fillForm([
                'items' => [
                    'item-1' => ['primary' => false],
                    'item-2' => ['primary' => false],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors(['items.item-1.primary', 'items.item-2.primary']);
    })->with([
        'checkbox' => TestComponentWithDistinctBooleanCheckboxInRepeater::class,
        'toggle' => TestComponentWithDistinctBooleanToggleInRepeater::class,
    ]);

    it('allows a single `distinct()` boolean field to be selected across sibling items', function (string $component): void {
        livewire($component)
            ->fillForm([
                'items' => [
                    'item-1' => ['primary' => true],
                    'item-2' => ['primary' => false],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors(['items.item-1.primary', 'items.item-2.primary']);
    })->with([
        'checkbox' => TestComponentWithDistinctBooleanCheckboxInRepeater::class,
        'toggle' => TestComponentWithDistinctBooleanToggleInRepeater::class,
    ]);

    it('does not allow more than one `distinct()` boolean field to be selected across sibling items', function (string $component): void {
        livewire($component)
            ->fillForm([
                'items' => [
                    'item-1' => ['primary' => true],
                    'item-2' => ['primary' => true],
                ],
            ])
            ->call('save')
            ->assertHasFormErrors(['items.item-1.primary', 'items.item-2.primary']);
    })->with([
        'checkbox' => TestComponentWithDistinctBooleanCheckboxInRepeater::class,
        'toggle' => TestComponentWithDistinctBooleanToggleInRepeater::class,
    ]);

    it('forces a `required()` `distinct()` boolean field to be selected across sibling items', function (string $component): void {
        livewire($component)
            ->fillForm([
                'items' => [
                    'item-1' => ['primary' => false],
                    'item-2' => ['primary' => false],
                ],
            ])
            ->call('save')
            ->assertHasFormErrors(['items.item-1.primary', 'items.item-2.primary']);
    })->with([
        'checkbox' => TestComponentWithRequiredDistinctBooleanCheckboxInRepeater::class,
        'toggle' => TestComponentWithRequiredDistinctBooleanToggleInRepeater::class,
    ]);
});

class TestComponentWithDistinctBooleanCheckboxInRepeater extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Repeater::make('items')
                    ->schema([
                        Checkbox::make('primary')
                            ->distinct(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithDistinctBooleanToggleInRepeater extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Repeater::make('items')
                    ->schema([
                        Toggle::make('primary')
                            ->distinct(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithRequiredDistinctBooleanCheckboxInRepeater extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Repeater::make('items')
                    ->schema([
                        Checkbox::make('primary')
                            ->distinct()
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithRequiredDistinctBooleanToggleInRepeater extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Repeater::make('items')
                    ->schema([
                        Toggle::make('primary')
                            ->distinct()
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

describe('actions in repeater', function (): void {
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

class RepeaterWithPublishedPostsRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('posts')
                    ->relationship(
                        'posts',
                        modifyQueryUsing: fn ($query) => $query->where('is_published', true),
                    )
                    ->schema([
                        TextInput::make('title'),
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

describe('rendering', function (): void {
    it('can render with `addable(false)`', function (): void {
        livewire(RenderRepeaterWithNotAddable::class)->assertSuccessful();
    });

    it('can render with `addable()` set via `Closure`', function (): void {
        livewire(RenderRepeaterWithClosureAddable::class)->assertSuccessful();
    });

    it('can render with `deletable()` set via `Closure`', function (): void {
        livewire(RenderRepeaterWithClosureDeletable::class)->assertSuccessful();
    });

    it('can render with `collapsible()`', function (): void {
        livewire(RenderRepeaterWithCollapsible::class)->assertSuccessful();
    });

    it('can render with `itemNumbers()`', function (): void {
        livewire(RenderRepeaterWithItemNumbers::class)->assertSuccessful();
    });

    it('can render with `itemHeaders(false)`', function (): void {
        livewire(RenderRepeaterWithNoItemHeaders::class)->assertSuccessful();
    });

    it('can render with `truncateItemLabel()` set via `Closure`', function (): void {
        livewire(RenderRepeaterWithClosureTruncateLabel::class)->assertSuccessful();
    });

    it('can render with `reorderableWithDragAndDrop(false)`', function (): void {
        livewire(RenderRepeaterWithNoDragAndDrop::class)->assertSuccessful();
    });

    it('can render with `reorderableWithButtons()`', function (): void {
        livewire(RenderRepeaterWithReorderButtons::class)->assertSuccessful();
    });

    it('can render with `reorderable(false)`', function (): void {
        livewire(RenderRepeaterWithNotReorderable::class)->assertSuccessful();
    });

    it('can render with `reorderable()` set via `Closure`', function (): void {
        livewire(RenderRepeaterWithClosureReorderable::class)->assertSuccessful();
    });

    it('can render with `cloneable()`', function (): void {
        livewire(RenderRepeaterWithCloneable::class)->assertSuccessful();
    });

    it('can render with `labelBetweenItems()`', function (): void {
        livewire(RenderRepeaterWithLabelBetweenItems::class)->assertSuccessful();
    });

    it('can render with `addActionAlignment()`', function (): void {
        livewire(RenderRepeaterWithAddActionAlignment::class)->assertSuccessful();
    });

    it('can render with `addActionLabel()` set via `Closure`', function (): void {
        livewire(RenderRepeaterWithClosureAddActionLabel::class)->assertSuccessful();
    });

    it('can render with `simple()` view', function (): void {
        livewire(RenderRepeaterWithSimple::class)->assertSuccessful();
    });

    it('can render with `table()` view', function (): void {
        livewire(RenderRepeaterWithTable::class)->assertSuccessful();
    });
});

it('can add and delete items in the browser', function (): void {
    retry(10, function (): void {
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
});

describe('nested singular relationships', function (): void {
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
});

describe('hydrateItems branches', function (): void {
    it('rekeys items with numeric keys when `generateUuidUsing(false)` is set', function (): void {
        $undoFake = Repeater::fake();

        livewire(TestComponentWithRepeaterFilledFromMount::class, [
            'initialData' => [
                ['title' => 'a'],
                ['title' => 'b'],
            ],
        ])->tap(function ($livewire): void {
            $items = $livewire->get('data.items');

            expect($items)->toHaveCount(2);
            expect(array_keys($items))->toBe([0, 1]);
        });

        $undoFake();
    });

    it('produces an empty array when `hydrateItems()` runs against empty `rawState`', function (): void {
        livewire(TestComponentWithRepeaterFilledFromMount::class, ['initialData' => []])
            ->tap(function ($livewire): void {
                expect($livewire->get('data.items'))->toBe([]);
            });
    });

    it('produces an empty array when `hydrateItems()` runs against `null` `rawState`', function (): void {
        livewire(TestComponentWithRepeaterFilledFromMount::class, ['initialData' => null])
            ->tap(function ($livewire): void {
                expect($livewire->get('data.items'))->toBe([]);
            });
    });

    it('returns early from `hydrateItems()` when `hydratedDefaultState` is already an array', function (): void {
        // A repeater with defaultItems(2) and no provided initialData hydrates two empty defaults.
        // `hydratedDefaultState` is set to that array, which short-circuits the rekey loop.
        livewire(TestComponentWithRepeaterDefaultItems::class)
            ->tap(function ($livewire): void {
                $items = $livewire->get('data.items');

                expect($items)->toHaveCount(2);
            });
    });
});

describe('saveToRelationship branches', function (): void {
    it('writes the order column when `orderColumn()` is set', function (): void {
        $user = User::factory()->create();

        livewire(RepeaterWithHasManyRelationshipAndOrderColumn::class, ['record' => $user])
            ->fillForm([
                'posts' => [
                    ['title' => 'First', 'rating' => 0],
                    ['title' => 'Second', 'rating' => 0],
                    ['title' => 'Third', 'rating' => 0],
                ],
            ])
            ->call('save');

        $posts = $user->fresh()->posts->sortBy('rating')->values();
        expect($posts)->toHaveCount(3);
        expect($posts->pluck('rating')->all())->toBe([1, 2, 3]);
        expect($posts->pluck('title')->all())->toBe(['First', 'Second', 'Third']);
    });

    it('skips updating an existing record when `mutateRelationshipDataBeforeSave()` returns null', function (): void {
        $user = User::factory()->create();
        $post = Post::factory()->create(['author_id' => $user->id, 'title' => 'Original Title']);

        livewire(RepeaterWithMutateBeforeSaveReturnsNull::class, ['record' => $user])
            ->fillForm([
                'posts' => [
                    "record-{$post->id}" => ['title' => 'New Title'],
                ],
            ])
            ->call('save');

        // The mutate callback returned null, so the record stayed at "Original Title"
        expect($post->fresh()->title)->toBe('Original Title');
    });

    it('skips creating a new record when `mutateRelationshipDataBeforeCreate()` returns null', function (): void {
        $user = User::factory()->create();

        livewire(RepeaterWithMutateBeforeCreateReturnsNull::class, ['record' => $user])
            ->fillForm([
                'posts' => [
                    ['title' => 'Should be skipped'],
                ],
            ])
            ->call('save');

        expect($user->fresh()->posts)->toHaveCount(0);
    });

    it('calls translatable content driver when `getFilamentTranslatableContentDriver()` is set', function (): void {
        $user = User::factory()->create();
        $existingPost = Post::factory()->create(['author_id' => $user->id, 'title' => 'Original']);

        RecordingTranslatableContentDriver::reset();

        livewire(RepeaterWithTranslatableContentDriver::class, ['record' => $user])
            ->fillForm([
                'posts' => [
                    "record-{$existingPost->id}" => ['title' => 'Updated'],
                    ['title' => 'New Post'],
                ],
            ])
            ->call('save');

        $log = RecordingTranslatableContentDriver::$callLog;

        expect($log)->toContain('updateRecord:Updated');
        expect($log)->toContain('makeRecord:New Post');
    });
});

describe('saveRelationshipsUsing closure paths', function (): void {
    it('creates new records when items are added', function (): void {
        $user = User::factory()->create();

        $undoRepeaterFake = Repeater::fake();

        livewire(RepeaterWithNestedSingularRelationship::class, ['record' => $user])
            ->fillForm([
                'posts' => [
                    ['title' => 'First Post', 'metadata' => ['seo_title' => '']],
                    ['title' => 'Second Post', 'metadata' => ['seo_title' => '']],
                ],
            ])
            ->call('save');

        $undoRepeaterFake();

        expect($user->fresh()->posts)->toHaveCount(2);
        expect($user->fresh()->posts->pluck('title')->sort()->values()->all())->toBe(['First Post', 'Second Post']);
    });

    it('deletes removed records via Schema API', function (): void {
        $user = User::factory()->create();
        $posts = Post::factory()->count(3)->create(['author_id' => $user->id]);

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Repeater('posts'))
                    ->relationship('posts')
                    ->schema([
                        TextInput::make('title'),
                    ]),
            ])
            ->model($user);

        $schema->loadStateFromRelationships();

        // Remove two items by keeping only the first
        $state = $schema->getRawState()['posts'];
        $firstKey = array_key_first($state);
        $schema->rawState(['posts' => [$firstKey => $state[$firstKey]]]);

        $schema->saveRelationships();

        expect($user->fresh()->posts)->toHaveCount(1);
    });

    it('updates existing records when items are modified', function (): void {
        $user = User::factory()->create();
        $post = Post::factory()->create(['author_id' => $user->id, 'title' => 'Original Title']);

        $undoRepeaterFake = Repeater::fake();

        livewire(RepeaterWithNestedSingularRelationship::class, ['record' => $user])
            ->fillForm([
                'posts' => [
                    "record-{$post->id}" => [
                        'title' => 'Updated Title',
                        'metadata' => ['seo_title' => ''],
                    ],
                ],
            ])
            ->call('save');

        $undoRepeaterFake();

        expect($post->fresh()->title)->toBe('Updated Title');
    });
});

describe('shared `statePath` `Group` components', function (): void {
    it('preserves visible sibling `Group` state when a hidden `Group` shares the same `statePath`', function (): void {
        $user = User::factory()->create();
        $existingPost = Post::factory()->create([
            'author_id' => $user->id,
        ]);

        $component = livewire(RepeaterSharedStatePathOneHiddenOneVisible::class, ['record' => $user]);

        $existingPostKey = array_key_first($component->get('data.posts'));

        $undoRepeaterFake = Repeater::fake();

        $component
            ->fillForm([
                'posts' => [
                    $existingPostKey => [
                        'config' => [
                            'internal_notes' => 'Do not publish',
                            'seo_title' => 'New SEO Title',
                        ],
                    ],
                ],
            ])
            ->call('save');

        $undoRepeaterFake();

        $existingPost->refresh();

        expect($existingPost->config)
            ->not->toHaveKey('internal_notes')
            ->toHaveKey('seo_title', 'New SEO Title');
    });

    it('does not save hidden fields when all sibling `Group` components sharing a `statePath` are hidden', function (): void {
        $user = User::factory()->create();
        $existingPost = Post::factory()->create([
            'author_id' => $user->id,
            'config' => ['seo_title' => 'Old Title', 'internal_notes' => 'Old Notes'],
        ]);

        $component = livewire(RepeaterSharedStatePathBothHidden::class, ['record' => $user]);

        $existingPostKey = array_key_first($component->get('data.posts'));

        $undoRepeaterFake = Repeater::fake();

        $component
            ->fillForm([
                'posts' => [
                    $existingPostKey => [
                        'title' => 'Updated Title',
                        'config' => [
                            'internal_notes' => 'Attempted override',
                            'seo_title' => 'Attempted override',
                        ],
                    ],
                ],
            ])
            ->call('save');

        $undoRepeaterFake();

        $existingPost->refresh();

        // The visible `title` field saves normally.
        expect($existingPost->title)->toBe('Updated Title');

        // Both `Group` components sharing `config` are hidden, so neither
        // field is dehydrated. The original database value is preserved
        // because Eloquent's `fill()` does not touch keys absent from
        // the dehydrated data.
        expect($existingPost->config)
            ->toBe(['seo_title' => 'Old Title', 'internal_notes' => 'Old Notes']);
    });

    it('preserves shared `statePath` state when `Group` components are in different `Section` components', function (): void {
        $user = User::factory()->create();
        $existingPost = Post::factory()->create([
            'author_id' => $user->id,
        ]);

        $component = livewire(RepeaterSharedStatePathInsideSection::class, ['record' => $user]);

        $existingPostKey = array_key_first($component->get('data.posts'));

        $undoRepeaterFake = Repeater::fake();

        $component
            ->fillForm([
                'posts' => [
                    $existingPostKey => [
                        'config' => [
                            'internal_notes' => 'Secret',
                            'seo_title' => 'Visible Title',
                        ],
                    ],
                ],
            ])
            ->call('save');

        $undoRepeaterFake();

        $existingPost->refresh();

        expect($existingPost->config)
            ->not->toHaveKey('internal_notes')
            ->toHaveKey('seo_title', 'Visible Title');
    });

    it('does not leak relationship data into `getState()` return value', function (): void {
        $user = User::factory()->create();
        Post::factory()->create([
            'author_id' => $user->id,
            'title' => 'Existing Post',
        ]);

        $component = livewire(RepeaterGetStateLeakCheck::class, ['record' => $user]);

        $undoRepeaterFake = Repeater::fake();

        $component->call('saveAndCapture');

        $undoRepeaterFake();

        $captured = $component->get('capturedState');

        expect($captured)->not->toHaveKey('posts');
    });

    it('preserves shared `statePath` state with deeply nested `Group` components', function (): void {
        $user = User::factory()->create();
        $existingPost = Post::factory()->create([
            'author_id' => $user->id,
        ]);

        $component = livewire(RepeaterSharedStatePathDeeplyNested::class, ['record' => $user]);

        $existingPostKey = array_key_first($component->get('data.posts'));

        $undoRepeaterFake = Repeater::fake();

        $component
            ->fillForm([
                'posts' => [
                    $existingPostKey => [
                        'config' => [
                            'admin_note' => 'Hidden deep note',
                            'seo_title' => 'Deep SEO Title',
                        ],
                    ],
                ],
            ])
            ->call('save');

        $undoRepeaterFake();

        $existingPost->refresh();

        expect($existingPost->config)
            ->not->toHaveKey('admin_note')
            ->toHaveKey('seo_title', 'Deep SEO Title');
    });
});

class RepeaterSharedStatePathOneHiddenOneVisible extends Component implements HasSchemas
{
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
                        Group::make([
                            TextInput::make('internal_notes'),
                        ])
                            ->hidden()
                            ->statePath('config'),
                        Group::make([
                            TextInput::make('seo_title'),
                        ])
                            ->statePath('config'),
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

class RepeaterSharedStatePathBothHidden extends Component implements HasSchemas
{
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
                        TextInput::make('title'),
                        Group::make([
                            TextInput::make('internal_notes'),
                        ])
                            ->hidden()
                            ->statePath('config'),
                        Group::make([
                            TextInput::make('seo_title'),
                        ])
                            ->hidden()
                            ->statePath('config'),
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

class RepeaterSharedStatePathInsideSection extends Component implements HasSchemas
{
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
                        Section::make('Internal')
                            ->schema([
                                Group::make([
                                    TextInput::make('internal_notes'),
                                ])
                                    ->hidden()
                                    ->statePath('config'),
                            ]),
                        Section::make('Public')
                            ->schema([
                                Group::make([
                                    TextInput::make('seo_title'),
                                ])
                                    ->statePath('config'),
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

class RepeaterGetStateLeakCheck extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public array $data = [];

    public array $capturedState = [];

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

    public function saveAndCapture(): void
    {
        $this->capturedState = $this->form->getState();
        $this->form->saveRelationships();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RepeaterSharedStatePathDeeplyNested extends Component implements HasSchemas
{
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
                        Group::make([
                            Group::make([
                                TextInput::make('admin_note'),
                            ]),
                        ])
                            ->hidden()
                            ->statePath('config'),
                        Group::make([
                            Group::make([
                                TextInput::make('seo_title'),
                            ]),
                        ])
                            ->statePath('config'),
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

describe('properties', function (): void {
    it('can set `addActionLabel()` and get with `getAddActionLabel()`', function (): void {
        $repeater = Repeater::make('items')
            ->addActionLabel('Add new item');

        expect($repeater->getAddActionLabel())->toBe('Add new item');
    });

    it('can set `addBetweenActionLabel()` and get with `getAddBetweenActionLabel()`', function (): void {
        $repeater = Repeater::make('items')
            ->addBetweenActionLabel('Insert between');

        expect($repeater->getAddBetweenActionLabel())->toBe('Insert between');
    });

    it('can set `labelBetweenItems()` and get with `getLabelBetweenItems()`', function (): void {
        $repeater = Repeater::make('items')
            ->labelBetweenItems('or');

        expect($repeater->getLabelBetweenItems())->toBe('or');
    });

    it('returns `null` for `getLabelBetweenItems()` by default', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->getLabelBetweenItems())->toBeNull();
    });

    it('can set `itemNumbers()` and check `hasItemNumbers()`', function (): void {
        $withNumbers = Repeater::make('items')->itemNumbers();
        $withoutNumbers = Repeater::make('items')->itemNumbers(false);

        expect($withNumbers->hasItemNumbers())->toBeTrue();
        expect($withoutNumbers->hasItemNumbers())->toBeFalse();
    });

    it('has `hasItemNumbers()` returning false by default', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->hasItemNumbers())->toBeFalse();
    });

    it('can set `itemHeaders()` and check `hasItemHeaders()`', function (): void {
        $withHeaders = Repeater::make('items')->itemHeaders();
        $withoutHeaders = Repeater::make('items')->itemHeaders(false);

        expect($withHeaders->hasItemHeaders())->toBeTrue();
        expect($withoutHeaders->hasItemHeaders())->toBeFalse();
    });

    it('has `hasItemHeaders()` returning true by default', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->hasItemHeaders())->toBeTrue();
    });

    it('can set `truncateItemLabel()` and check `isItemLabelTruncated()`', function (): void {
        $truncated = Repeater::make('items')->truncateItemLabel();
        $notTruncated = Repeater::make('items')->truncateItemLabel(false);

        expect($truncated->isItemLabelTruncated())->toBeTrue();
        expect($notTruncated->isItemLabelTruncated())->toBeFalse();
    });

    it('has `isItemLabelTruncated()` returning true by default', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->isItemLabelTruncated())->toBeTrue();
    });

    it('can set `partiallyRenderAfterActionsCalled()` and check `shouldPartiallyRenderAfterActionsCalled()`', function (): void {
        $enabled = Repeater::make('items')->partiallyRenderAfterActionsCalled();
        $disabled = Repeater::make('items')->partiallyRenderAfterActionsCalled(false);
        $liveEnabled = Repeater::make('items')->live()->partiallyRenderAfterActionsCalled();

        expect($enabled->shouldPartiallyRenderAfterActionsCalled())->toBeTrue();
        expect($disabled->shouldPartiallyRenderAfterActionsCalled())->toBeFalse();
        expect($liveEnabled->shouldPartiallyRenderAfterActionsCalled())->toBeTrue();
    });

    it('defaults `shouldPartiallyRenderAfterActionsCalled()` based on `live()`', function (): void {
        [$default, $live, $conditionallyLive, $conditionallyNotLive] = Schema::make(Livewire::make())
            ->components([
                Repeater::make('default'),
                Repeater::make('live')->live(),
                Repeater::make('conditionallyLive')->live(condition: static fn (): bool => true),
                Repeater::make('conditionallyNotLive')->live(condition: static fn (): bool => false),
            ])
            ->getComponents();

        expect($default->shouldPartiallyRenderAfterActionsCalled())->toBeTrue();
        expect($live->shouldPartiallyRenderAfterActionsCalled())->toBeFalse();
        expect($conditionallyLive->shouldPartiallyRenderAfterActionsCalled())->toBeFalse();
        expect($conditionallyNotLive->shouldPartiallyRenderAfterActionsCalled())->toBeTrue();
    });

    it('can set `addActionAlignment()` and get with `getAddActionAlignment()`', function (): void {
        $repeater = Repeater::make('items')
            ->addActionAlignment('center');

        expect($repeater->getAddActionAlignment())->toBe(Alignment::Center);
    });

    it('returns `null` for `getAddActionAlignment()` by default', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->getAddActionAlignment())->toBeNull();
    });

    it('returns `null` for `getRelationshipName()` when no relationship set', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->getRelationshipName())->toBeNull();
    });

    it('can check `hasRelationship()` returns false when no relationship is set', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->hasRelationship())->toBeFalse();
    });

    it('can check `hasItemLabels()` returns false when `itemLabel()` is not set', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->hasItemLabels())->toBeFalse();
    });

    it('can check `hasItemLabels()` returns true when `itemLabel()` is set', function (): void {
        $repeater = Repeater::make('items')
            ->itemLabel(static fn (): string => 'Item');

        expect($repeater->hasItemLabels())->toBeTrue();
    });

    it('can check `isSimple()` returns false by default', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->isSimple())->toBeFalse();
    });

    it('can return correct action names', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->getAddActionName())->toBe('add');
        expect($repeater->getAddBetweenActionName())->toBe('addBetween');
        expect($repeater->getCloneActionName())->toBe('clone');
        expect($repeater->getDeleteActionName())->toBe('delete');
        expect($repeater->getMoveDownActionName())->toBe('moveDown');
        expect($repeater->getMoveUpActionName())->toBe('moveUp');
        expect($repeater->getReorderActionName())->toBe('reorder');
        expect($repeater->getCollapseActionName())->toBe('collapse');
        expect($repeater->getExpandActionName())->toBe('expand');
        expect($repeater->getCollapseAllActionName())->toBe('collapseAll');
        expect($repeater->getExpandAllActionName())->toBe('expandAll');
    });
});

it('returns fluent `$this` from `defaultItems()`', function (): void {
    $repeater = Repeater::make('items');

    $result = $repeater->defaultItems(3);

    expect($result)->toBe($repeater);
});

it('can set `orderColumn()` and get with `getOrderColumn()`', function (): void {
    $repeater = Repeater::make('items');

    expect($repeater->getOrderColumn())->toBeNull();

    $repeater->orderColumn('sort');

    expect($repeater->getOrderColumn())->toBe('sort');
});

it('uses `sort` as default column for `orderColumn()` when called without argument', function (): void {
    $repeater = Repeater::make('items')
        ->orderColumn();

    expect($repeater->getOrderColumn())->toBe('sort');
});

it('can set `table()` columns and check `isTable()`', function (): void {
    $repeater = Repeater::make('items');

    expect($repeater->isTable())->toBeFalse();
    expect($repeater->getTableColumns())->toBeNull();

    $columns = [
        Repeater\TableColumn::make('name'),
        Repeater\TableColumn::make('email'),
    ];

    $repeater->table($columns);

    expect($repeater->isTable())->toBeTrue();
    expect($repeater->getTableColumns())->toHaveCount(2);
});

it('returns `false` for `canConcealComponents()` when not collapsible', function (): void {
    $repeater = Repeater::make('items');

    expect($repeater->canConcealComponents())->toBeFalse();
});

it('returns `true` for `canConcealComponents()` when collapsible', function (): void {
    $repeater = Repeater::make('items')
        ->collapsible();

    expect($repeater->canConcealComponents())->toBeTrue();
});

it('returns `0` for `getHeadingsCount()` by default', function (): void {
    $repeater = Repeater::make('items');

    expect($repeater->getHeadingsCount())->toBe(0);
});

it('returns fluent `$this` from lifecycle hook methods', function (): void {
    $repeater = Repeater::make('items');

    expect($repeater->mutateRelationshipDataBeforeCreateUsing(static fn (array $data): array => $data))->toBe($repeater);
    expect($repeater->mutateRelationshipDataBeforeSaveUsing(static fn (array $data): array => $data))->toBe($repeater);
    expect($repeater->mutateRelationshipDataBeforeFillUsing(static fn (array $data): array => $data))->toBe($repeater);
    expect($repeater->afterCreate(static fn () => null))->toBe($repeater);
    expect($repeater->afterUpdate(static fn () => null))->toBe($repeater);
    expect($repeater->afterDelete(static fn () => null))->toBe($repeater);
});

it('can set `addActionLabel()` with a `Closure`', function (): void {
    $repeater = Repeater::make('items')
        ->addActionLabel(static fn (): string => 'Add new item');

    expect($repeater->getAddActionLabel())->toBe('Add new item');
});

it('can set `truncateItemLabel()` with a `Closure`', function (): void {
    $repeater = Repeater::make('items')
        ->truncateItemLabel(static fn (): bool => false);

    expect($repeater->isItemLabelTruncated())->toBeFalse();
});

describe('boolean properties with defaults and Closure', function (): void {
    it('defaults `isAddable()` to `true`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items'),
            ])
            ->fill();

        expect($repeater->isAddable())->toBeTrue();
    });

    it('can set `addable()` to `false`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items')->addable(false),
            ])
            ->fill();

        expect($repeater->isAddable())->toBeFalse();
    });

    it('can set `addable()` with a `Closure`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items')
                    ->addable(static fn (): bool => false),
            ])
            ->fill();

        expect($repeater->isAddable())->toBeFalse();
    });

    it('returns `false` for `isAddable()` when disabled', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items')->disabled(),
            ])
            ->fill();

        expect($repeater->isAddable())->toBeFalse();
    });

    it('returns `false` for `isAddable()` when `maxItems()` is reached', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items')
                    ->maxItems(2)
                    ->default([['name' => 'a'], ['name' => 'b']]),
            ])
            ->fill();

        expect($repeater->isAddable())->toBeFalse();
    });

    it('defaults `isDeletable()` to `true`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items'),
            ])
            ->fill();

        expect($repeater->isDeletable())->toBeTrue();
    });

    it('can set `deletable()` with a `Closure`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items')
                    ->deletable(static fn (): bool => false),
            ])
            ->fill();

        expect($repeater->isDeletable())->toBeFalse();
    });

    it('returns `false` for `isDeletable()` when disabled', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items')->disabled(),
            ])
            ->fill();

        expect($repeater->isDeletable())->toBeFalse();
    });

    it('defaults `isReorderable()` to `true`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items'),
            ])
            ->fill();

        expect($repeater->isReorderable())->toBeTrue();
    });

    it('can set `reorderable()` with a `Closure`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items')
                    ->reorderable(static fn (): bool => false),
            ])
            ->fill();

        expect($repeater->isReorderable())->toBeFalse();
    });

    it('returns `false` for `isReorderable()` when disabled', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items')->disabled(),
            ])
            ->fill();

        expect($repeater->isReorderable())->toBeFalse();
    });

    it('can set `reorderableWithButtons()` and `reorderableWithDragAndDrop()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items')
                    ->reorderableWithButtons()
                    ->reorderableWithDragAndDrop(false),
            ])
            ->fill();

        expect($repeater->isReorderableWithButtons())->toBeTrue();
        expect($repeater->isReorderableWithDragAndDrop())->toBeFalse();
    });
});

describe('item labels', function (): void {
    it('can set `itemLabel()` with a `Closure`', function (): void {
        $repeater = Repeater::make('items')
            ->itemLabel(static fn (?array $state): string => $state['name'] ?? 'Unnamed');

        expect($repeater->hasItemLabels())->toBeTrue();
    });

    it('returns `null` for `getItemLabel()` when not set', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items'),
            ])
            ->fill();

        expect($repeater->hasItemLabels())->toBeFalse();
    });

    it('injects the `$index` of the item into the `itemLabel()` `Closure`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items')
                    ->schema([
                        TextInput::make('name'),
                    ])
                    ->itemLabel(static fn (int $index, array $state): string => "Item {$index}: {$state['name']}")
                    ->default([
                        ['name' => 'first'],
                        ['name' => 'second'],
                        ['name' => 'third'],
                    ]),
            ])
            ->fill();

        $itemKeys = array_keys($repeater->getRawState());

        expect($repeater->getItemLabel($itemKeys[0], 0))->toBe('Item 0: first');
        expect($repeater->getItemLabel($itemKeys[1], 1))->toBe('Item 1: second');
        expect($repeater->getItemLabel($itemKeys[2], 2))->toBe('Item 2: third');
    });

    it('can set `itemNumbers()` with a `Closure`', function (): void {
        $repeater = Repeater::make('items')
            ->itemNumbers(static fn (): bool => true);

        expect($repeater->hasItemNumbers())->toBeTrue();
    });

    it('can set `itemHeaders()` to `false`', function (): void {
        $repeater = Repeater::make('items')
            ->itemHeaders(false);

        expect($repeater->hasItemHeaders())->toBeFalse();
    });
});

describe('simple mode', function (): void {
    it('can enable `simple()` with a field', function (): void {
        $repeater = Repeater::make('tags')
            ->simple(TextInput::make('tag'));

        expect($repeater->isSimple())->toBeTrue();
        expect($repeater->getSimpleField())->toBeInstanceOf(TextInput::class);
    });

    it('can disable `simple()` with `null`', function (): void {
        $repeater = Repeater::make('tags')
            ->simple(TextInput::make('tag'))
            ->simple(null);

        expect($repeater->isSimple())->toBeFalse();
    });
});

describe('order column', function (): void {
    it('can set `orderColumn()` with a `Closure`', function (): void {
        $repeater = Repeater::make('items')
            ->orderColumn(static fn (): string => 'position');

        expect($repeater->getOrderColumn())->toBe('position');
    });

    it('returns `null` for `getOrderColumn()` by default', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->getOrderColumn())->toBeNull();
    });
});

describe('relationship name', function (): void {
    it('uses field name when `relationship()` name is `null`', function (): void {
        $repeater = Repeater::make('posts')
            ->relationship();

        expect($repeater->getRelationshipName())->toBe('posts');
    });

    it('uses explicit name when provided to `relationship()`', function (): void {
        $repeater = Repeater::make('post_items')
            ->relationship('posts');

        expect($repeater->getRelationshipName())->toBe('posts');
    });
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

class RenderRepeaterWithNotAddable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->addable(false),
        ])->statePath('data');
    }
}

class RenderRepeaterWithClosureAddable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->addable(static fn (): bool => false),
        ])->statePath('data');
    }
}

class RenderRepeaterWithClosureDeletable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->deletable(static fn (): bool => false),
        ])->statePath('data');
    }
}

class RenderRepeaterWithCollapsible extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->collapsible(),
        ])->statePath('data');
    }
}

class RenderRepeaterWithItemNumbers extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->itemNumbers(),
        ])->statePath('data');
    }
}

class RenderRepeaterWithNoItemHeaders extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->itemHeaders(false),
        ])->statePath('data');
    }
}

class RenderRepeaterWithClosureTruncateLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->truncateItemLabel(static fn (): bool => false),
        ])->statePath('data');
    }
}

class RenderRepeaterWithNoDragAndDrop extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->reorderableWithDragAndDrop(false),
        ])->statePath('data');
    }
}

class RenderRepeaterWithReorderButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->reorderableWithButtons(),
        ])->statePath('data');
    }
}

class RenderRepeaterWithNotReorderable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->reorderable(false),
        ])->statePath('data');
    }
}

class RenderRepeaterWithClosureReorderable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->reorderable(static fn (): bool => false),
        ])->statePath('data');
    }
}

class RenderRepeaterWithCloneable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->cloneable(),
        ])->statePath('data');
    }
}

class RenderRepeaterWithLabelBetweenItems extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->labelBetweenItems('or'),
        ])->statePath('data');
    }
}

class RenderRepeaterWithAddActionAlignment extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->addActionAlignment('center'),
        ])->statePath('data');
    }
}

class RenderRepeaterWithClosureAddActionLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->schema([TextInput::make('name')])->addActionLabel(static fn (): string => 'Add new item'),
        ])->statePath('data');
    }
}

class RenderRepeaterWithSimple extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')->simple(TextInput::make('name')),
        ])->statePath('data');
    }
}

class RenderRepeaterWithTable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Repeater::make('items')
                ->schema([TextInput::make('name'), TextInput::make('email')])
                ->table([
                    Repeater\TableColumn::make('name'),
                    Repeater\TableColumn::make('email'),
                ]),
        ])->statePath('data');
    }
}

class TestComponentWithRepeaterFilledFromMount extends Livewire
{
    public mixed $initialData = null;

    public function mount(): void
    {
        $this->form->fill(['items' => $this->initialData]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Repeater::make('items')
                    ->schema([
                        TextInput::make('title'),
                    ]),
            ])
            ->statePath('data');
    }
}

class TestComponentWithRepeaterDefaultItems extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Repeater::make('items')
                    ->schema([
                        TextInput::make('title'),
                    ])
                    ->defaultItems(2),
            ])
            ->statePath('data');
    }
}

class RepeaterWithHasManyRelationshipAndOrderColumn extends Component implements HasActions, HasSchemas
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
                    ->orderColumn('rating')
                    ->schema([
                        TextInput::make('title'),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RepeaterWithMutateBeforeSaveReturnsNull extends Component implements HasActions, HasSchemas
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
                    ->mutateRelationshipDataBeforeSaveUsing(static fn (?array $data): ?array => null)
                    ->schema([
                        TextInput::make('title'),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RepeaterWithMutateBeforeCreateReturnsNull extends Component implements HasActions, HasSchemas
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
                    ->mutateRelationshipDataBeforeCreateUsing(static fn (?array $data): ?array => null)
                    ->schema([
                        TextInput::make('title'),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RepeaterWithTranslatableContentDriver extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function getFilamentTranslatableContentDriver(): ?string
    {
        return RecordingTranslatableContentDriver::class;
    }

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

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}
