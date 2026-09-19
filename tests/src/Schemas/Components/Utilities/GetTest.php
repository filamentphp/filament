<?php

use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Enums\IntegerBackedEnum;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

describe('state retrieval with `Get`', function (): void {
    test('sibling state can be retrieved relatively from another component', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath($statePath = Str::random())
                    ->default($state = Str::random()),
                $placeholder = Placeholder::make(Str::random())
                    ->content(fn (Get $get): string => $get($statePath)),
            ])
            ->fill();

        expect($placeholder)
            ->getContent()->toBe($state);
    });

    test('sibling nested state can be retrieved relatively from another component', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath($parentStatePath = Str::random())
                    ->schema([
                        (new Component)
                            ->statePath($statePath = Str::random())
                            ->default($state = Str::random()),
                    ]),
                $placeholder = Placeholder::make(Str::random())
                    ->content(fn (Get $get): string => $get("{$parentStatePath}.{$statePath}")),
            ])
            ->fill();

        expect($placeholder)
            ->getContent()->toBe($state);
    });

    test('casted state can be retrieved from a child component', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                Tabs::make()
                    ->tabs([
                        $parentTab = Tab::make('Parent')
                            ->badge(fn (Get $get): string => get_debug_type($get('status')))
                            ->schema([
                                Select::make('status')
                                    ->options(StringBackedEnum::class),
                            ]),
                        $siblingTab = Tab::make('Sibling')
                            ->badge(fn (Get $get): string => get_debug_type($get('status'))),
                    ]),
            ])
            ->fill(['status' => StringBackedEnum::One->value]);

        expect($parentTab->getBadge())
            ->toBe(StringBackedEnum::class)
            ->and($siblingTab->getBadge())
            ->toBe(StringBackedEnum::class);
    });

    test('component lookups are cached while child schemas remain unchanged', function (): void {
        $targetComponent = new class('target') extends TextInput
        {
            public int $statePathRetrievalCount = 0;

            public function getStatePath(bool $isAbsolute = true): ?string
            {
                $this->statePathRetrievalCount++;

                return parent::getStatePath($isAbsolute);
            }
        };

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $sourceComponent = TextInput::make('source'),
                $targetComponent,
                TextInput::make('unrelated'),
            ])
            ->fill([
                'target' => 'value',
            ]);

        $targetComponent->statePathRetrievalCount = 0;
        $get = $sourceComponent->makeGetUtility();

        expect($get('target'))->toBe('value');

        $statePathRetrievalCount = $targetComponent->statePathRetrievalCount;

        $sourceComponent->makeSetUtility()('unrelated', 'updated');
        $statePathRetrievalCountAfterUnrelatedWrite = $targetComponent->statePathRetrievalCount;

        expect($get('target'))
            ->toBe('value')
            ->and($targetComponent->statePathRetrievalCount)
            ->toBe($statePathRetrievalCountAfterUnrelatedWrite + 1)
            ->and($statePathRetrievalCountAfterUnrelatedWrite)
            ->toBe($statePathRetrievalCount + 1);
    });

    test('uncached dynamic child schemas are skipped while retrieving state', function (): void {
        $firstSchemaEvaluationCount = 0;
        $secondSchemaEvaluationCount = 0;

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->schema(function (Get $get) use (&$firstSchemaEvaluationCount): array {
                        $firstSchemaEvaluationCount++;
                        $get('firstMissingState');
                        $get('secondMissingState');

                        return [];
                    }),
                (new Component)
                    ->schema(function (Get $get) use (&$secondSchemaEvaluationCount): array {
                        $secondSchemaEvaluationCount++;
                        $get('thirdMissingState');
                        $get('fourthMissingState');

                        return [];
                    }),
            ]);

        $schema->getComponents()[0]->getChildSchemas();

        expect($firstSchemaEvaluationCount)
            ->toBe(1)
            ->and($secondSchemaEvaluationCount)
            ->toBe(1);
    });

    test('uncached nested child schemas are skipped while retrieving state', function (): void {
        $schemaEvaluationCount = 0;

        $parentComponent = Tab::make('Parent')
            ->badge(fn (Get $get): mixed => $get('missingState'))
            ->schema([
                (new Component)
                    ->hiddenWhenAllChildComponentsHidden()
                    ->schema(function (Get $get) use (&$schemaEvaluationCount): array {
                        $schemaEvaluationCount++;
                        $get('missingState');

                        return [];
                    }),
            ]);

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([$parentComponent]);

        $schema->getComponents();
        $parentComponent->getChildSchemas(withHidden: true)['default']->getComponents(withHidden: true);
        $parentComponent->getBadge();

        expect($schemaEvaluationCount)->toBe(0);
    });

    test('cached child schemas with uncached components are skipped while retrieving state', function (): void {
        $schemaEvaluationCount = 0;
        $livewire = Livewire::make();
        $childSchema = Schema::make($livewire)
            ->components(function () use (&$schemaEvaluationCount): array {
                $schemaEvaluationCount++;

                return [];
            });

        $parentComponent = Tab::make('Parent')
            ->badge(fn (Get $get): mixed => $get('missingState'))
            ->schema($childSchema);

        Schema::make($livewire)
            ->statePath('data')
            ->components([$parentComponent])
            ->getComponents();

        $parentComponent->getChildSchemas(withHidden: true);
        $parentComponent->getBadge();

        expect($schemaEvaluationCount)->toBe(0);
    });

    test('direct default child schema construction does not expose previously cached children to its schema `Closure`', function (): void {
        $livewire = Livewire::make();
        $retrievedStates = [];

        $group = Group::make()
            ->schema(function (Get $get) use (&$retrievedStates): array {
                $retrievedStates[] = $get('status');

                return [
                    Select::make('status')->options(StringBackedEnum::class),
                ];
            });

        Schema::make($livewire)
            ->statePath('data')
            ->components([$group])
            ->fill(['status' => StringBackedEnum::One->value]);

        $group->getChildSchema()->getComponents();

        expect($retrievedStates)
            ->not->toBeEmpty()
            ->each->toBe(StringBackedEnum::One->value);
    });

    test('default child schema construction can retry after its schema `Closure` throws', function (): void {
        $shouldThrow = true;
        $group = Group::make()
            ->schema(function () use (&$shouldThrow): array {
                if ($shouldThrow) {
                    $shouldThrow = false;

                    throw new RuntimeException('The first schema evaluation failed.');
                }

                return [
                    TextInput::make('name'),
                ];
            })
            ->container(Schema::make(Livewire::make()));

        expect(fn () => $group->getChildSchema())
            ->toThrow(RuntimeException::class, 'The first schema evaluation failed.')
            ->and($group->getChildSchema()?->getComponents())
            ->toHaveCount(1);
    });

    test('casted state is retrieved from rebuilt child components', function (): void {
        $livewire = Livewire::make();

        Schema::make($livewire)
            ->statePath('data')
            ->components([
                $parentComponent = (new Component)
                    ->schema([
                        Select::make('status')->options(StringBackedEnum::class),
                    ]),
            ])
            ->fill(['status' => StringBackedEnum::One->value]);

        $get = $parentComponent->makeGetUtility();

        expect($get('status'))->toBe(StringBackedEnum::One);

        $parentComponent->schema([
            Select::make('status')->options(IntegerBackedEnum::class),
        ]);
        $livewire->data['status'] = IntegerBackedEnum::One->value;
        $parentComponent->getChildSchemas(withHidden: true)['default']->getComponents(withHidden: true);

        expect($get('status'))->toBe(IntegerBackedEnum::One);
    });

    test('action-injected `Get` uses rebuilt nested child components', function (): void {
        $livewire = Livewire::make();

        Schema::make($livewire)
            ->statePath('data')
            ->components([
                (new Component)
                    ->schema([
                        $parentComponent = (new Component)
                            ->schema([
                                Select::make('status')->options(StringBackedEnum::class),
                            ]),
                    ]),
            ])
            ->fill(['status' => StringBackedEnum::One->value]);

        $action = Action::make('readStatus')
            ->schemaComponent($parentComponent)
            ->action(fn (Get $get): mixed => $get('status'));

        expect($action->call())->toBe(StringBackedEnum::One);

        $parentComponent->schema([
            Select::make('status')->options(IntegerBackedEnum::class),
        ]);
        $livewire->data['status'] = IntegerBackedEnum::One->value;

        expect($action->call())->toBe(IntegerBackedEnum::One);
    });

    test('stale builder item schemas are skipped while rebuilding items', function (): void {
        $livewire = Livewire::make();
        $shouldReadState = false;
        $stateDuringRebuild = null;

        $builder = Builder::make('content')
            ->generateUuidUsing(false)
            ->blocks(function (Get $get) use (&$shouldReadState, &$stateDuringRebuild): array {
                if ($shouldReadState) {
                    $stateDuringRebuild = $get('/data.content.0.data.status');
                }

                return [
                    Block::make('enum')->schema([
                        Select::make('status')->options(StringBackedEnum::class),
                    ]),
                    Block::make('text')->schema([
                        Placeholder::make('status'),
                    ]),
                ];
            });

        Schema::make($livewire)
            ->statePath('data')
            ->components([$builder])
            ->fill([
                'content' => [[
                    'type' => 'enum',
                    'data' => ['status' => StringBackedEnum::One->value],
                ]],
            ]);

        $builder->getItems()[0]->getComponents();
        $livewire->data['content'][0]['type'] = 'text';
        $shouldReadState = true;
        $builder->getItems();

        expect($stateDuringRebuild)
            ->toBe(StringBackedEnum::One->value);
    });

    test('builder item schemas do not recursively rebuild when a block schema retrieves state', function (): void {
        $livewire = Livewire::make();
        $schemaEvaluationCount = 0;
        $stateDuringRebuild = null;

        $builder = Builder::make('content')
            ->generateUuidUsing(false)
            ->blocks([
                Block::make('enum')->schema([
                    Select::make('status')->options(StringBackedEnum::class),
                ]),
                Block::make('text')->schema(function (Get $get) use (&$schemaEvaluationCount, &$stateDuringRebuild): array {
                    $schemaEvaluationCount++;

                    if ($schemaEvaluationCount > 5) {
                        throw new RuntimeException('The block schema was evaluated recursively.');
                    }

                    $stateDuringRebuild = $get('/data.content.0.data.status');

                    return [
                        TextInput::make('status'),
                    ];
                }),
            ]);

        Schema::make($livewire)
            ->statePath('data')
            ->components([$builder])
            ->fill([
                'content' => [[
                    'type' => 'enum',
                    'data' => ['status' => StringBackedEnum::One->value],
                ]],
            ]);

        $builder->getItems()[0]->getComponents();
        $livewire->data['content'][0]['type'] = 'text';
        $builder->getItems();

        expect($schemaEvaluationCount)
            ->toBe(1)
            ->and($stateDuringRebuild)
            ->toBe(StringBackedEnum::One->value);
    });

    test('cached state lookups skip stale builder item schemas before rebuilding items', function (): void {
        $livewire = Livewire::make();
        $targetComponentPrototype = new class('status') extends Select
        {
            public int $statePathRetrievalCount = 0;

            public function getStatePath(bool $isAbsolute = true): ?string
            {
                $this->statePathRetrievalCount++;

                return parent::getStatePath($isAbsolute);
            }
        };
        $builder = Builder::make('content')
            ->generateUuidUsing(false)
            ->blocks([
                Block::make('enum')->schema([
                    $targetComponentPrototype->options(StringBackedEnum::class),
                ]),
                Block::make('text')->schema([
                    Placeholder::make('status'),
                ]),
            ]);

        Schema::make($livewire)
            ->statePath('data')
            ->components([
                Tabs::make()
                    ->tabs([
                        $parentTab = Tab::make('Parent')
                            ->schema([$builder]),
                    ]),
            ])
            ->fill([
                'content' => [[
                    'type' => 'enum',
                    'data' => ['status' => StringBackedEnum::One->value],
                ]],
            ]);

        $targetComponent = $builder->getItems()[0]->getComponents()[0];
        $get = $parentTab->makeGetUtility();

        expect($get('/data.content.0.data.status'))->toBe(StringBackedEnum::One);

        $statePathRetrievalCount = invade($targetComponent)->statePathRetrievalCount;

        expect($get('/data.content.0.data.status'))
            ->toBe(StringBackedEnum::One)
            ->and(invade($targetComponent)->statePathRetrievalCount)
            ->toBe($statePathRetrievalCount + 1);

        $livewire->data['content'][0]['type'] = 'text';

        expect($get('/data.content.0.data.status'))->toBe(StringBackedEnum::One->value);
    });

    test('cached sibling state lookups skip stale builder item schemas before rebuilding items', function (): void {
        $livewire = Livewire::make();
        $builder = Builder::make('content')
            ->generateUuidUsing(false)
            ->blocks([
                Block::make('enum')->schema([
                    Select::make('status')->options(StringBackedEnum::class),
                ]),
                Block::make('text')->schema([
                    TextInput::make('status'),
                ]),
            ]);

        Schema::make($livewire)
            ->statePath('data')
            ->components([
                $source = TextInput::make('source'),
                $builder,
            ])
            ->fill([
                'content' => [[
                    'type' => 'enum',
                    'data' => ['status' => StringBackedEnum::One->value],
                ]],
            ]);

        $builder->getItems()[0]->getComponents();
        $get = $source->makeGetUtility();

        expect($get('/data.content.0.data.status'))->toBe(StringBackedEnum::One);

        $livewire->data['content'][0]['type'] = 'text';

        expect($get('/data.content.0.data.status'))->toBe(StringBackedEnum::One->value);
    });

    test('cached builder item lookups validate only the requested item', function (): void {
        $livewire = Livewire::make();
        $builder = (new class('content') extends Builder
        {
            public int $fullFreshnessCheckCount = 0;

            public int $itemFreshnessCheckCount = 0;

            protected function areCachedDefaultChildSchemasFresh(): bool
            {
                $this->fullFreshnessCheckCount++;

                return parent::areCachedDefaultChildSchemasFresh();
            }

            protected function isCachedDefaultChildSchemaFresh(string | int $key): bool
            {
                $this->itemFreshnessCheckCount++;

                return parent::isCachedDefaultChildSchemaFresh($key);
            }
        })
            ->generateUuidUsing(false)
            ->blocks([
                Block::make('enum')->schema([
                    Select::make('status')->options(StringBackedEnum::class),
                ]),
            ]);

        Schema::make($livewire)
            ->statePath('data')
            ->components([
                Tabs::make()
                    ->tabs([
                        $parentTab = Tab::make('Parent')
                            ->schema([$builder]),
                    ]),
            ])
            ->fill([
                'content' => array_fill(0, 10, [
                    'type' => 'enum',
                    'data' => ['status' => StringBackedEnum::One->value],
                ]),
            ]);

        foreach ($builder->getItems() as $item) {
            $item->getComponents();
        }

        $get = $parentTab->makeGetUtility();

        foreach (range(0, 9) as $itemIndex) {
            expect($get("/data.content.{$itemIndex}.data.status"))->toBe(StringBackedEnum::One);
        }

        $builder->fullFreshnessCheckCount = 0;
        $builder->itemFreshnessCheckCount = 0;

        foreach (range(0, 9) as $itemIndex) {
            expect($get("/data.content.{$itemIndex}.data.status"))->toBe(StringBackedEnum::One);
        }

        expect($builder->fullFreshnessCheckCount)
            ->toBe(0)
            ->and($builder->itemFreshnessCheckCount)
            ->toBe(10);
    });

    test('parent sibling state can be retrieved relatively from another component', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath($statePath = Str::random())
                    ->default($state = Str::random()),
                (new Component)
                    ->statePath(Str::random())
                    ->schema([
                        $placeholder = Placeholder::make(Str::random())
                            ->content(fn (Get $get): string => $get("../{$statePath}")),
                    ]),
            ])
            ->fill();

        expect($placeholder)
            ->getContent()->toBe($state);
    });

    test('sibling state can be retrieved absolutely from another component', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath($statePath = Str::random())
                    ->default($state = Str::random()),
                $placeholder = Placeholder::make(Str::random())
                    ->content(fn (Get $get): string => $get("data.{$statePath}", isAbsolute: true)),
            ])
            ->fill();

        expect($placeholder)
            ->getContent()->toBe($state);
    });

    test('sibling nested state can be retrieved absolutely from another component', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath($parentStatePath = Str::random())
                    ->schema([
                        (new Component)
                            ->statePath($statePath = Str::random())
                            ->default($state = Str::random()),
                    ]),
                $placeholder = Placeholder::make(Str::random())
                    ->content(fn (Get $get): string => $get("data.{$parentStatePath}.{$statePath}", isAbsolute: true)),
            ])
            ->fill();

        expect($placeholder)
            ->getContent()->toBe($state);
    });

    test('parent sibling state can be retrieved absolutely from another component', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath($statePath = Str::random())
                    ->default($state = Str::random()),
                (new Component)
                    ->statePath(Str::random())
                    ->schema([
                        $placeholder = Placeholder::make(Str::random())
                            ->content(fn (Get $get): string => $get("data.{$statePath}", isAbsolute: true)),
                    ]),
            ])
            ->fill();

        expect($placeholder)
            ->getContent()->toBe($state);
    });
});

describe('typed accessors', function (): void {
    it('can retrieve state as string via `string()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('name')
                    ->default('John'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->string('name')),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('John');
    });

    it('returns `null` from `string()` when nullable and blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): ?string => $get->string('empty', isNullable: true)),
            ])
            ->fill();

        expect($placeholder->getContent())->toBeNull();
    });

    it('can retrieve state as integer via `integer()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('count')
                    ->default('42'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): int => $get->integer('count')),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe(42);
    });

    it('returns `null` from `integer()` when nullable and blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): ?int => $get->integer('empty', isNullable: true)),
            ])
            ->fill();

        expect($placeholder->getContent())->toBeNull();
    });

    it('can retrieve state as float via `float()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('price')
                    ->default('3.14'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): float => $get->float('price')),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe(3.14);
    });

    it('returns `null` from `float()` when nullable and blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): ?float => $get->float('empty', isNullable: true)),
            ])
            ->fill();

        expect($placeholder->getContent())->toBeNull();
    });

    it('can retrieve state as boolean via `boolean()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('active')
                    ->default('1'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): bool => $get->boolean('active')),
            ])
            ->fill();

        expect($placeholder->getContent())->toBeTrue();
    });

    it('returns `null` from `boolean()` when nullable and blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): ?bool => $get->boolean('empty', isNullable: true)),
            ])
            ->fill();

        expect($placeholder->getContent())->toBeNull();
    });

    it('can retrieve state as array via `array()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('items')
                    ->default(['a', 'b']),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => implode(',', $get->array('items'))),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('a,b');
    });

    it('returns `null` from `array()` when nullable and not array', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('scalar')
                    ->default('not-array'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): ?string => $get->array('scalar', isNullable: true) === null ? 'null' : 'not-null'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('null');
    });

    it('can retrieve state as date via `date()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('date')
                    ->default('2024-01-15'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->date('date')->format('Y-m-d')),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('2024-01-15');
    });

    it('returns `null` from `date()` when nullable and blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): ?string => $get->date('empty', isNullable: true) === null ? 'null' : 'not-null'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('null');
    });

    it('can retrieve state as enum via `enum()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('status')
                    ->default('active'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->enum('status', GetTestStatus::class)?->value ?? 'null'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('active');
    });

    it('returns `null` from `enum()` when nullable and blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->enum('empty', GetTestStatus::class, isNullable: true) === null ? 'null' : 'not-null'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('null');
    });

    it('returns existing `BackedEnum` instance from `enum()` without re-parsing', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('status')
                    ->default(GetTestStatus::Active),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->enum('status', GetTestStatus::class)?->value ?? 'null'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('active');
    });
});

describe('`filled()` and `blank()`', function (): void {
    it('returns `true` from `filled()` when state has a value', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('name')
                    ->default('John'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->filled('name') ? 'yes' : 'no'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('yes');
    });

    it('returns `false` from `filled()` when state is blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->filled('empty') ? 'yes' : 'no'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('no');
    });

    it('returns `true` from `blank()` when state is blank', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('empty')
                    ->default(''),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->blank('empty') ? 'yes' : 'no'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('yes');
    });

    it('returns `false` from `blank()` when state has a value', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                (new Component)
                    ->statePath('name')
                    ->default('John'),
                $placeholder = Placeholder::make('result')
                    ->content(fn (Get $get): string => $get->blank('name') ? 'yes' : 'no'),
            ])
            ->fill();

        expect($placeholder->getContent())->toBe('no');
    });
});

it('can set `skipComponentsChildContainersWhileSearching()`', function (): void {
    $component = (new Component)
        ->container(Schema::make(Livewire::make()));

    $get = new Get($component);

    $result = $get->skipComponentsChildContainersWhileSearching(false);

    expect($result)->toBe($get);
});

enum GetTestStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
