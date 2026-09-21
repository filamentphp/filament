<?php

use Filament\Actions\Action;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Enums\Alignment;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('displays blocks in builder', function (): void {
    $data = [
        'builder' => [
            [
                'type' => 'one',
                'data' => [
                    'foo' => 'test',
                ],
            ],
        ],
    ];

    livewire(TestComponentWithBuilder::class)
        ->assertSuccessful()
        ->fillForm($data)
        ->assertSchemaStateSet($data);
});

describe('`distinct()` validation', function (): void {
    it('can validate `distinct()` fields in blocks in a builder with errors', function (): void {
        $data = [
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
        ];

        livewire(TestComponentWithBuilder::class)
            ->assertSuccessful()
            ->fillForm($data)
            ->assertSchemaStateSet($data)
            ->call('save')
            ->assertHasFormErrors(['builder.0.data.foo' => ['The foo field has a duplicate value.'], 'builder.1.data.foo' => ['The foo field has a duplicate value.']]);
    });

    it('can validate `distinct()` fields in blocks in a builder with no errors', function (): void {
        $data = [
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
        ];

        livewire(TestComponentWithBuilder::class)
            ->assertSuccessful()
            ->fillForm($data)
            ->assertSchemaStateSet($data)
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('can validate `distinct()` fields in a `Repeater` in a builder block with errors', function (): void {
        $data = [
            'builder' => [
                [
                    'type' => 'one',
                    'data' => [
                        'foo' => 'test 1',
                        'repeater' => [
                            [
                                'bar' => 'test 1',
                            ],
                            [
                                'bar' => 'test 1',
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'one',
                    'data' => [
                        'foo' => 'test 1',
                        'repeater' => [
                            [
                                'bar' => 'test 1',
                            ],
                            [
                                'bar' => 'test 1',
                            ],
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

    it('can validate `distinct()` fields in a `Repeater` in a builder block with no errors', function (): void {
        $data = [
            'builder' => [
                [
                    'type' => 'one',
                    'data' => [
                        'foo' => 'test 1',
                        'repeater' => [
                            [
                                'bar' => 'test 1',
                            ],
                            [
                                'bar' => 'test 2',
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'one',
                    'data' => [
                        'foo' => 'test 2',
                        'repeater' => [
                            [
                                'bar' => 'test 1',
                            ],
                            [
                                'bar' => 'test 2',
                            ],
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
});

class TestComponentWithBuilder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Builder::make('builder')
                    ->blocks([
                        Builder\Block::make('one')
                            ->schema([
                                TextInput::make('foo')
                                    ->distinct(),
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
                                TextInput::make('foo')
                                    ->distinct(),
                                Repeater::make('repeater')
                                    ->schema([
                                        TextInput::make('bar')
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

it('can access correct block schema state from action directly in builder schema', function (): void {
    $undoBuilderFake = Builder::fake();

    livewire(TestComponentWithActionInBuilder::class)
        ->callAction(
            TestAction::make('captureSchemaState')
                ->schemaComponent('blocks.0.data'),
        )
        ->assertDispatched('state-captured', state: [
            'content' => 'Block 1 content',
        ])
        ->callAction(
            TestAction::make('captureSchemaState')
                ->schemaComponent('blocks.1.data'),
        )
        ->assertDispatched('state-captured', state: [
            'content' => 'Block 2 content',
        ]);

    $undoBuilderFake();
});

it('can access correct block state from `extraItemActions()`', function (): void {
    $undoBuilderFake = Builder::fake();

    livewire(TestComponentWithExtraItemActionInBuilder::class)
        ->callAction(
            TestAction::make('captureBlockState')
                ->schemaComponent('blocks')
                ->arguments(['item' => 0]),
        )
        ->assertDispatched('state-captured', state: [
            'content' => 'First Block',
        ])
        ->callAction(
            TestAction::make('captureBlockState')
                ->schemaComponent('blocks')
                ->arguments(['item' => 1]),
        )
        ->assertDispatched('state-captured', state: [
            'content' => 'Second Block',
        ]);

    $undoBuilderFake();
});

it('can save a builder block containing a repeater and hidden field', function (): void {
    livewire(TestComponentWithBuilderRepeaterAndHiddenField::class)
        ->assertSuccessful()
        ->call('save')
        ->assertHasNoFormErrors();
});

class TestComponentWithActionInBuilder extends Livewire
{
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Builder::make('blocks')
                    ->blocks([
                        Builder\Block::make('text')
                            ->schema([
                                TextInput::make('content'),
                                Action::make('captureSchemaState')
                                    ->action(function (array $schemaState): void {
                                        $this->dispatch('state-captured', state: $schemaState);
                                    }),
                            ]),
                    ])
                    ->default([
                        ['type' => 'text', 'data' => ['content' => 'Block 1 content']],
                        ['type' => 'text', 'data' => ['content' => 'Block 2 content']],
                    ]),
            ])
            ->statePath('data');
    }
}

class TestComponentWithExtraItemActionInBuilder extends Livewire
{
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Builder::make('blocks')
                    ->blocks([
                        Builder\Block::make('paragraph')
                            ->schema([
                                TextInput::make('content'),
                            ]),
                    ])
                    ->extraItemActions([
                        Action::make('captureBlockState')
                            ->action(function (array $schemaState): void {
                                $this->dispatch('state-captured', state: $schemaState);
                            }),
                    ])
                    ->default([
                        ['type' => 'paragraph', 'data' => ['content' => 'First Block']],
                        ['type' => 'paragraph', 'data' => ['content' => 'Second Block']],
                    ]),
            ])
            ->statePath('data');
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
                                        TextInput::make('service')
                                            ->required(),
                                    ]),
                                TextInput::make('hidden')
                                    ->visible(false),
                            ]),
                    ])
                    ->default([
                        [
                            'type' => 'services',
                            'data' => [
                                'items' => [
                                    [
                                        'service' => 'Service 1',
                                    ],
                                ],
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

describe('properties', function (): void {
    it('can set `addActionLabel()` and get with `getAddActionLabel()`', function (): void {
        $builder = Builder::make('content')
            ->addActionLabel('Add content block');

        expect($builder->getAddActionLabel())->toBe('Add content block');
    });

    it('can set `addBetweenActionLabel()` and get with `getAddBetweenActionLabel()`', function (): void {
        $builder = Builder::make('content')
            ->addBetweenActionLabel('Insert block here');

        expect($builder->getAddBetweenActionLabel())->toBe('Insert block here');
    });

    it('can set `labelBetweenItems()` and get with `getLabelBetweenItems()`', function (): void {
        $builder = Builder::make('content')
            ->labelBetweenItems('or');

        expect($builder->getLabelBetweenItems())->toBe('or');
    });

    it('returns `null` for `getLabelBetweenItems()` by default', function (): void {
        $builder = Builder::make('content');

        expect($builder->getLabelBetweenItems())->toBeNull();
    });

    it('can set `reorderableWithDragAndDrop()` to false and check `isReorderableWithDragAndDrop()`', function (): void {
        $builder = Builder::make('content')
            ->reorderableWithDragAndDrop(false);

        expect($builder->isReorderableWithDragAndDrop())->toBeFalse();
    });

    it('can set `blockLabels()` and check `hasBlockLabels()`', function (): void {
        $withLabels = Builder::make('content')->blockLabels();
        $withoutLabels = Builder::make('content')->blockLabels(false);

        expect($withLabels->hasBlockLabels())->toBeTrue();
        expect($withoutLabels->hasBlockLabels())->toBeFalse();
    });

    it('has `hasBlockLabels()` returning true by default', function (): void {
        $builder = Builder::make('content');

        expect($builder->hasBlockLabels())->toBeTrue();
    });

    it('can set `blockNumbers()` and check `hasBlockNumbers()`', function (): void {
        $withNumbers = Builder::make('content')->blockNumbers();
        $withoutNumbers = Builder::make('content')->blockNumbers(false);

        expect($withNumbers->hasBlockNumbers())->toBeTrue();
        expect($withoutNumbers->hasBlockNumbers())->toBeFalse();
    });

    it('has `hasBlockNumbers()` returning true by default', function (): void {
        $builder = Builder::make('content');

        expect($builder->hasBlockNumbers())->toBeTrue();
    });

    it('can set `blockIcons()` and check `hasBlockIcons()`', function (): void {
        $withIcons = Builder::make('content')->blockIcons();
        $withoutIcons = Builder::make('content')->blockIcons(false);

        expect($withIcons->hasBlockIcons())->toBeTrue();
        expect($withoutIcons->hasBlockIcons())->toBeFalse();
    });

    it('has `hasBlockIcons()` returning false by default', function (): void {
        $builder = Builder::make('content');

        expect($builder->hasBlockIcons())->toBeFalse();
    });

    it('can set `blockHeaders()` and check `hasBlockHeaders()`', function (): void {
        $withHeaders = Builder::make('content')->blockHeaders();
        $withoutHeaders = Builder::make('content')->blockHeaders(false);

        expect($withHeaders->hasBlockHeaders())->toBeTrue();
        expect($withoutHeaders->hasBlockHeaders())->toBeFalse();
    });

    it('has `hasBlockHeaders()` returning true by default', function (): void {
        $builder = Builder::make('content');

        expect($builder->hasBlockHeaders())->toBeTrue();
    });

    it('can set `blockPreviews()` and check `hasBlockPreviews()`', function (): void {
        $withPreviews = Builder::make('content')->blockPreviews();
        $withoutPreviews = Builder::make('content')->blockPreviews(false);

        expect($withPreviews->hasBlockPreviews())->toBeTrue();
        expect($withoutPreviews->hasBlockPreviews())->toBeFalse();
    });

    it('has `hasBlockPreviews()` returning false by default', function (): void {
        $builder = Builder::make('content');

        expect($builder->hasBlockPreviews())->toBeFalse();
    });

    it('can set `blockPreviews()` with interactive and check `hasInteractiveBlockPreviews()`', function (): void {
        $interactive = Builder::make('content')->blockPreviews(areInteractive: true);
        $nonInteractive = Builder::make('content')->blockPreviews();

        expect($interactive->hasInteractiveBlockPreviews())->toBeTrue();
        expect($nonInteractive->hasInteractiveBlockPreviews())->toBeFalse();
    });

    it('can set `truncateBlockLabel()` and check `isBlockLabelTruncated()`', function (): void {
        $truncated = Builder::make('content')->truncateBlockLabel();
        $notTruncated = Builder::make('content')->truncateBlockLabel(false);

        expect($truncated->isBlockLabelTruncated())->toBeTrue();
        expect($notTruncated->isBlockLabelTruncated())->toBeFalse();
    });

    it('has `isBlockLabelTruncated()` returning true by default', function (): void {
        $builder = Builder::make('content');

        expect($builder->isBlockLabelTruncated())->toBeTrue();
    });

    it('can set `partiallyRenderAfterActionsCalled()` and check `shouldPartiallyRenderAfterActionsCalled()`', function (): void {
        $enabled = Builder::make('content')->partiallyRenderAfterActionsCalled();
        $disabled = Builder::make('content')->partiallyRenderAfterActionsCalled(false);
        $liveEnabled = Builder::make('content')->live()->partiallyRenderAfterActionsCalled();

        expect($enabled->shouldPartiallyRenderAfterActionsCalled())->toBeTrue();
        expect($disabled->shouldPartiallyRenderAfterActionsCalled())->toBeFalse();
        expect($liveEnabled->shouldPartiallyRenderAfterActionsCalled())->toBeTrue();
    });

    it('can set `addActionAlignment()` and get with `getAddActionAlignment()`', function (): void {
        $builder = Builder::make('content')
            ->addActionAlignment('center');

        expect($builder->getAddActionAlignment())->toBe(Alignment::Center);
    });

    it('returns `null` for `getAddActionAlignment()` by default', function (): void {
        $builder = Builder::make('content');

        expect($builder->getAddActionAlignment())->toBeNull();
    });

    it('can set `blockPickerColumns()` and get with `getBlockPickerColumns()`', function (): void {
        $builder = Builder::make('content')
            ->blockPickerColumns(3);

        expect($builder->getBlockPickerColumns('lg'))->toBe(3);
    });

    it('can set `blockPickerWidth()` and get with `getBlockPickerWidth()`', function (): void {
        $builder = Builder::make('content')
            ->blockPickerWidth('xl');

        expect($builder->getBlockPickerWidth())->toBe('xl');
    });

    it('can return correct action names', function (): void {
        $builder = Builder::make('content');

        expect($builder->getAddActionName())->toBe('add');
        expect($builder->getAddBetweenActionName())->toBe('addBetween');
        expect($builder->getCloneActionName())->toBe('clone');
        expect($builder->getDeleteActionName())->toBe('delete');
        expect($builder->getMoveDownActionName())->toBe('moveDown');
        expect($builder->getMoveUpActionName())->toBe('moveUp');
        expect($builder->getReorderActionName())->toBe('reorder');
        expect($builder->getCollapseActionName())->toBe('collapse');
        expect($builder->getExpandActionName())->toBe('expand');
        expect($builder->getCollapseAllActionName())->toBe('collapseAll');
        expect($builder->getExpandAllActionName())->toBe('expandAll');
        expect($builder->getEditActionName())->toBe('edit');
    });
});

describe('rendering', function (): void {
    it('can render with `addable(false)`', function (): void {
        livewire(RenderBuilderWithNotAddable::class)->assertSuccessful();
    });

    it('can render with `addable()` set via `Closure`', function (): void {
        livewire(RenderBuilderWithClosureAddable::class)->assertSuccessful();
    });

    it('can render with `deletable(false)`', function (): void {
        livewire(RenderBuilderWithNotDeletable::class)->assertSuccessful();
    });

    it('can render with `deletable()` set via `Closure`', function (): void {
        livewire(RenderBuilderWithClosureDeletable::class)->assertSuccessful();
    });

    it('can render with `collapsible()`', function (): void {
        livewire(RenderBuilderWithCollapsible::class)->assertSuccessful();
    });

    it('can render with `blockLabels(false)`', function (): void {
        livewire(RenderBuilderWithNoBlockLabels::class)->assertSuccessful();
    });

    it('can render with `blockNumbers(false)`', function (): void {
        livewire(RenderBuilderWithNoBlockNumbers::class)->assertSuccessful();
    });

    it('can render with `blockHeaders(false)`', function (): void {
        livewire(RenderBuilderWithNoBlockHeaders::class)->assertSuccessful();
    });

    it('can render with `truncateBlockLabel(false)`', function (): void {
        livewire(RenderBuilderWithNoTruncateLabel::class)->assertSuccessful();
    });

    it('can render with `truncateBlockLabel()` set via `Closure`', function (): void {
        livewire(RenderBuilderWithClosureTruncateLabel::class)->assertSuccessful();
    });

    it('can render with `reorderableWithDragAndDrop(false)`', function (): void {
        livewire(RenderBuilderWithNoDragAndDrop::class)->assertSuccessful();
    });

    it('can render with `reorderableWithButtons()`', function (): void {
        livewire(RenderBuilderWithReorderButtons::class)->assertSuccessful();
    });

    it('can render with `blockPickerColumns()` responsive breakpoints', function (): void {
        livewire(RenderBuilderWithBlockPickerColumns::class)->assertSuccessful();
    });

    it('can render with `blockPickerWidth()` set via `Closure`', function (): void {
        livewire(RenderBuilderWithClosureBlockPickerWidth::class)->assertSuccessful();
    });

    it('can render with `labelBetweenItems()`', function (): void {
        livewire(RenderBuilderWithLabelBetweenItems::class)->assertSuccessful();
    });

    it('can render with `addActionLabel()` set via `Closure`', function (): void {
        livewire(RenderBuilderWithClosureAddActionLabel::class)->assertSuccessful();
    });

    it('can render with `cloneable()`', function (): void {
        livewire(RenderBuilderWithCloneable::class)->assertSuccessful();
    });

    it('can render with `addActionAlignment()`', function (): void {
        livewire(RenderBuilderWithAddActionAlignment::class)->assertSuccessful();
    });

    it('can render with `reorderable(false)`', function (): void {
        livewire(RenderBuilderWithNotReorderable::class)->assertSuccessful();
    });

    it('can render with `reorderable()` set via `Closure`', function (): void {
        livewire(RenderBuilderWithClosureReorderable::class)->assertSuccessful();
    });
});

describe('block picker search', function (): void {
    it('can configure `searchable()`', function (): void {
        $builder = Builder::make('content');

        expect($builder->isSearchable())->toBeFalse()
            ->and($builder->searchable()->isSearchable())->toBeTrue()
            ->and($builder->searchable(false)->isSearchable())->toBeFalse()
            ->and($builder->searchable(static fn (): bool => true)->isSearchable())->toBeTrue()
            ->and($builder->searchable(null)->isSearchable())->toBeFalse()
            ->and($builder->searchable()->searchable(static fn () => null)->isSearchable())->toBeFalse();
    });

    it('returns default translations for `getSearchPrompt()` and `getNoSearchResultsMessage()`', function (): void {
        $builder = Builder::make('content');

        expect($builder->getSearchPrompt())->toBe(__('filament-forms::components.builder.block_picker.search_prompt'))
            ->and($builder->getNoSearchResultsMessage())->toBe(__('filament-forms::components.builder.block_picker.no_search_results_message'));
    });

    it('can set `searchPrompt()`, `noSearchResultsMessage()`, and `searchDebounce()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->searchPrompt(static fn (): string => 'Find a block')
            ->noSearchResultsMessage(static fn (): string => 'Nothing found.')
            ->searchDebounce(static fn (): int => 500);

        expect($builder->getSearchPrompt())->toBe('Find a block')
            ->and($builder->getNoSearchResultsMessage())->toBe('Nothing found.')
            ->and($builder->getSearchDebounce())->toBe(500);
    });

    it('returns `0` for `getSearchDebounce()` by default', function (): void {
        expect(Builder::make('content')->getSearchDebounce())->toBe(0);
    });

    it('can set and reset the block picker search configuration', function (): void {
        $builder = Builder::make('content')
            ->searchPrompt('Find')
            ->noSearchResultsMessage('Empty')
            ->searchDebounce(300);

        expect($builder->getSearchPrompt())->toBe('Find')
            ->and($builder->getNoSearchResultsMessage())->toBe('Empty')
            ->and($builder->getSearchDebounce())->toBe(300)
            ->and($builder->searchPrompt(null)->getSearchPrompt())->toBe(__('filament-forms::components.builder.block_picker.search_prompt'))
            ->and($builder->noSearchResultsMessage(null)->getNoSearchResultsMessage())->toBe(__('filament-forms::components.builder.block_picker.no_search_results_message'))
            ->and($builder->searchDebounce(0)->getSearchDebounce())->toBe(0);
    });

    it('preserves literal text in `searchPrompt()`', function (): void {
        expect(Builder::make('content')->searchPrompt('<Find> &amp; "R&D"')->getSearchPrompt())
            ->toBe('<Find> &amp; "R&D"');
    });

    it('preserves an `Htmlable` in `getSearchPrompt()` and accepts the `message` named argument', function (): void {
        $message = new HtmlString('<strong>Find &quot;R&amp;D&quot;</strong>');
        $builder = Builder::make('content');

        expect($builder->searchPrompt(message: $message))->toBe($builder)
            ->and($builder->getSearchPrompt())->toBe($message)
            ->and($builder->searchPrompt(message: static fn (): HtmlString => $message)->getSearchPrompt())->toBe($message);
    });

    it('renders search labels and safely escapes an `Htmlable` search prompt', function (bool $hasPublishedView): void {
        $cache = new ReflectionProperty(ViewComponent::class, 'hasPublishedEmbeddedViewOverrideCache');
        $originalCache = $cache->getValue();
        $cache->setValue(null, [
            ...$originalCache,
            'filament-forms::components.builder.block-picker' => $hasPublishedView,
        ]);

        try {
            livewire(RenderBuilderWithSearchableBlocks::class)
                ->assertSuccessful()
                ->assertSeeHtml('placeholder="Find &quot;R&amp;D&quot;"')
                ->assertSeeHtml('aria-label="Find &quot;R&amp;D&quot;"')
                ->assertSeeHtml('data-block-label="paragraph"')
                ->assertSeeHtml('data-block-label="editor&#039;s picks"')
                ->assertSeeHtml('<strong>No matching blocks</strong>')
                ->assertDontSeeHtml('<span title="Search">');
        } finally {
            $cache->setValue(null, $originalCache);
        }
    })->with(['embedded' => false, 'published Blade' => true]);

    it('does not render search markup when not `searchable()`', function (): void {
        livewire(TestComponentWithBuilder::class)
            ->assertSuccessful()
            ->assertDontSeeHtml('data-dropdown-autofocus')
            ->assertDontSeeHtml('data-block-label')
            ->assertDontSeeHtml('builderBlockPickerFormComponent');
    });
});

it('can add and delete blocks in the browser', function (): void {
    retry(10, function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        visit('/builder-test')
            ->assertNotPresent('[data-testid="builder"] .fi-fo-builder-item')
            ->click('text=Add to content')
            ->click('text=Paragraph')
            ->wait(1)
            ->assertPresent('[data-testid="builder"] .fi-fo-builder-item')
            ->click('text=Add to content')
            ->click('text=Heading')
            ->wait(1)
            ->assertCount('[data-testid="builder"] .fi-fo-builder-item', 2)
            ->click('[data-testid="builder"] .fi-fo-builder-items > .fi-fo-builder-item:last-child .fi-fo-builder-item-header-end-actions button')
            ->wait(1)
            ->assertCount('[data-testid="builder"] .fi-fo-builder-item', 1)
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/builder-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

it('can search blocks in the picker in the browser', function (bool $isDarkMode): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $addBlockAction = '[data-testid="add-block"]';
    $noSearchResultsMessage = '[data-testid="builder"] [role="status"]';
    $searchInput = '[data-testid="builder"] input[type="search"]';
    $page = visit('/builder-searchable-test');

    if ($isDarkMode) {
        $page = $page->inDarkMode();
    }

    $page
        ->click($addBlockAction)
        ->assertVisible($searchInput)
        ->assertScript('document.activeElement.matches(\'[data-testid="builder"] input[type="search"]\')', true)
        ->type($searchInput, 'ReSeArCh & DEVELOPMENT')
        ->assertVisible('[data-testid="builder"] [data-block-label="research & development"]')
        ->assertMissing('[data-testid="builder"] [data-block-label="paragraph"]')
        ->type($searchInput, 'zzz')
        ->assertVisible($noSearchResultsMessage)
        ->keys($searchInput, 'Escape')
        ->assertValue($searchInput, '')
        ->assertVisible($searchInput)
        ->assertVisible('[data-testid="builder"] [data-block-label="paragraph"]')
        ->keys($searchInput, 'Escape')
        ->assertMissing($searchInput)
        ->assertScript('document.activeElement.closest(\'[data-testid="add-block"]\') !== null', true)
        ->click($addBlockAction)
        ->type($searchInput, 'video')
        ->click('[data-testid="builder"] [data-block-label="video"]')
        ->assertCount('[data-testid="builder"] .fi-fo-builder-item', 1)
        ->click($addBlockAction)
        ->type($searchInput, 'video')
        ->assertVisible($noSearchResultsMessage)
        ->assertNoSmoke()
        ->assertScript('document.querySelector(\'[data-testid="builder"]\').getAnimations({ subtree: true }).length', 0)
        ->assertNoAccessibilityIssues();
})->with(['light' => false, 'dark' => true]);

it('does not submit the surrounding form when `Enter` is pressed in the block picker search input', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $searchInput = '[data-testid="builder"] input[type="search"]';

    visit('/builder-searchable-test')
        ->assertScript('(() => { window.builderFormSubmitCount = 0; document.querySelector(\'[data-testid="builder"]\').closest(\'form\').addEventListener(\'submit\', () => window.builderFormSubmitCount++); return window.builderFormSubmitCount })()', 0)
        ->click('[data-testid="add-block"]')
        ->assertVisible($searchInput)
        ->type($searchInput, 'para')
        ->keys($searchInput, 'Enter')
        ->wait(1)
        ->assertScript('window.builderFormSubmitCount', 0)
        ->assertVisible($searchInput)
        ->assertValue($searchInput, 'para')
        ->assertNoSmoke();
});

it('clears a debounced block picker search with `Escape` before the debounce elapses', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $searchInput = '[data-testid="debounced-builder"] input[type="search"]';

    visit('/builder-searchable-test')
        ->click('[data-testid="add-debounced-block"]')
        ->assertVisible($searchInput)
        ->type($searchInput, 'zzz')
        ->keys($searchInput, 'Escape')
        ->assertVisible($searchInput)
        ->assertValue($searchInput, '')
        // Wait for the debounce to elapse, so the cleared input is not overwritten by the stale search.
        ->wait(1.5)
        ->assertVisible($searchInput)
        ->assertValue($searchInput, '')
        ->assertVisible('[data-testid="debounced-builder"] [data-block-label="paragraph"]')
        ->assertMissing('[data-testid="debounced-builder"] [role="status"]')
        ->assertNoSmoke();
});

it('closes only the block picker with `Escape` when it is inside a modal', function (bool $isDarkMode): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $modal = '[data-testid="builder-modal"]';
    $addBlockAction = '[data-testid="add-modal-block"]';
    $searchInput = '[data-testid="modal-builder"] input[type="search"]';
    $page = visit('/builder-searchable-test');

    if ($isDarkMode) {
        $page = $page->inDarkMode();
    }

    $page
        ->click('[data-testid="modal-builder-trigger"]')
        ->assertVisible($modal)
        ->assertScript('document.querySelector(\'[data-testid="builder-modal"]\').contains(document.activeElement)', true)
        ->click($addBlockAction)
        ->assertVisible($searchInput)
        ->assertScript('document.querySelector(\'[data-testid="builder-modal"]\').getAnimations({ subtree: true }).length', 0)
        ->assertNoAccessibilityIssues()
        ->type($searchInput, 'zzz')
        ->keys($searchInput, 'Escape')
        ->assertValue($searchInput, '')
        ->assertVisible($searchInput)
        ->assertVisible($modal)
        ->keys($searchInput, 'Escape')
        ->assertMissing($searchInput)
        ->assertVisible($modal)
        ->assertScript('document.activeElement.closest(\'[data-testid="add-modal-block"]\') !== null', true)
        ->keys($addBlockAction, 'Escape')
        ->assertMissing($modal)
        ->assertNoSmoke();
})->with(['light' => false, 'dark' => true]);

it('clears and focuses the block picker search after clicking away and reopening', function (bool $isDarkMode): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $searchInput = '[data-testid="builder"] input[type="search"]';
    $page = visit('/builder-searchable-test');

    if ($isDarkMode) {
        $page = $page->inDarkMode();
    }

    $page
        ->click('[data-testid="add-block"]')
        ->type($searchInput, 'video')
        ->assertVisible('[data-testid="builder"] [data-block-label="video"]')
        ->assertMissing('[data-testid="builder"] [data-block-label="paragraph"]')
        ->click('[data-testid="outside-picker"]')
        ->assertMissing($searchInput)
        ->click('[data-testid="add-block"]')
        ->assertVisible($searchInput)
        ->assertValue($searchInput, '')
        ->assertScript('document.activeElement.matches(\'[data-testid="builder"] input[type="search"]\')', true)
        ->assertVisible('[data-testid="builder"] [data-block-label="paragraph"]')
        ->assertVisible('[data-testid="builder"] [data-block-label="research & development"]')
        ->assertVisible('[data-testid="builder"] [data-block-label="video"]')
        ->assertMissing('[data-testid="builder"] [role="status"]')
        ->assertNoSmoke()
        ->assertScript('document.querySelector(\'[data-testid="builder"]\').getAnimations({ subtree: true }).length', 0)
        ->assertNoAccessibilityIssues();
})->with(['light' => false, 'dark' => true]);

it('searches independently in the add-between picker and inserts the selected block in order', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $endPicker = '[data-testid="builder"] > .fi-fo-builder-block-picker';
    $betweenPicker = '[data-testid="builder"] .fi-fo-builder-add-between-items-ctn';

    visit('/builder-searchable-test')
        ->click('[data-testid="add-block"]')
        ->click($endPicker . ' [data-block-label="paragraph"]')
        ->assertCount('[data-testid="builder"] .fi-fo-builder-item', 1)
        ->click('[data-testid="add-block"]')
        ->click($endPicker . ' [data-block-label="research & development"]')
        ->assertCount('[data-testid="builder"] .fi-fo-builder-item', 2)
        ->click('[data-testid="add-block"]')
        ->type($endPicker . ' input[type="search"]', 'paragraph')
        ->click('[data-testid="outside-picker"]')
        ->hover('[data-testid="builder"] .fi-fo-builder-item:first-child')
        ->click($betweenPicker . ' .fi-dropdown-trigger button')
        ->assertValue($betweenPicker . ' input[type="search"]', '')
        ->type($betweenPicker . ' input[type="search"]', 'video')
        ->assertMissing($betweenPicker . ' [data-block-label="paragraph"]')
        ->assertValue($endPicker . ' input[type="search"]', 'paragraph')
        ->click($betweenPicker . ' [data-block-label="video"]')
        ->assertCount('[data-testid="builder"] .fi-fo-builder-item', 3)
        ->assertScript('Array.from(document.querySelectorAll(\'[data-testid="builder"] .fi-fo-builder-item input\'), input => input.id.split(\'.\').pop())', ['text', 'url', 'title'])
        ->assertNoSmoke();
});

it('preserves an active search when the block catalog changes', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $searchInput = '[data-testid="builder"] input[type="search"]';
    $page = visit('/builder-searchable-test')
        ->click('[data-testid="add-block"]')
        ->type($searchInput, 'video')
        ->assertVisible('[data-testid="builder"] [data-block-label="video"]');

    $page->script('Alpine.$data(document.querySelector(\'[data-testid="builder"]\')).$wire.$set(\'hasUpdatedBlocks\', true)');

    $page
        ->assertValue($searchInput, 'video')
        ->assertVisible('[data-testid="builder"] [data-block-label="video heading"]')
        ->assertVisible('[data-testid="builder"] [data-block-label="video quote"]')
        ->assertNotPresent('[data-testid="builder"] [data-block-label="video"]')
        ->assertMissing('[data-testid="builder"] [data-block-label="introduction"]')
        ->assertMissing('[data-testid="builder"] [role="status"]')
        ->assertScript('document.activeElement.matches(\'[data-testid="builder"] input[type="search"]\')', true);

    $page->script('Alpine.$data(document.querySelector(\'[data-testid="builder"]\')).$wire.$set(\'hasUpdatedBlocks\', false)');

    $page
        ->assertVisible('[data-testid="builder"] [data-block-label="video"]')
        ->type($searchInput, 'quote')
        ->assertVisible('[data-testid="builder"] [role="status"]');

    $page->script('Alpine.$data(document.querySelector(\'[data-testid="builder"]\')).$wire.$set(\'hasUpdatedBlocks\', true)');

    $page
        ->assertValue($searchInput, 'quote')
        ->assertVisible('[data-testid="builder"] [data-block-label="video quote"]')
        ->assertMissing('[data-testid="builder"] [role="status"]')
        ->click('[data-testid="builder"] [data-block-label="video quote"]')
        ->assertVisible('[data-testid="builder"] input[id$=".quotation"]')
        ->assertCount('[data-testid="builder"] .fi-fo-builder-item', 1)
        ->assertNoSmoke();
});

it('focuses the search when blocks become available and after deleting the last allowed block', function (bool $hasPublishedView): void {
    $cache = new ReflectionProperty(ViewComponent::class, 'hasPublishedEmbeddedViewOverrideCache');
    $originalCache = $cache->getValue();
    $cache->setValue(null, [
        ...$originalCache,
        'filament-forms::components.builder.block-picker' => $hasPublishedView,
    ]);

    try {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        $searchInput = '[data-testid="builder"] input[type="search"]';
        $page = visit('/builder-searchable-test?empty=1&limited=1')
            ->assertNotPresent($searchInput);

        $page->script('Alpine.$data(document.querySelector(\'[data-testid="builder"]\')).$wire.$set(\'hasNoBlocks\', false)');

        $page
            ->click('[data-testid="add-block"]')
            ->assertVisible($searchInput)
            ->assertAttribute('[data-testid="add-block"]', 'aria-expanded', 'true')
            ->assertScript('document.activeElement.matches(\'[data-testid="builder"] input[type="search"]\')', true)
            ->click('[data-testid="builder"] [data-block-label="video"]')
            ->assertCount('[data-testid="builder"] .fi-fo-builder-item', 1)
            ->assertNotPresent($searchInput)
            ->click('[data-testid="builder"] .fi-fo-builder-item-header-end-actions button')
            ->assertNotPresent('[data-testid="builder"] .fi-fo-builder-item')
            ->click('[data-testid="add-block"]')
            ->assertVisible($searchInput)
            ->assertAttribute('[data-testid="add-block"]', 'aria-expanded', 'true')
            ->assertScript('document.activeElement.matches(\'[data-testid="builder"] input[type="search"]\')', true)
            ->assertNoSmoke();
    } finally {
        $cache->setValue(null, $originalCache);
    }
})->with(['embedded' => false, 'published Blade' => true]);

it('updates a dynamic `blockPickerWidth()`', function (bool $isSearchable): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $page = visit('/builder-searchable-test?notSearchable=' . (int) (! $isSearchable))
        ->click('[data-testid="add-block"]')
        ->assertVisible('[data-testid="builder"] .fi-dropdown-panel.fi-width-sm');

    $page->script('Alpine.$data(document.querySelector(\'[data-testid="builder"]\')).$wire.$set(\'hasWidePicker\', true)');

    $page
        ->assertPresent('[data-testid="builder"] .fi-dropdown-panel.fi-width-lg')
        ->assertNotPresent('[data-testid="builder"] .fi-dropdown-panel.fi-width-sm')
        ->click('[data-testid="add-block"]')
        ->assertVisible('[data-testid="builder"] .fi-dropdown-panel.fi-width-lg')
        ->assertScript('getComputedStyle(document.querySelector(\'[data-testid="builder"] .fi-dropdown-panel\')).opacity', '1')
        ->assertNoSmoke();
})->with(['searchable' => true, 'not searchable' => false]);

it('returns `1` for `getHeadingsCount()` when block labels are enabled (default)', function (): void {
    $builder = Builder::make('content');

    expect($builder->getHeadingsCount())->toBe(1);
});

it('returns `0` for `getHeadingsCount()` when block labels are disabled', function (): void {
    $builder = Builder::make('content')
        ->blockLabels(false);

    expect($builder->getHeadingsCount())->toBe(0);
});

it('returns fluent `$this` from `blocks()`', function (): void {
    $builder = Builder::make('content');

    $result = $builder->blocks([]);

    expect($result)->toBe($builder);
});

it('can set `blockPickerColumns()` with responsive breakpoints', function (): void {
    $builder = Builder::make('content')
        ->blockPickerColumns(['default' => 1, 'sm' => 2, 'lg' => 3]);

    expect($builder->getBlockPickerColumns('default'))->toBe(1);
    expect($builder->getBlockPickerColumns('sm'))->toBe(2);
    expect($builder->getBlockPickerColumns('lg'))->toBe(3);
    expect($builder->getBlockPickerColumns('xl'))->toBeNull();
});

it('can set `blockPickerColumns()` with a `Closure`', function (): void {
    $builder = Builder::make('content')
        ->blockPickerColumns(static fn (): int => 2);

    expect($builder->getBlockPickerColumns('lg'))->toBe(2);
});

it('can set `addActionLabel()` with a `Closure`', function (): void {
    $builder = Builder::make('content')
        ->addActionLabel(static fn (): string => 'Add custom block');

    expect($builder->getAddActionLabel())->toBe('Add custom block');
});

it('can set `blockPickerWidth()` with a `Closure`', function (): void {
    $builder = Builder::make('content')
        ->blockPickerWidth(static fn (): string => 'lg');

    expect($builder->getBlockPickerWidth())->toBe('lg');
});

it('can set `truncateBlockLabel()` with a `Closure`', function (): void {
    $builder = Builder::make('content')
        ->truncateBlockLabel(static fn (): bool => false);

    expect($builder->isBlockLabelTruncated())->toBeFalse();
});

it('returns `false` for `hasBlockPreviews()` by default', function (): void {
    $builder = Builder::make('content');

    expect($builder->hasBlockPreviews())->toBeFalse();
    expect($builder->hasInteractiveBlockPreviews())->toBeFalse();
});

describe('boolean properties', function (): void {
    it('defaults `isAddable()` to `true`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content'),
            ])
            ->fill();

        expect($builder->isAddable())->toBeTrue();
    });

    it('can set `addable()` to `false`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')->addable(false),
            ])
            ->fill();

        expect($builder->isAddable())->toBeFalse();
    });

    it('can set `addable()` with a `Closure`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')
                    ->addable(static fn (): bool => false),
            ])
            ->fill();

        expect($builder->isAddable())->toBeFalse();
    });

    it('defaults `isDeletable()` to `true`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content'),
            ])
            ->fill();

        expect($builder->isDeletable())->toBeTrue();
    });

    it('can set `deletable()` to `false`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')->deletable(false),
            ])
            ->fill();

        expect($builder->isDeletable())->toBeFalse();
    });

    it('can set `deletable()` with a `Closure`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')
                    ->deletable(static fn (): bool => false),
            ])
            ->fill();

        expect($builder->isDeletable())->toBeFalse();
    });

    it('defaults `isReorderable()` to `true`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content'),
            ])
            ->fill();

        expect($builder->isReorderable())->toBeTrue();
    });

    it('can set `reorderable()` to `false`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')->reorderable(false),
            ])
            ->fill();

        expect($builder->isReorderable())->toBeFalse();
    });

    it('can set `reorderable()` with a `Closure`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')
                    ->reorderable(static fn (): bool => false),
            ])
            ->fill();

        expect($builder->isReorderable())->toBeFalse();
    });

    it('defaults `isReorderableWithButtons()` to `false`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content'),
            ])
            ->fill();

        expect($builder->isReorderableWithButtons())->toBeFalse();
    });

    it('can set `reorderableWithButtons()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')->reorderableWithButtons(),
            ])
            ->fill();

        expect($builder->isReorderableWithButtons())->toBeTrue();
    });

    it('can set `reorderableWithButtons()` with a `Closure`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')
                    ->reorderableWithButtons(static fn (): bool => true),
            ])
            ->fill();

        expect($builder->isReorderableWithButtons())->toBeTrue();
    });

    it('defaults `isReorderableWithDragAndDrop()` to `true`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content'),
            ])
            ->fill();

        expect($builder->isReorderableWithDragAndDrop())->toBeTrue();
    });

    it('can set `reorderableWithDragAndDrop()` with a `Closure`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')
                    ->reorderableWithDragAndDrop(static fn (): bool => false),
            ])
            ->fill();

        expect($builder->isReorderableWithDragAndDrop())->toBeFalse();
    });

    it('can set `blockLabels()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->blockLabels(static fn (): bool => false);

        expect($builder->hasBlockLabels())->toBeFalse();
    });

    it('can set `blockNumbers()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->blockNumbers(static fn (): bool => false);

        expect($builder->hasBlockNumbers())->toBeFalse();
    });

    it('can set `blockIcons()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->blockIcons(static fn (): bool => true);

        expect($builder->hasBlockIcons())->toBeTrue();
    });

    it('can set `blockHeaders()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->blockHeaders(static fn (): bool => false);

        expect($builder->hasBlockHeaders())->toBeFalse();
    });

    it('can set `blockPreviews()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->blockPreviews(static fn (): bool => true);

        expect($builder->hasBlockPreviews())->toBeTrue();
    });

    it('can set `partiallyRenderAfterActionsCalled()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->partiallyRenderAfterActionsCalled(static fn (): bool => false);

        expect($builder->shouldPartiallyRenderAfterActionsCalled())->toBeFalse();
    });

    it('defaults `shouldPartiallyRenderAfterActionsCalled()` based on `live()`', function (): void {
        [$default, $live, $conditionallyLive, $conditionallyNotLive] = Schema::make(Livewire::make())
            ->components([
                Builder::make('default'),
                Builder::make('live')->live(),
                Builder::make('conditionallyLive')->live(condition: static fn (): bool => true),
                Builder::make('conditionallyNotLive')->live(condition: static fn (): bool => false),
            ])
            ->getComponents();

        expect($default->shouldPartiallyRenderAfterActionsCalled())->toBeTrue();
        expect($live->shouldPartiallyRenderAfterActionsCalled())->toBeFalse();
        expect($conditionallyLive->shouldPartiallyRenderAfterActionsCalled())->toBeFalse();
        expect($conditionallyNotLive->shouldPartiallyRenderAfterActionsCalled())->toBeTrue();
    });
});

describe('disabled interaction logic', function (): void {
    it('returns `false` for `isAddable()` when disabled', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')->disabled(),
            ])
            ->fill();

        expect($builder->isAddable())->toBeFalse();
    });

    it('returns `false` for `isDeletable()` when disabled', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')->disabled(),
            ])
            ->fill();

        expect($builder->isDeletable())->toBeFalse();
    });

    it('returns `false` for `isReorderable()` when disabled', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')->disabled(),
            ])
            ->fill();

        expect($builder->isReorderable())->toBeFalse();
    });

    it('returns `false` for `isReorderableWithDragAndDrop()` when `reorderable()` is `false`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')
                    ->reorderableWithDragAndDrop()
                    ->reorderable(false),
            ])
            ->fill();

        expect($builder->isReorderableWithDragAndDrop())->toBeFalse();
    });

    it('returns `false` for `isReorderableWithButtons()` when `reorderable()` is `false`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')
                    ->reorderableWithButtons()
                    ->reorderable(false),
            ])
            ->fill();

        expect($builder->isReorderableWithButtons())->toBeFalse();
    });
});

describe('cloning', function (): void {
    it('defaults `isCloneable()` to `false`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content'),
            ])
            ->fill();

        expect($builder->isCloneable())->toBeFalse();
    });

    it('can set `cloneable()`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')->cloneable(),
            ])
            ->fill();

        expect($builder->isCloneable())->toBeTrue();
    });

    it('can set `cloneable()` with a `Closure`', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')
                    ->cloneable(static fn (): bool => true),
            ])
            ->fill();

        expect($builder->isCloneable())->toBeTrue();
    });

    it('returns `false` for `isCloneable()` when disabled', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $builder = Builder::make('content')
                    ->cloneable()
                    ->disabled(),
            ])
            ->fill();

        expect($builder->isCloneable())->toBeFalse();
    });
});

describe('UUID generation', function (): void {
    it('generates a UUID by default', function (): void {
        $builder = Builder::make('content');

        $uuid = $builder->generateUuid();

        expect($uuid)->toBeString();
        expect($uuid)->not->toBeEmpty();
    });

    it('returns `null` from `generateUuid()` when set to `false`', function (): void {
        $builder = Builder::make('content')
            ->generateUuidUsing(false);

        expect($builder->generateUuid())->toBeNull();
    });

    it('can use custom UUID generator via `generateUuidUsing()`', function (): void {
        $builder = Builder::make('content')
            ->generateUuidUsing(static fn (): string => 'custom-uuid-123');

        expect($builder->generateUuid())->toBe('custom-uuid-123');
    });
});

describe('`hydrateItems()`', function (): void {
    it('rekeys items with UUIDs when hydrating from numeric-keyed `rawState`', function (): void {
        livewire(TestComponentWithBuilderFilledFromMount::class, [
            'initialData' => [
                ['type' => 'one', 'data' => ['foo' => 'a']],
                ['type' => 'one', 'data' => ['foo' => 'b']],
            ],
        ])->tap(function ($livewire): void {
            $items = $livewire->get('data.builder');

            expect($items)->toHaveCount(2);

            foreach (array_keys($items) as $key) {
                expect(is_numeric($key))->toBeFalse();
                expect($key)->toBeString();
            }
        });
    });

    it('rekeys items with numeric keys when `generateUuidUsing(false)` is set', function (): void {
        $undoBuilderFake = Builder::fake();

        livewire(TestComponentWithBuilderFilledFromMount::class, [
            'initialData' => [
                'existing-uuid-a' => ['type' => 'one', 'data' => ['foo' => 'a']],
                'existing-uuid-b' => ['type' => 'one', 'data' => ['foo' => 'b']],
            ],
        ])->tap(function ($livewire): void {
            $items = $livewire->get('data.builder');

            expect($items)->toHaveCount(2);
            expect(array_keys($items))->toBe([0, 1]);
        });

        $undoBuilderFake();
    });

    it('produces an empty array when `hydrateItems()` runs against empty `rawState`', function (): void {
        livewire(TestComponentWithBuilderFilledFromMount::class, ['initialData' => []])
            ->tap(function ($livewire): void {
                expect($livewire->get('data.builder'))->toBe([]);
            });
    });

    it('produces an empty array when `hydrateItems()` runs against `null` `rawState`', function (): void {
        livewire(TestComponentWithBuilderFilledFromMount::class, ['initialData' => null])
            ->tap(function ($livewire): void {
                expect($livewire->get('data.builder'))->toBe([]);
            });
    });
});

describe('item count limits', function (): void {
    it('returns `null` for `getMaxItems()` by default', function (): void {
        $builder = Builder::make('content');

        expect($builder->getMaxItems())->toBeNull();
    });

    it('can set `maxItems()`', function (): void {
        $builder = Builder::make('content')->maxItems(5);

        expect($builder->getMaxItems())->toBe(5);
    });

    it('can set `maxItems()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->maxItems(static fn (): int => 10);

        expect($builder->getMaxItems())->toBe(10);
    });

    it('returns `null` for `getMinItems()` by default', function (): void {
        $builder = Builder::make('content');

        expect($builder->getMinItems())->toBeNull();
    });

    it('can set `minItems()`', function (): void {
        $builder = Builder::make('content')->minItems(2);

        expect($builder->getMinItems())->toBe(2);
    });

    it('can set `minItems()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->minItems(static fn (): int => 3);

        expect($builder->getMinItems())->toBe(3);
    });
});

describe('reorder animation', function (): void {
    it('defaults `getReorderAnimationDuration()` to `300`', function (): void {
        $builder = Builder::make('content');

        expect($builder->getReorderAnimationDuration())->toBe(300);
    });

    it('can set `reorderAnimationDuration()`', function (): void {
        $builder = Builder::make('content')
            ->reorderAnimationDuration(500);

        expect($builder->getReorderAnimationDuration())->toBe(500);
    });

    it('can set `reorderAnimationDuration()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->reorderAnimationDuration(static fn (): int => 0);

        expect($builder->getReorderAnimationDuration())->toBe(0);
    });
});

describe('collapsing', function (): void {
    it('defaults `isCollapsible()` to `false`', function (): void {
        $builder = Builder::make('content');

        expect($builder->isCollapsible())->toBeFalse();
    });

    it('can set `collapsible()`', function (): void {
        $builder = Builder::make('content')->collapsible();

        expect($builder->isCollapsible())->toBeTrue();
    });

    it('can set `collapsible()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->collapsible(static fn (): bool => true);

        expect($builder->isCollapsible())->toBeTrue();
    });

    it('defaults `isCollapsed()` to `false`', function (): void {
        $builder = Builder::make('content');

        expect($builder->isCollapsed())->toBeFalse();
    });

    it('can set `collapsed()` and it makes the component collapsible', function (): void {
        $builder = Builder::make('content')->collapsed();

        expect($builder->isCollapsed())->toBeTrue();
        expect($builder->isCollapsible())->toBeTrue();
    });

    it('can set `collapsed()` without making collapsible using `shouldMakeComponentCollapsible: false`', function (): void {
        $builder = Builder::make('content')
            ->collapsed(true, shouldMakeComponentCollapsible: false);

        expect($builder->isCollapsed())->toBeTrue();
        expect($builder->isCollapsible())->toBeFalse();
    });

    it('defaults `shouldPersistCollapsed()` to `false`', function (): void {
        $builder = Builder::make('content');

        expect($builder->shouldPersistCollapsed())->toBeFalse();
    });

    it('can set `persistCollapsed()`', function (): void {
        $builder = Builder::make('content')->persistCollapsed();

        expect($builder->shouldPersistCollapsed())->toBeTrue();
    });

    it('can set `persistCollapsed()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->persistCollapsed(static fn (): bool => true);

        expect($builder->shouldPersistCollapsed())->toBeTrue();
    });
});

describe('`getBlockPickerWidth()` logic', function (): void {
    it('returns `null` when `blockPickerWidth()` is not set and columns are empty', function (): void {
        $builder = Builder::make('content')
            ->blockPickerColumns([]);

        expect($builder->getBlockPickerWidth())->toBeNull();
    });

    it('returns `md` when max column count is `2`', function (): void {
        $builder = Builder::make('content')
            ->blockPickerColumns(2);

        expect($builder->getBlockPickerWidth())->toBe('md');
    });

    it('returns `2xl` when max column count is `3`', function (): void {
        $builder = Builder::make('content')
            ->blockPickerColumns(3);

        expect($builder->getBlockPickerWidth())->toBe('2xl');
    });

    it('returns `4xl` when max column count is `4`', function (): void {
        $builder = Builder::make('content')
            ->blockPickerColumns(4);

        expect($builder->getBlockPickerWidth())->toBe('4xl');
    });

    it('returns `6xl` when max column count is `5`', function (): void {
        $builder = Builder::make('content')
            ->blockPickerColumns(5);

        expect($builder->getBlockPickerWidth())->toBe('6xl');
    });

    it('returns `7xl` when max column count is `6`', function (): void {
        $builder = Builder::make('content')
            ->blockPickerColumns(6);

        expect($builder->getBlockPickerWidth())->toBe('7xl');
    });

    it('returns `null` for unmapped column counts', function (): void {
        $builder = Builder::make('content')
            ->blockPickerColumns(7);

        expect($builder->getBlockPickerWidth())->toBeNull();
    });

    it('prioritizes explicit `blockPickerWidth()` over computed width', function (): void {
        $builder = Builder::make('content')
            ->blockPickerColumns(6)
            ->blockPickerWidth('sm');

        expect($builder->getBlockPickerWidth())->toBe('sm');
    });
});

describe('`getAddActionAlignment()` logic', function (): void {
    it('converts string to `Alignment` enum', function (): void {
        $builder = Builder::make('content')
            ->addActionAlignment('center');

        expect($builder->getAddActionAlignment())->toBe(Alignment::Center);
    });

    it('passes through non-enum string as-is', function (): void {
        $builder = Builder::make('content')
            ->addActionAlignment('custom-value');

        expect($builder->getAddActionAlignment())->toBe('custom-value');
    });

    it('accepts `Alignment` enum directly', function (): void {
        $builder = Builder::make('content')
            ->addActionAlignment(Alignment::End);

        expect($builder->getAddActionAlignment())->toBe(Alignment::End);
    });

    it('can set `addActionAlignment()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->addActionAlignment(static fn (): string => 'start');

        expect($builder->getAddActionAlignment())->toBe(Alignment::Start);
    });
});

describe('action modifier callbacks', function (): void {
    it('can modify `addAction()` using callback', function (): void {
        $builder = Builder::make('content')
            ->addAction(static fn (Action $action) => $action->label('Custom Add'));

        $action = $builder->getAddAction();

        expect($action->getLabel())->toBe('Custom Add');
    });

    it('can modify `addBetweenAction()` using callback', function (): void {
        $builder = Builder::make('content')
            ->addBetweenAction(static fn (Action $action) => $action->label('Custom Insert'));

        $action = $builder->getAddBetweenAction();

        expect($action->getLabel())->toBe('Custom Insert');
    });

    it('can modify `cloneAction()` using callback', function (): void {
        $builder = Builder::make('content')
            ->cloneAction(static fn (Action $action) => $action->label('Duplicate'));

        $action = $builder->getCloneAction();

        expect($action->getLabel())->toBe('Duplicate');
    });

    it('can modify `deleteAction()` using callback', function (): void {
        $builder = Builder::make('content')
            ->deleteAction(static fn (Action $action) => $action->label('Remove'));

        $action = $builder->getDeleteAction();

        expect($action->getLabel())->toBe('Remove');
    });

    it('can modify `moveDownAction()` using callback', function (): void {
        $builder = Builder::make('content')
            ->moveDownAction(static fn (Action $action) => $action->label('Shift Down'));

        $action = $builder->getMoveDownAction();

        expect($action->getLabel())->toBe('Shift Down');
    });

    it('can modify `moveUpAction()` using callback', function (): void {
        $builder = Builder::make('content')
            ->moveUpAction(static fn (Action $action) => $action->label('Shift Up'));

        $action = $builder->getMoveUpAction();

        expect($action->getLabel())->toBe('Shift Up');
    });

    it('can modify `reorderAction()` using callback', function (): void {
        $builder = Builder::make('content')
            ->reorderAction(static fn (Action $action) => $action->label('Sort'));

        $action = $builder->getReorderAction();

        expect($action->getLabel())->toBe('Sort');
    });

    it('can modify `collapseAction()` using callback', function (): void {
        $builder = Builder::make('content')
            ->collapseAction(static fn (Action $action) => $action->label('Fold'));

        $action = $builder->getCollapseAction();

        expect($action->getLabel())->toBe('Fold');
    });

    it('can modify `expandAction()` using callback', function (): void {
        $builder = Builder::make('content')
            ->expandAction(static fn (Action $action) => $action->label('Unfold'));

        $action = $builder->getExpandAction();

        expect($action->getLabel())->toBe('Unfold');
    });

    it('can modify `collapseAllAction()` using callback', function (): void {
        $builder = Builder::make('content')
            ->collapseAllAction(static fn (Action $action) => $action->label('Fold All'));

        $action = $builder->getCollapseAllAction();

        expect($action->getLabel())->toBe('Fold All');
    });

    it('can modify `expandAllAction()` using callback', function (): void {
        $builder = Builder::make('content')
            ->expandAllAction(static fn (Action $action) => $action->label('Unfold All'));

        $action = $builder->getExpandAllAction();

        expect($action->getLabel())->toBe('Unfold All');
    });

    it('can modify `editAction()` using callback', function (): void {
        $builder = Builder::make('content')
            ->editAction(static fn (Action $action) => $action->label('Modify'));

        $action = $builder->getEditAction();

        expect($action->getLabel())->toBe('Modify');
    });
});

describe('labels with `Closure`', function (): void {
    it('can set `addBetweenActionLabel()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->addBetweenActionLabel(static fn (): string => 'Dynamic Insert');

        expect($builder->getAddBetweenActionLabel())->toBe('Dynamic Insert');
    });

    it('can set `labelBetweenItems()` with a `Closure`', function (): void {
        $builder = Builder::make('content')
            ->labelBetweenItems(static fn (): string => 'then');

        expect($builder->getLabelBetweenItems())->toBe('then');
    });

    it('returns a default translation for `getAddActionLabel()` when not customized', function (): void {
        $builder = Builder::make('content');

        $label = $builder->getAddActionLabel();

        expect($label)->toBeString();
        expect($label)->not->toBeEmpty();
    });

    it('returns a default translation for `getAddBetweenActionLabel()` when not customized', function (): void {
        $builder = Builder::make('content');

        $label = $builder->getAddBetweenActionLabel();

        expect($label)->toBeString();
        expect($label)->not->toBeEmpty();
    });
});

describe('block labels', function (): void {
    it('injects the `$index` of the item into the `Block::label()` `Closure`', function (): void {
        $block = Builder\Block::make('content')
            ->label(static fn (?int $index, ?array $state): string => "Block {$index}: {$state['title']}");

        expect($block->getLabel(['title' => 'first'], 'item-0', 0))->toBe('Block 0: first');
        expect($block->getLabel(['title' => 'second'], 'item-1', 1))->toBe('Block 1: second');
    });
});

describe('`blockPickerColumns()` default behavior', function (): void {
    it('returns default column values when no breakpoint is requested', function (): void {
        $builder = Builder::make('content');

        $columns = $builder->getBlockPickerColumns();

        expect($columns)->toBeArray();
    });

    it('returns `null` for an unset breakpoint', function (): void {
        $builder = Builder::make('content');

        expect($builder->getBlockPickerColumns('2xl'))->toBeNull();
    });

    it('merges responsive breakpoints when called multiple times', function (): void {
        $builder = Builder::make('content')
            ->blockPickerColumns(2)
            ->blockPickerColumns(['sm' => 3]);

        expect($builder->getBlockPickerColumns('lg'))->toBe(2);
        expect($builder->getBlockPickerColumns('sm'))->toBe(3);
    });
});

describe('`getItems()` memoization', function (): void {
    $makeBuilder = function (array $default): Builder {
        $builder = Builder::make('content')
            ->blocks([
                Builder\Block::make('one')
                    ->schema([
                        TextInput::make('foo'),
                    ]),
                Builder\Block::make('two')
                    ->schema([
                        TextInput::make('bar'),
                    ]),
            ])
            ->default($default);

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([$builder])
            ->fill();

        return $builder;
    };

    it('builds one schema per block of differing types', function () use ($makeBuilder): void {
        $builder = $makeBuilder([
            ['type' => 'one', 'data' => ['foo' => 'A']],
            ['type' => 'two', 'data' => ['bar' => 'B']],
            ['type' => 'one', 'data' => ['foo' => 'C']],
        ]);

        $items = $builder->getItems();

        expect($items)->toHaveCount(3)
            ->and(array_keys($items))->toBe(array_keys($builder->getRawState()))
            ->and(array_values($items)[0])->toBeInstanceOf(Schema::class);
    });

    it('memoizes `getItems()` so repeated calls return the same instances', function () use ($makeBuilder): void {
        $builder = $makeBuilder([
            ['type' => 'one', 'data' => ['foo' => 'A']],
            ['type' => 'two', 'data' => ['bar' => 'B']],
        ]);

        expect($builder->getItems())->toBe($builder->getItems());
    });

    it('rebuilds `getItems()` to reflect the new block count after the cache is cleared', function () use ($makeBuilder): void {
        $builder = $makeBuilder([
            ['type' => 'one', 'data' => ['foo' => 'A']],
            ['type' => 'two', 'data' => ['bar' => 'B']],
        ]);

        $firstItems = $builder->getItems();

        expect($firstItems)->toHaveCount(2);

        $builder->state([
            ['type' => 'one', 'data' => ['foo' => 'A']],
            ['type' => 'two', 'data' => ['bar' => 'B']],
            ['type' => 'one', 'data' => ['foo' => 'C']],
        ]);

        // Mirrors the state-update lifecycle's `clearCachedChildSchemas()` call.
        $builder->clearCachedChildSchemas();

        expect($builder->getItems())
            ->toHaveCount(3)
            ->not->toBe($firstItems);
    });
});

class RenderBuilderWithNotAddable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->addable(false),
        ])->statePath('data');
    }
}

class RenderBuilderWithClosureAddable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->addable(static fn (): bool => false),
        ])->statePath('data');
    }
}

class RenderBuilderWithNotDeletable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->deletable(false),
        ])->statePath('data');
    }
}

class RenderBuilderWithClosureDeletable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->deletable(static fn (): bool => false),
        ])->statePath('data');
    }
}

class RenderBuilderWithCollapsible extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->collapsible(),
        ])->statePath('data');
    }
}

class RenderBuilderWithNoBlockLabels extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->blockLabels(false),
        ])->statePath('data');
    }
}

class RenderBuilderWithNoBlockNumbers extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->blockNumbers(false),
        ])->statePath('data');
    }
}

class RenderBuilderWithNoBlockHeaders extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->blockHeaders(false),
        ])->statePath('data');
    }
}

class RenderBuilderWithNoTruncateLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->truncateBlockLabel(false),
        ])->statePath('data');
    }
}

class RenderBuilderWithClosureTruncateLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->truncateBlockLabel(static fn (): bool => false),
        ])->statePath('data');
    }
}

class RenderBuilderWithNoDragAndDrop extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->reorderableWithDragAndDrop(false),
        ])->statePath('data');
    }
}

class RenderBuilderWithReorderButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->reorderableWithButtons(),
        ])->statePath('data');
    }
}

class RenderBuilderWithBlockPickerColumns extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->blockPickerColumns(['default' => 1, 'sm' => 2, 'lg' => 3]),
        ])->statePath('data');
    }
}

class RenderBuilderWithClosureBlockPickerWidth extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->blockPickerWidth(static fn (): string => 'lg'),
        ])->statePath('data');
    }
}

class RenderBuilderWithLabelBetweenItems extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->labelBetweenItems('or'),
        ])->statePath('data');
    }
}

class RenderBuilderWithClosureAddActionLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->addActionLabel(static fn (): string => 'Add custom block'),
        ])->statePath('data');
    }
}

class RenderBuilderWithCloneable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->cloneable(),
        ])->statePath('data');
    }
}

class RenderBuilderWithAddActionAlignment extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->addActionAlignment('center'),
        ])->statePath('data');
    }
}

class RenderBuilderWithNotReorderable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->reorderable(false),
        ])->statePath('data');
    }
}

class RenderBuilderWithClosureReorderable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')->blocks([Builder\Block::make('one')->schema([TextInput::make('foo')])])->reorderable(static fn (): bool => false),
        ])->statePath('data');
    }
}

class TestComponentWithBuilderFilledFromMount extends Livewire
{
    public mixed $initialData = null;

    public function mount(): void
    {
        $this->form->fill(['builder' => $this->initialData]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Builder::make('builder')
                    ->blocks([
                        Builder\Block::make('one')
                            ->schema([
                                TextInput::make('foo'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }
}

it('rebuilds blocks after an `afterStateUpdated` hook uses `$set()` on an ancestor\'s state path', function (): void {
    livewire(BuilderInStatePathAncestorSetByHook::class)
        ->assertSeeText('First block type')
        ->assertDontSeeText('Second block type')
        ->set('data.trigger', 'anything')
        ->assertSeeText('Second block type');
});

class BuilderInStatePathAncestorSetByHook extends Livewire
{
    public function mount(): void
    {
        $this->form->fill([
            'trigger' => null,
            'group' => [
                'blocks' => [
                    ['type' => 'one', 'data' => ['foo' => 'A']],
                ],
            ],
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                // The `Section` is deliberately registered before the `trigger` field, so that
                // the `afterStateUpdated` walk traverses it, and the `Builder` caches its
                // items, before the `trigger` field's hook runs `$set()`.
                Section::make('Blocks')
                    ->statePath('group')
                    ->schema([
                        Builder::make('blocks')
                            ->addable(false) // Without the add action, its block picker does not render every block type's label, so the assertions below can rely on the rendered block headers alone.
                            ->blocks([
                                Builder\Block::make('one')
                                    ->label('First block type')
                                    ->schema([
                                        TextInput::make('foo'),
                                    ]),
                                Builder\Block::make('two')
                                    ->label('Second block type')
                                    ->schema([
                                        TextInput::make('bar'),
                                    ]),
                            ]),
                    ]),
                TextInput::make('trigger')
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('group', [
                            'blocks' => [
                                ['type' => 'one', 'data' => ['foo' => 'A']],
                                ['type' => 'two', 'data' => ['bar' => 'B']],
                            ],
                        ]);
                    }),
            ])
            ->statePath('data');
    }
}

class RenderBuilderWithSearchableBlocks extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Builder::make('content')
                ->searchable()
                ->searchPrompt(new HtmlString('<span title="Search">Find &quot;R&amp;D&quot;</span>'))
                ->noSearchResultsMessage(new HtmlString('<strong>No matching blocks</strong>'))
                ->blocks([
                    Builder\Block::make('paragraph')
                        ->label('Paragraph')
                        ->schema([TextInput::make('text')]),
                    Builder\Block::make('heading')
                        ->label(new HtmlString('Editor&apos;s picks'))
                        ->schema([TextInput::make('title')]),
                    Builder\Block::make('video')
                        ->label('Video')
                        ->maxItems(1)
                        ->schema([TextInput::make('url')]),
                ]),
        ])->statePath('data');
    }
}
