<?php

namespace Filament\Tests\Forms\Components;

use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Tests\Fixtures\Enums\IntegerBackedEnum;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
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
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\HttpException;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    require_once __DIR__ . '/../../../../packages/forms/src/helpers.php';
});

describe('properties', function (): void {
    it('can set `addActionLabel()` with a string or `Closure`', function (): void {
        $repeater = Repeater::make('items')
            ->addActionLabel('Add new item');

        expect($repeater->getAddActionLabel())->toBe('Add new item');

        $repeater->addActionLabel(static fn (): string => 'Add new item');

        expect($repeater->getAddActionLabel())->toBe('Add new item');
    });

    it('can set `addBetweenActionLabel()` and get with `getAddBetweenActionLabel()`', function (): void {
        $repeater = Repeater::make('items')
            ->addBetweenActionLabel('Insert between');

        expect($repeater->getAddBetweenActionLabel())->toBe('Insert between');
    });

    it('defaults, sets and resets `labelBetweenItems()`', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->getLabelBetweenItems())->toBeNull();

        $repeater->labelBetweenItems('or');

        expect($repeater->getLabelBetweenItems())->toBe('or');

        $repeater->labelBetweenItems(null);

        expect($repeater->getLabelBetweenItems())->toBeNull();
    });

    it('defaults, sets, evaluates and resets `itemNumbers()`', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->hasItemNumbers())->toBeFalse();

        $repeater->itemNumbers();

        expect($repeater->hasItemNumbers())->toBeTrue();

        $repeater->itemNumbers(static fn (): bool => true);

        expect($repeater->hasItemNumbers())->toBeTrue();

        $repeater->itemNumbers(false);

        expect($repeater->hasItemNumbers())->toBeFalse();
    });

    it('defaults and sets `itemHeaders()`', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->hasItemHeaders())->toBeTrue();

        $repeater->itemHeaders(false);

        expect($repeater->hasItemHeaders())->toBeFalse();

        $repeater->itemHeaders();

        expect($repeater->hasItemHeaders())->toBeTrue();
    });

    it('defaults, sets, evaluates and resets `truncateItemLabel()`', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->isItemLabelTruncated())->toBeTrue();

        $repeater->truncateItemLabel(false);

        expect($repeater->isItemLabelTruncated())->toBeFalse();

        $repeater->truncateItemLabel(static fn (): bool => false);

        expect($repeater->isItemLabelTruncated())->toBeFalse();

        $repeater->truncateItemLabel();

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

    it('defaults, sets and resets `addActionAlignment()`', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->getAddActionAlignment())->toBeNull();

        $repeater->addActionAlignment('center');

        expect($repeater->getAddActionAlignment())->toBe(Alignment::Center);

        $repeater->addActionAlignment(null);

        expect($repeater->getAddActionAlignment())->toBeNull();
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

    it('returns fluent `$this` from `defaultItems()`', function (): void {
        $repeater = Repeater::make('items');

        $result = $repeater->defaultItems(3);

        expect($result)->toBe($repeater);
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
});

describe('boolean properties', function (): void {
    it('defaults, sets, evaluates and resets `addable()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items'),
            ])
            ->fill();

        expect($repeater->isAddable())->toBeTrue();

        $repeater->addable(false);

        expect($repeater->isAddable())->toBeFalse();

        $repeater->addable(static fn (): bool => false);

        expect($repeater->isAddable())->toBeFalse();

        $repeater->addable();

        expect($repeater->isAddable())->toBeTrue();
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

    it('defaults, evaluates and resets `deletable()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items'),
            ])
            ->fill();

        expect($repeater->isDeletable())->toBeTrue();

        $repeater->deletable(static fn (): bool => false);

        expect($repeater->isDeletable())->toBeFalse();

        $repeater->deletable();

        expect($repeater->isDeletable())->toBeTrue();
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

    it('defaults, evaluates and resets `reorderable()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items'),
            ])
            ->fill();

        expect($repeater->isReorderable())->toBeTrue();

        $repeater->reorderable(static fn (): bool => false);

        expect($repeater->isReorderable())->toBeFalse();

        $repeater->reorderable();

        expect($repeater->isReorderable())->toBeTrue();
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

    it('defaults `isReorderableWithButtons()` to `false`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items'),
            ])
            ->fill();

        expect($repeater->isReorderableWithButtons())->toBeFalse();
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
    it('defaults, evaluates and resets `itemLabel()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $repeater = Repeater::make('items'),
            ])
            ->fill();

        expect($repeater->hasItemLabels())->toBeFalse();

        $repeater->itemLabel(static fn (): string => 'Item');

        expect($repeater->hasItemLabels())->toBeTrue();

        $repeater->itemLabel(static fn (?array $state): string => $state['name'] ?? 'Unnamed');

        expect($repeater->hasItemLabels())->toBeTrue();

        $repeater->itemLabel(null);

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
});

describe('simple mode', function (): void {
    it('defaults, enables and resets `simple()`', function (): void {
        $repeater = Repeater::make('tags');

        expect($repeater->isSimple())->toBeFalse();

        $repeater->simple(TextInput::make('tag'));

        expect($repeater->isSimple())->toBeTrue();
        expect($repeater->getSimpleField())->toBeInstanceOf(TextInput::class);

        $repeater->simple(null);

        expect($repeater->isSimple())->toBeFalse();
    });
});

describe('order column', function (): void {
    it('defaults, sets and evaluates `orderColumn()`', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->getOrderColumn())->toBeNull();

        $repeater->orderColumn('sort');

        expect($repeater->getOrderColumn())->toBe('sort');

        $repeater->orderColumn(static fn (): string => 'position');

        expect($repeater->getOrderColumn())->toBe('position');

        $repeater->orderColumn();

        expect($repeater->getOrderColumn())->toBe('sort');
    });
});

describe('relationship name', function (): void {
    it('has no `relationship()` by default', function (): void {
        $repeater = Repeater::make('items');

        expect($repeater->getRelationshipName())->toBeNull();
        expect($repeater->hasRelationship())->toBeFalse();
    });

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

describe('UUID generation', function (): void {
    it('generates distinct UUIDs when enabled and preserves disabled and callback generation', function (): void {
        foreach ([true, null] as $configuration) {
            $repeater = Repeater::make('items')->generateUuidUsing($configuration);
            $first = $repeater->generateUuid();
            $second = $repeater->generateUuid();

            expect(Str::isUuid($first))->toBeTrue();
            expect(Str::isUuid($second))->toBeTrue();
            expect($first)->not->toBe($second);
        }

        $repeater->generateUuidUsing(false);
        expect($repeater->generateUuid())->toBeNull();
        $repeater->generateUuidUsing(static fn ($component): string => $component->getName() . '-key');
        expect($repeater->generateUuid())->toBe('items-key');
        expect($repeater->generateUuid())->toBe('items-key');
    });
});

describe('state hydration', function (): void {
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

    it('fills two default items when no initial state is provided', function (): void {
        livewire(TestComponentWithRepeaterDefaultItems::class)
            ->tap(function ($livewire): void {
                $items = $livewire->get('data.items');

                expect($items)->toHaveCount(2);
            });
    });

    it('can fill and assert data in a repeater', function (array $data): void {
        $undoRepeaterFake = Repeater::fake();

        try {
            livewire(TestComponentWithRepeater::class)
                ->fillForm($data)
                ->assertSchemaStateSet($data);
        } finally {
            $undoRepeaterFake();
        }
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
        } finally {
            $undoRepeaterFake();
        }
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
});

describe('item schemas', function (): void {
    it('builds one schema per item keyed by the item key', function (): void {
        $repeater = Repeater::make('items')
            ->schema([
                TextInput::make('name'),
            ])
            ->default([
                ['name' => 'First'],
                ['name' => 'Second'],
                ['name' => 'Third'],
            ]);

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([$repeater])
            ->fill();

        $items = $repeater->getItems();

        expect($items)->toHaveCount(3)
            ->and(array_keys($items))->toBe(array_keys($repeater->getRawState()))
            ->and(array_values($items)[0])->toBeInstanceOf(Schema::class);
    });

    it('clones components from a warmed closure-backed `Schema` for each item', function (): void {
        $livewire = Livewire::make();
        $itemSchema = Schema::make($livewire)
            ->components(static fn (): array => [
                TextInput::make('name'),
            ]);

        $itemSchema->getComponents();

        $repeater = Repeater::make('items')
            ->schema($itemSchema)
            ->default([
                ['name' => 'First'],
                ['name' => 'Second'],
            ]);

        Schema::make($livewire)
            ->statePath('data')
            ->components([$repeater])
            ->fill();

        [$firstItem, $secondItem] = array_values($repeater->getItems());
        $firstComponent = $firstItem->getComponents()[0];
        $secondComponent = $secondItem->getComponents()[0];

        expect($firstComponent)
            ->not->toBe($secondComponent)
            ->getContainer()->toBe($firstItem)
            ->getParentRepeaterItem()->toBe($firstItem)
            ->getStatePath()->not->toBe($secondComponent->getStatePath())
            ->and($secondComponent->getContainer())->toBe($secondItem)
            ->and($secondComponent->getParentRepeaterItem())->toBe($secondItem);
    });

    it('reflects the new item count after a state-updating action', function (): void {
        livewire(TestComponentWithRepeaterSetByAction::class)
            ->callAction(TestAction::make('insert')->schemaComponent('questionsSection'))
            ->assertSchemaStateSet(function (array $state): array {
                expect($state['questions'])->toHaveCount(3);

                return [];
            });
    });

    it('can evaluate a `setUp()` closure bound to the original component with a `$get` injection inside a repeater item', function (): void {
        $repeater = Repeater::make('items')
            ->schema([
                TextInput::make('name'),
                SelectWithOptionsEvaluatedInSetUp::make('option'),
            ])
            ->default([
                ['name' => 'First', 'option' => null],
            ]);

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([$repeater])
            ->fill();

        $select = Arr::first($repeater->getItems())->getComponents()[1];

        expect($select->getOptions())->toBe(['First' => 'First']);
    });

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

    it('rebuilds items after an `afterStateUpdated()` hook uses `$set()` on an ancestor\'s state path', function (): void {
        livewire(RepeaterInStatePathAncestorSetByHook::class)
            ->assertSeeText('Original item')
            ->set('data.trigger', 'anything')
            ->assertSeeText('Added item');
    });

    it('can save a builder block containing a repeater and hidden field', function (): void {
        livewire(TestComponentWithBuilderRepeaterAndHiddenField::class)
            ->assertSuccessful()
            ->call('save')
            ->assertHasNoFormErrors();
    });
});

describe('processing item limits', function (): void {
    it('processes at most 500 items by default', function (): void {
        $item = ['foo' => 'Kept item'];

        livewire(RepeaterWithItemActions::class)
            ->set('data.group.items', array_fill(0, 500, $item))
            ->assertSuccessful()
            ->assertSchemaStateSet(['group.items' => array_fill(0, 500, $item)])
            ->set('data.group.items', array_fill(0, 501, $item))
            ->assertStatus(413);
    });

    it('allows overriding a global `maxItemsForProcessing()` independently of `maxItems()`', function (): void {
        expect(Repeater::make('items')->getMaxItemsForProcessing())->toBe(500);

        Repeater::configureUsing(
            static fn (Repeater $component) => $component->maxItemsForProcessing(10),
            during: function (): void {
                expect(Repeater::make('items')->getMaxItemsForProcessing())->toBe(10);

                foreach ([5, 20, null] as $limit) {
                    $component = Repeater::make('items')->maxItems(2)->maxItemsForProcessing($limit);

                    expect($component->getMaxItemsForProcessing())->toBe($limit)
                        ->and($component->getMaxItems())->toBe(2);
                }
            },
        );
    });

    it('rejects oversized state before evaluating item schemas without truncating it', function (): void {
        $items = array_fill(0, 3, ['foo' => 'Keep me']);
        $fields = static fn () => throw new RuntimeException('Item schemas must not be evaluated.');
        $component = Repeater::make('items')
            ->maxItemsForProcessing(2)
            ->schema($fields);

        Schema::make(Livewire::make()->data(['items' => $items]))
            ->statePath('data')
            ->components([$component])
            ->getComponents();

        expect(fn () => $component->getDefaultChildSchemas())->toThrow(function (HttpException $exception): void {
            expect($exception->getStatusCode())->toBe(413);
        });

        expect($component->getRawState())->toBe($items);
    });

    it('accepts the exact processing limit, rejects item growth after caching, and allows disabling the limit', function (): void {
        $items = array_fill(0, 2, ['foo' => 'Keep me']);
        $component = Repeater::make('items')
            ->maxItems(1)
            ->maxItemsForProcessing(2)
            ->schema([TextInput::make('foo')]);

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([$component])
            ->fill(['items' => $items]);

        expect($component->getItems())->toHaveCount(2);

        expect(fn () => $schema->getState())->toThrow(ValidationException::class);

        $component->rawState([...$items, $items[0]]);

        expect(fn () => $component->getItems())->toThrow(function (HttpException $exception): void {
            expect($exception->getStatusCode())->toBe(413);
        });

        $component->maxItemsForProcessing(null);

        expect($component->getItems())->toHaveCount(3);

        $component->rawState([])->maxItemsForProcessing(0);

        expect($component->getItems())->toBe([]);
    });

    it('rejects oversized nested data during `fill()` before evaluating item schemas', function (): void {
        $fields = static fn () => throw new RuntimeException('Item schemas must not be evaluated.');
        $component = Repeater::make('children')->maxItemsForProcessing(1)->schema($fields);

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([Repeater::make('parents')->schema([$component])]);

        $item = ['foo' => 'Keep me'];

        expect(fn () => $schema->fill(['parents' => [['children' => [$item, $item]]]]))
            ->toThrow(function (HttpException $exception): void {
                expect($exception->getStatusCode())->toBe(413);
            });
    });

    it('rejects oversized builder data nested in a repeater during `fill()` before evaluating item schemas', function (): void {
        $fields = static fn () => throw new RuntimeException('Item schemas must not be evaluated.');
        $component = Builder::make('children')
            ->maxItemsForProcessing(1)
            ->blocks([Builder\Block::make('one')->schema($fields)]);

        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([Repeater::make('parents')->schema([$component])]);

        $item = ['type' => 'one', 'data' => ['foo' => 'Keep me']];

        expect(fn () => $schema->fill(['parents' => [['children' => [$item, $item]]]]))
            ->toThrow(function (HttpException $exception): void {
                expect($exception->getStatusCode())->toBe(413);
            });
    });

    it('rejects Livewire item and ancestor replacements above `maxItemsForProcessing()`', function (string $statePath): void {
        $item = ['foo' => 'Keep me'];

        Repeater::configureUsing(
            static fn (Repeater $component) => $component->maxItemsForProcessing(2),
            during: function () use ($statePath, $item): void {
                $livewire = livewire(RepeaterWithItemActions::class)
                    ->set('data.group.items', array_fill(0, 2, $item))
                    ->assertSuccessful();

                $state = [];
                data_set($state, 'data.group.items', array_fill(0, 3, $item));

                $livewire->set($statePath, data_get($state, $statePath))->assertStatus(413);
            },
        );
    })->with(['data.group.items', 'data.group', 'data']);
});

describe('`distinct()` validation in nested builders', function (): void {
    it('can validate `distinct()` fields in a repeater in a builder block with errors', function (): void {
        $data = [
            'builder' => [
                [
                    'type' => 'one',
                    'data' => [
                        'foo' => 'test 1',
                        'repeater' => [
                            ['bar' => 'test 1'],
                            ['bar' => 'test 1'],
                        ],
                    ],
                ],
                [
                    'type' => 'one',
                    'data' => [
                        'foo' => 'test 1',
                        'repeater' => [
                            ['bar' => 'test 1'],
                            ['bar' => 'test 1'],
                        ],
                    ],
                ],
            ],
        ];

        livewire(TestComponentWithBuilderAndRepeater::class)
            ->assertSuccessful()
            ->fillForm($data)
            ->assertSchemaStateSet($data)
            ->call('save')
            ->assertHasFormErrors([
                'builder.0.data.foo' => ['The foo field has a duplicate value.'],
                'builder.0.data.repeater.0.bar' => ['The bar field has a duplicate value.'],
                'builder.0.data.repeater.1.bar' => ['The bar field has a duplicate value.'],
                'builder.1.data.foo' => ['The foo field has a duplicate value.'],
                'builder.1.data.repeater.0.bar' => ['The bar field has a duplicate value.'],
                'builder.1.data.repeater.1.bar' => ['The bar field has a duplicate value.'],
            ]);
    });

    it('can validate `distinct()` fields in a repeater in a builder block with no errors', function (): void {
        $data = [
            'builder' => [
                [
                    'type' => 'one',
                    'data' => [
                        'foo' => 'test 1',
                        'repeater' => [
                            ['bar' => 'test 1'],
                            ['bar' => 'test 2'],
                        ],
                    ],
                ],
                [
                    'type' => 'one',
                    'data' => [
                        'foo' => 'test 2',
                        'repeater' => [
                            ['bar' => 'test 1'],
                            ['bar' => 'test 2'],
                        ],
                    ],
                ],
            ],
        ];

        livewire(TestComponentWithBuilderAndRepeater::class)
            ->assertSuccessful()
            ->fillForm($data)
            ->assertSchemaStateSet($data)
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('can validate `distinct()` fields in a builder block in a repeater with errors', function (): void {
        $undoRepeaterFake = Repeater::fake();

        $data = [
            'repeater' => [
                [
                    'bar' => 'test 1',
                    'builder' => [
                        ['type' => 'one', 'data' => ['foo' => 'test 1']],
                        ['type' => 'one', 'data' => ['foo' => 'test 1']],
                    ],
                ],
                [
                    'bar' => 'test 1',
                    'builder' => [
                        ['type' => 'one', 'data' => ['foo' => 'test 1']],
                        ['type' => 'one', 'data' => ['foo' => 'test 1']],
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

    it('can validate `distinct()` fields in a builder block in a repeater with no errors', function (): void {
        $undoRepeaterFake = Repeater::fake();

        $data = [
            'repeater' => [
                [
                    'bar' => 'test 1',
                    'builder' => [
                        ['type' => 'one', 'data' => ['foo' => 'test 1']],
                        ['type' => 'one', 'data' => ['foo' => 'test 2']],
                    ],
                ],
                [
                    'bar' => 'test 2',
                    'builder' => [
                        ['type' => 'one', 'data' => ['foo' => 'test 1']],
                        ['type' => 'one', 'data' => ['foo' => 'test 2']],
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
});

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

describe('`fixIndistinctState()`', function (): void {
    it('clears duplicate cast values and preserves their submitted type', function (string $fieldClass, array | string $options, mixed $state, string $updatedState): void {
        $livewire = livewire(TestComponentWithFixIndistinctStateRepeater::class, [
            'fieldClass' => $fieldClass,
            'options' => $options,
            'initialItems' => [['choice' => $state], ['choice' => null]],
        ])
            ->set('data.items.1.choice', $updatedState)
            ->call('save')
            ->assertHasNoFormErrors();

        expect($livewire->get('submittedData'))->toBe([
            'items' => [['choice' => null], ['choice' => $state]],
        ]);
    })->with([
        'numeric zero' => [Select::class, [0 => 'Zero', 1 => 'One'], 0, '0'],
        'string enum' => [Radio::class, StringBackedEnum::class, StringBackedEnum::One, 'one'],
        'integer enum' => [ToggleButtons::class, IntegerBackedEnum::class, IntegerBackedEnum::Zero, '0'],
    ]);

    it('clears duplicate enum selections from multiple fields', function (): void {
        $livewire = livewire(TestComponentWithFixIndistinctStateRepeater::class, [
            'options' => TestLetterEnum::class,
            'isMultiple' => true,
            'initialItems' => [['choice' => ['A', 'B']], ['choice' => ['C']]],
        ])->set('data.items.1.choice', ['A']);

        expect($livewire->get('data'))->toBe([
            'items' => [['choice' => ['B']], ['choice' => ['A']]],
        ]);

        $livewire->call('save')->assertHasNoFormErrors();

        expect($livewire->get('submittedData'))->toBe([
            'items' => [['choice' => [TestLetterEnum::B]], ['choice' => [TestLetterEnum::A]]],
        ]);
    });

    it('allows multiple `false` values and clears only other `true` values', function (string $fieldClass): void {
        $livewire = livewire(TestComponentWithFixIndistinctStateRepeater::class, [
            'fieldClass' => $fieldClass,
            'initialItems' => [['choice' => true], ['choice' => false], ['choice' => false]],
        ])
            ->set('data.items.1.choice', false)
            ->call('save')
            ->assertHasNoFormErrors();

        expect($livewire->get('submittedData'))->toBe([
            'items' => [['choice' => true], ['choice' => false], ['choice' => false]],
        ]);

        $livewire->set('data.items.1.choice', true)
            ->call('save')
            ->assertHasNoFormErrors();

        expect($livewire->get('submittedData'))->toBe([
            'items' => [['choice' => false], ['choice' => true], ['choice' => false]],
        ]);
    })->with(['checkbox' => [Checkbox::class], 'toggle' => [Toggle::class]]);

    it('preserves different string option identifiers with leading zeroes', function (): void {
        $livewire = livewire(TestComponentWithFixIndistinctStateRepeater::class, [
            'options' => ['01' => 'First', 1 => 'Second'],
            'initialItems' => [['choice' => '01'], ['choice' => null]],
        ])->set('data.items.1.choice', '1');

        expect($livewire->get('data'))->toBe([
            'items' => [['choice' => '01'], ['choice' => '1']],
        ]);

        $livewire->call('save')->assertHasFormErrors([
            'items.0.choice',
            'items.1.choice',
        ]);
    });
});

describe('`disableOptionsWhenSelectedInSiblingRepeaterItems()`', function (): void {
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

    it('disables enum options selected in sibling repeater items for multiple fields', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                Repeater::make('items')
                    ->generateUuidUsing(false)
                    ->schema([
                        Select::make('choice')
                            ->multiple()
                            ->options(TestLetterEnum::class)
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                    ]),
            ]);

        $schema->fill(['items' => [['choice' => ['A']], ['choice' => ['B']]]]);

        expect($schema->getComponentByStatePath('items.0.choice', withHidden: true)->getEnabledOptions())->toBe([
            'A' => 'A',
            'C' => 'C',
        ])
            ->and($schema->getComponentByStatePath('items.1.choice', withHidden: true)->getEnabledOptions())->toBe([
                'B' => 'B',
                'C' => 'C',
            ]);
    });
});

describe('item actions', function (): void {
    it('inserts two items with distinct enabled UUIDs', function (): void {
        $livewire = livewire(RepeaterWithItemActions::class);

        foreach (range(1, 2) as $index) {
            $livewire->call('mountAction', 'add', [], ['schemaComponent' => 'form.group.items']);
            expect($livewire->get('data.group.items'))->toHaveCount($index);
        }

        foreach (array_keys($livewire->get('data.group.items')) as $key) {
            expect(Str::isUuid($key))->toBeTrue();
        }
    });

    it('clones an existing item without changing its source or sibling state', function (bool $generateUuids): void {
        $items = [
            'source' => ['foo' => 'Alpha', 'nested' => ['left' => 13, 'right' => [7, 29]]],
            'other' => ['foo' => 'Beta', 'nested' => ['left' => 31, 'right' => []]],
        ];

        $livewire = livewire(RepeaterWithItemActions::class, compact('generateUuids'))
            ->set('data.group.items', $items)
            ->call('mountAction', 'clone', ['item' => 'source'], ['schemaComponent' => 'form.group.items'])
            ->assertDispatched('items-updated');

        $state = $livewire->get('data.group.items');
        expect($state)->toHaveCount(3);
        $newKey = array_key_last($state);

        if ($generateUuids) {
            expect(Str::isUuid($newKey))->toBeTrue();
        } else {
            expect($newKey)->toBe(0);
        }

        expect($livewire->get('data'))->toBe([
            'group' => ['items' => [...$items, $newKey => $items['source']]],
            'sibling' => 'Untouched',
        ]);
    })->with([false, true]);

    it('ignores stale item actions after a concurrent deletion without changing nested or sibling state', function (string $action, bool $deleteAll): void {
        $items = [
            'record-17' => ['foo' => 'Alpha', 'nested' => ['left' => 13, 'right' => [7, 29]]],
            'record-42' => ['foo' => 'Beta', 'nested' => ['left' => 31, 'right' => []]],
            'record-93' => ['foo' => 'Gamma', 'nested' => ['left' => 2, 'right' => [11]]],
        ];

        $livewire = livewire(RepeaterWithItemActions::class)
            ->set('data.group.items', $items);

        foreach ($deleteAll ? array_keys($items) : ['record-42'] as $key) {
            $livewire->call('mountAction', 'delete', ['item' => $key], ['schemaComponent' => 'form.group.items']);
            unset($items[$key]);
        }

        $expected = ['group' => ['items' => $items], 'sibling' => 'Untouched'];
        expect($livewire->get('data'))->toBe($expected);

        $livewire->call('mountAction', $action, [
            'item' => 'record-42',
            'afterItem' => 'record-42',
            'items' => ['record-93', 'record-42', 'record-17'],
        ], ['schemaComponent' => 'form.group.items'])->assertNotDispatched('items-updated');

        expect($livewire->get('data'))->toBe($expected);
    })->with(['clone', 'moveUp', 'moveDown', 'delete', 'addBetween', 'reorder', 'collapse', 'expand'])
        ->with([false, true]);

    it('preserves item keys and values for valid moves and partial reorders', function (array $keys): void {
        $items = array_combine($keys, [
            ['foo' => 'Alpha', 'nested' => [13, 7]],
            ['foo' => 'Beta', 'nested' => [29]],
            ['foo' => 'Gamma', 'nested' => []],
        ]);

        $livewire = livewire(RepeaterWithItemActions::class)->set('data.group.items', $items);
        $livewire->call('mountAction', 'moveDown', ['item' => (string) $keys[0]], ['schemaComponent' => 'form.group.items']);
        expect($livewire->get('data.group.items'))->toBe([$keys[1] => $items[$keys[1]], $keys[0] => $items[$keys[0]], $keys[2] => $items[$keys[2]]]);
        $livewire->call('mountAction', 'moveUp', ['item' => (string) $keys[0]], ['schemaComponent' => 'form.group.items']);
        expect($livewire->get('data.group.items'))->toBe($items);
        $livewire->call('mountAction', 'moveUp', ['item' => $keys[0]], ['schemaComponent' => 'form.group.items']);
        $livewire->call('mountAction', 'moveDown', ['item' => $keys[2]], ['schemaComponent' => 'form.group.items']);
        expect($livewire->get('data.group.items'))->toBe($items);
        $livewire->call('mountAction', 'reorder', ['items' => [(string) $keys[2], (string) $keys[0]]], ['schemaComponent' => 'form.group.items']);
        expect($livewire->get('data'))->toBe([
            'group' => ['items' => [$keys[2] => $items[$keys[2]], $keys[0] => $items[$keys[0]], $keys[1] => $items[$keys[1]]]],
            'sibling' => 'Untouched',
        ]);
    })->with([
        'relationship keys' => [['record-17', 'record-42', 'record-93']],
        'numeric keys' => [[0, 1, 2]],
    ]);

    it('adds between numeric items without overwriting the following item when UUIDs are disabled', function (): void {
        $items = [['foo' => 'Alpha'], ['foo' => 'Beta']];

        $livewire = livewire(RepeaterWithItemActions::class, ['generateUuids' => false])
            ->set('data.group.items', $items)
            ->call('mountAction', 'addBetween', ['afterItem' => '0'], ['schemaComponent' => 'form.group.items']);

        $newItem = ['foo' => 'Draft 3'];

        expect($livewire->get('data.group.items'))->toBe([0 => $items[0], 2 => $newItem, 1 => $items[1]]);
    });

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

    it('loads existing records when `modifyQueryUsing()` adds a join and `orderColumn()` is set, without an ambiguous column error', function (): void {
        $undoRepeaterFake = Repeater::fake();

        $user = User::factory()->create();
        Post::factory()->count(3)->create(['author_id' => $user->id]);

        livewire(RepeaterWithHasManyRelationshipJoinAndOrderColumn::class, ['record' => $user])
            ->assertSchemaStateSet(function (array $state) {
                expect($state['posts'])->toHaveCount(3);

                return [];
            });

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

        $component->set('data.posts', []);
        $component->call('save');

        $undoRepeaterFake();

        expect(Post::query()->whereKey($publishedPost->id)->exists())->toBeFalse();

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

describe('relationship ordering and mutation', function (): void {
    it('preserves related records when stale reorders follow deletion and saves valid ordering', function (): void {
        $user = User::factory()->create();
        $first = Post::factory()->create(['author_id' => $user->id, 'title' => 'Alpha', 'rating' => 1]);
        $deleted = Post::factory()->create(['author_id' => $user->id, 'title' => 'Beta', 'rating' => 2]);
        $last = Post::factory()->create(['author_id' => $user->id, 'title' => 'Gamma', 'rating' => 3]);

        $livewire = livewire(RepeaterWithHasManyRelationshipAndOrderColumn::class, ['record' => $user])
            ->callAction(TestAction::make('delete')->schemaComponent('posts')->arguments(['item' => "record-{$deleted->id}"]));
        $remainingState = $livewire->get('data');

        $livewire->callAction(TestAction::make('reorder')->schemaComponent('posts')->arguments([
            'items' => ["record-{$last->id}", "record-{$deleted->id}", "record-{$first->id}"],
        ]));
        expect($livewire->get('data'))->toBe($remainingState);

        $livewire->callAction(TestAction::make('moveDown')->schemaComponent('posts')->arguments(['item' => "record-{$first->id}"]));
        expect(array_keys($livewire->get('data.posts')))->toBe(["record-{$last->id}", "record-{$first->id}"]);
        $livewire->callAction(TestAction::make('moveUp')->schemaComponent('posts')->arguments(['item' => "record-{$first->id}"]));
        expect($livewire->get('data'))->toBe($remainingState);

        $livewire->callAction(TestAction::make('reorder')->schemaComponent('posts')->arguments([
            'items' => ["record-{$last->id}", "record-{$first->id}"],
        ]))->call('save');

        expect($user->posts()->orderBy('rating')->pluck('id')->all())->toBe([$last->id, $first->id]);
        expect($first->fresh()->title)->toBe('Alpha');
        expect($first->fresh()->rating)->toBe(2);
        expect($last->fresh()->title)->toBe('Gamma');
        expect($last->fresh()->rating)->toBe(1);
        expect($deleted->fresh()->trashed())->toBeTrue();
    });

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

    it('skips updating an existing record when `mutateRelationshipDataBeforeSaveUsing()` returns `null`', function (): void {
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

    it('skips creating a new record when `mutateRelationshipDataBeforeCreateUsing()` returns `null`', function (): void {
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

describe('relationship persistence', function (): void {
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

    it('deletes removed records via the `Schema` API', function (): void {
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

    it('preserves literal labels and decodes `Htmlable` text in reorder descriptions', function (string | HtmlString $label, ?string $expectedLabel): void {
        $repeater = Repeater::make('items')
            ->generateUuidUsing(false)
            ->schema([TextInput::make('foo')])
            ->itemLabel($label)
            ->default([['foo' => 'Value']]);

        Schema::make(Livewire::make())->statePath('data')->components([$repeater])->fill();
        $expectedLabel ??= __('filament-forms::components.repeater.reordering.item');

        expect($repeater->toHtml())
            ->toContain('data-reorder-label="' . e($expectedLabel) . '"')
            ->toContain(e(__('filament-forms::components.repeater.reordering.position', ['label' => $expectedLabel, 'position' => 1, 'count' => 1])));
    })->with([
        'literal' => ['Customer <West> &amp;', 'Customer <West> &amp;'],
        'HTML' => [new HtmlString('<strong>Research &amp; Development &lt;West&gt;</strong>'), 'Research & Development <West>'],
        'literal zero' => ['0', '0'],
        'HTML zero' => [new HtmlString('<strong>0</strong>'), '0'],
        'empty' => ['', null],
        'whitespace' => ['  ', null],
        'textless HTML' => [new HtmlString('<i></i>'), null],
    ]);
});

describe('browser interactions', function (): void {
    it('clears duplicate selections with `fixIndistinctState()` in the browser and allows saving', function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        $firstSelect = ':nth-match([data-testid="choice"], 1)';
        $secondSelect = ':nth-match([data-testid="choice"], 2)';

        $page = visit('/indistinct-state-browser-test');

        $page
            ->assertValue($firstSelect, '0')
            ->select($secondSelect, '0')
            ->assertValue($firstSelect, '')
            ->assertValue($secondSelect, '0')
            ->assertNoAccessibilityIssues()
            ->click('[data-testid="save"]')
            ->assertVisible('[data-testid="saved"]')
            ->assertNoSmoke();

        $page->inDarkMode()->assertNoAccessibilityIssues();
    });

    it('keeps item order, position feedback and keyboard focus when reordering in the browser', function (string $view): void {
        Artisan::call('filament:assets');
        $this->actingAs(User::factory()->create());

        $url = '/repeater-test?reordering=' . $view;
        $page = visit($url);
        $firstName = ':nth-match([data-testid="item-name"], 1)';
        $secondName = ':nth-match([data-testid="item-name"], 2)';
        $thirdName = ':nth-match([data-testid="item-name"], 3)';
        $moveDown = '[data-testid="repeater"] [data-reorder-direction="down"]:not([data-testid="children"] *)';
        $moveUp = '[data-testid="repeater"] [data-reorder-direction="up"]:not([data-testid="children"] *)';
        $firstDown = ':nth-match(' . $moveDown . ', 1)';
        $secondDown = ':nth-match(' . $moveDown . ', 2)';
        $secondUp = ':nth-match(' . $moveUp . ', 2)';
        $thirdUp = ':nth-match(' . $moveUp . ', 3)';
        $status = '[data-testid="repeater"] > [role="status"]';
        $firstDragHandle = ':nth-match([data-testid="repeater"] button[aria-label="' . __('filament-forms::components.repeater.actions.reorder.label') . '"], 1)';

        $page->assertAttribute($firstDragHandle, 'tabindex', '-1')
            ->assertAttribute($firstDragHandle, 'aria-hidden', 'true')
            ->keys($firstDown, 'Enter')
            ->assertValue($firstName, 'Beta')
            ->assertValue($secondName, 'Alpha')
            ->assertValue($thirdName, 'Gamma')
            ->assertPresent($secondDown . ':focus')
            ->assertSeeIn($status, __('filament-forms::components.repeater.reordering.moved', [
                'label' => $view === 'simple' ? __('filament-forms::components.repeater.reordering.item') : 'Alpha',
                'position' => 2,
                'count' => 3,
            ]));

        $page->keys($secondDown, 'Space')
            ->assertValue($firstName, 'Beta')
            ->assertValue($secondName, 'Gamma')
            ->assertValue($thirdName, 'Alpha')
            ->assertDisabled(':nth-match(' . $moveDown . ', 3)');

        if ($view === 'one-direction') {
            $lastItem = ':nth-match([data-testid="repeater"] li:has([data-testid="item-name"]), 3)';
            $page->assertPresent($lastItem . ':focus');
            $description = '[id="' . $page->attribute($lastItem, 'aria-labelledby') . '"]';
            $page->assertSeeIn($description, __('filament-forms::components.repeater.reordering.position', ['label' => 'Alpha', 'position' => 3, 'count' => 3]))
                ->keys($secondDown, 'Enter')
                ->assertValue($firstName, 'Beta')
                ->assertValue($secondName, 'Alpha')
                ->assertValue($thirdName, 'Gamma')
                ->assertSeeIn($description, __('filament-forms::components.repeater.reordering.position', ['label' => 'Alpha', 'position' => 2, 'count' => 3]))
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();

            $page->inDarkMode()->assertNoAccessibilityIssues();

            return;
        }

        $page->assertPresent($thirdUp . ':focus')
            ->keys($thirdUp, 'Enter')
            ->assertValue($firstName, 'Beta')
            ->assertValue($secondName, 'Alpha')
            ->assertValue($thirdName, 'Gamma')
            ->assertPresent($secondUp . ':focus')
            ->keys($secondUp, 'Enter')
            ->assertValue($firstName, 'Alpha')
            ->assertValue($secondName, 'Beta')
            ->assertValue($thirdName, 'Gamma')
            ->assertPresent($firstDown . ':focus')
            ->assertDisabled(':nth-match(' . $moveUp . ', 1)');

        if (in_array($view, ['regular', 'full', 'ancestor'])) {
            $page->keys(':nth-match([data-testid="children"] [data-reorder-direction="down"], 1)', 'Enter')
                ->assertValue(':nth-match([data-testid="child-name"], 1)', 'Child B')
                ->assertValue(':nth-match([data-testid="child-name"], 2)', 'Child A')
                ->assertPresent('[data-testid="children"] [data-reorder-direction="up"]:focus')
                ->assertSeeIn('[data-testid="children"] > [role="status"]', __('filament-forms::components.repeater.reordering.moved', ['label' => __('filament-forms::components.repeater.reordering.item'), 'position' => 2, 'count' => 2]))
                ->assertSeeIn($status, __('filament-forms::components.repeater.reordering.moved', ['label' => 'Alpha', 'position' => 1, 'count' => 3]));
        }

        $page->assertNoSmoke();

        if (in_array($view, ['regular', 'simple', 'table'])) {
            $page->assertNoAccessibilityIssues();
            $page->inDarkMode()->assertNoAccessibilityIssues();
        }
    })->with(['regular', 'full', 'ancestor', 'simple', 'table', 'one-direction']);

    it('provides accessible non-tabbable move actions when visible buttons are disabled', function (string $view): void {
        Artisan::call('filament:assets');
        $this->actingAs(User::factory()->create());
        $url = '/repeater-test?reordering=screen-reader-' . $view;
        $page = visit($url);
        $controls = '[data-testid="repeater"] [data-reorder-direction]:not([data-testid="children"] *)';
        $page->assertCount($controls, 6)
            ->assertNotPresent('[data-testid="repeater"] [aria-hidden="true"] [data-reorder-direction]');

        foreach (range(1, 6) as $position) {
            $page->assertAttribute(':nth-match(' . $controls . ', ' . $position . ')', 'tabindex', '-1');
        }

        $page->assertNoSmoke();

        if (in_array($view, ['regular', 'simple', 'table'])) {
            $page->assertNoAccessibilityIssues();
            $page->inDarkMode()->assertNoAccessibilityIssues();
        }
    })->with(['regular', 'full', 'ancestor', 'simple', 'table']);

    it('omits fallback move controls when drag is unavailable', function (string $view): void {
        Artisan::call('filament:assets');
        $this->actingAs(User::factory()->create());
        $url = '/repeater-test?reordering=screen-reader-' . $view;
        $page = visit($url);
        $page->assertNotPresent('[data-testid="repeater"] [data-reorder-direction]:not([data-testid="children"] *)')
            ->assertNoSmoke();

        if ($view === 'disabled') {
            $page->assertNoAccessibilityIssues();
            $page->inDarkMode()->assertNoAccessibilityIssues();
        }
    })->with(['no-drag', 'disabled', 'no-handle']);

    it('announces confirmed moves and allows reordering after cancelling confirmation', function (): void {
        Artisan::call('filament:assets');
        $this->actingAs(User::factory()->create());
        $url = '/repeater-test?reordering=modal';
        $page = visit($url);
        $firstDown = ':nth-match([data-testid="repeater"] [data-reorder-direction="down"], 1)';
        $status = '[data-testid="repeater"] > [role="status"]';

        $page->assertPresent('[data-testid="repeater"]')
            ->keys($firstDown, 'Enter')->assertVisible('[data-testid="cancel-move"]')
            ->assertSeeNothingIn($status)
            ->click('[data-testid="cancel-move"]')
            ->assertNotPresent('[data-testid="cancel-move"]')
            ->assertValue(':nth-match([data-testid="item-name"], 1)', 'Alpha')
            ->assertValue(':nth-match([data-testid="item-name"], 2)', 'Beta')
            ->assertValue(':nth-match([data-testid="item-name"], 3)', 'Gamma')
            ->assertSeeNothingIn($status)
            ->click($firstDown)
            ->assertVisible('[data-testid="confirm-move"]')
            ->assertSeeNothingIn($status)
            ->click('[data-testid="confirm-move"]')
            ->assertValue(':nth-match([data-testid="item-name"], 1)', 'Beta')
            ->assertValue(':nth-match([data-testid="item-name"], 2)', 'Alpha')
            ->assertValue(':nth-match([data-testid="item-name"], 3)', 'Gamma')
            ->assertNotPresent('[data-testid="confirm-move"]')
            ->assertSeeIn($status, __('filament-forms::components.repeater.reordering.moved', ['label' => 'Alpha', 'position' => 2, 'count' => 3]))
            ->click(':nth-match([data-testid="repeater"] [data-reorder-direction="up"]:not([data-testid="children"] *), 2)')
            ->assertValue(':nth-match([data-testid="item-name"], 1)', 'Alpha')
            ->assertValue(':nth-match([data-testid="item-name"], 2)', 'Beta')
            ->assertValue(':nth-match([data-testid="item-name"], 3)', 'Gamma')
            ->assertNoSmoke();
    });

    it('leaves custom move handlers in control without locking later ordering', function (string $view): void {
        Artisan::call('filament:assets');
        $this->actingAs(User::factory()->create());
        $url = '/repeater-test?reordering=' . $view;
        $page = visit($url);
        $page->assertPresent('[data-testid="repeater"]')
            ->assertNotPresent('[data-testid="custom-refreshed"]');
        $page->keys(':nth-match([data-testid="repeater"] [data-reorder-direction="down"], 1)', 'Enter');

        if ($view === 'custom-refresh') {
            $page->assertVisible('[data-testid="custom-refreshed"]');
        } else {
            $page->assertVisible('[data-testid="cancel-custom"]');

            $page->click('[data-testid="cancel-custom"]')
                ->assertMissing('[data-testid="cancel-custom"]')
                ->assertNotPresent('[role="dialog"]:visible');
        }

        $page->assertSeeNothingIn('[data-testid="repeater"] > [role="status"]')
            ->click(':nth-match([data-testid="repeater"] [data-reorder-direction="up"]:not([data-testid="children"] *), 2)')
            ->assertValue(':nth-match([data-testid="item-name"], 1)', 'Beta')
            ->assertValue(':nth-match([data-testid="item-name"], 2)', 'Alpha')
            ->assertValue(':nth-match([data-testid="item-name"], 3)', 'Gamma')
            ->assertSeeIn('[data-testid="repeater"] > [role="status"]', __('filament-forms::components.repeater.reordering.moved', ['label' => 'Beta', 'position' => 1, 'count' => 3]))
            ->assertNoSmoke();
    })->with(['custom-mount', 'custom-refresh']);

    it('can add and delete items in the browser', function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        $firstName = ':nth-match([data-testid="item-name"], 1)';
        $secondName = ':nth-match([data-testid="item-name"], 2)';
        $page = visit('/repeater-test');

        $page->assertValue($firstName, 'Alpha')
            ->click('[data-testid="add-item"]')
            ->assertVisible($secondName)
            ->fill($secondName, 'Beta')
            ->assertValue($firstName, 'Alpha')
            ->click(':nth-match([data-testid="delete-item"], 2)')
            ->assertNotPresent($secondName)
            ->assertValue($firstName, 'Alpha')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        $page->inDarkMode()->assertNoAccessibilityIssues();
    });
});

class TestComponentWithBuilderAndRepeater extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Builder::make('builder')
                    ->blocks([
                        Builder\Block::make('one')
                            ->schema([
                                TextInput::make('foo')->distinct(),
                                Repeater::make('repeater')
                                    ->schema([
                                        TextInput::make('bar')->distinct(),
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

class TestComponentWithRepeaterAndBuilder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Repeater::make('repeater')
                    ->schema([
                        TextInput::make('bar')->distinct(),
                        Builder::make('builder')
                            ->blocks([
                                Builder\Block::make('one')
                                    ->schema([
                                        TextInput::make('foo')->distinct(),
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

class TestComponentWithBuilderRepeaterAndHiddenField extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Builder::make('builder')
                    ->blocks([
                        Builder\Block::make('services')
                            ->schema([
                                Repeater::make('items')
                                    ->schema([
                                        TextInput::make('service')->required(),
                                    ]),
                                TextInput::make('hidden')->visible(false),
                            ]),
                    ])
                    ->default([
                        [
                            'type' => 'services',
                            'data' => [
                                'items' => [['service' => 'Service 1']],
                            ],
                        ],
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class RepeaterWithItemActions extends Livewire
{
    public bool $generateUuids = true;

    public function form(Schema $form): Schema
    {
        $repeater = Repeater::make('items')
            ->generateUuidUsing($this->generateUuids)
            ->cloneable()
            ->reorderableWithButtons()
            ->afterStateUpdated(fn () => $this->dispatch('items-updated'))
            ->schema([
                TextInput::make('foo')->default(fn (): string => 'Draft ' . count($this->data['group']['items'] ?? [])),
            ])
            ->defaultItems(0)
            ->addBetweenAction(static fn (Action $action): Action => $action->visible());

        return $form->components([
            Section::make('Items')->statePath('group')->schema([$repeater]),
            TextInput::make('sibling')->default('Untouched'),
        ])->statePath('data');
    }
}

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

class RepeaterInStatePathAncestorSetByHook extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'trigger' => null,
            'group' => [
                'items' => [
                    ['name' => 'Original item'],
                ],
            ],
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                // The `Section` is deliberately registered before the `trigger` field, so that
                // the `afterStateUpdated` walk traverses it, and the `Repeater` caches its
                // items, before the `trigger` field's hook runs `$set()`.
                Section::make('Items')
                    ->statePath('group')
                    ->schema([
                        Repeater::make('items')
                            ->itemLabel(static fn (?array $state): string => $state['name'] ?? '')
                            ->schema([
                                TextInput::make('name'),
                            ]),
                    ]),
                TextInput::make('trigger')
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('group', [
                            'items' => [
                                ['name' => 'Original item'],
                                ['name' => 'Added item'],
                            ],
                        ]);
                    }),
            ])
            ->statePath('data');
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

class SelectWithOptionsEvaluatedInSetUp extends Select
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->options(function (): array {
            $name = $this->evaluate(fn (Get $get): ?string => $get('name'));

            return [$name => $name];
        });
    }
}

class TestComponentWithFixIndistinctStateRepeater extends Livewire
{
    public string $fieldClass = Select::class;

    public array | string | null $options = null;

    public bool $isMultiple = false;

    public array $initialItems = [];

    public array $submittedData = [];

    public function mount(): void
    {
        $this->form->fill(['items' => $this->initialItems]);
    }

    public function form(Schema $form): Schema
    {
        $field = $this->fieldClass::make('choice')->fixIndistinctState();

        if ($this->options !== null) {
            $field->options($this->options);
        }

        if ($this->isMultiple) {
            $field->multiple();
        }

        return $form
            ->components([
                Repeater::make('items')
                    ->generateUuidUsing(false)
                    ->schema([$field]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->submittedData = $this->form->getState();
    }
}

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

class RepeaterWithHasManyRelationshipJoinAndOrderColumn extends Component implements HasActions, HasSchemas
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
                        // The join brings in a second `created_at` column, so the order column must be
                        // qualified to avoid an ambiguous column error when existing records are loaded.
                        modifyQueryUsing: fn ($query) => $query->join('users', 'users.id', '=', 'posts.author_id'),
                    )
                    ->orderColumn('created_at')
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
