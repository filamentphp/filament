<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\View\FormsIconAlias;
use Filament\Schemas\Components\Concerns\CanBeCollapsed;
use Filament\Schemas\Components\Contracts\CanConcealComponents;
use Filament\Schemas\Components\Contracts\HasExtraItemActions;
use Filament\Schemas\Schema;
use Filament\Support\Concerns\HasReorderAnimationDuration;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use function Filament\Forms\array_move_after;
use function Filament\Forms\array_move_before;

class Builder extends Field implements CanConcealComponents, HasExtraItemActions
{
    use CanBeCollapsed;
    use Concerns\CanBeCloned;
    use Concerns\CanGenerateUuids;
    use Concerns\CanLimitItemsLength;
    use Concerns\HasExtraItemActions;
    use HasReorderAnimationDuration;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.builder';

    protected string | Closure | null $addBetweenActionLabel = null;

    protected string | Closure | null $addActionLabel = null;

    protected bool | Closure $isReorderable = true;

    protected bool | Closure $isReorderableWithDragAndDrop = true;

    protected bool | Closure $isReorderableWithButtons = false;

    protected bool | Closure $isAddable = true;

    protected bool | Closure $isDeletable = true;

    protected bool | Closure $hasBlockLabels = true;

    protected bool | Closure $hasBlockNumbers = true;

    protected bool | Closure $hasBlockIcons = false;

    protected bool | Closure $hasBlockPreviews = false;

    protected bool | Closure $hasInteractiveBlockPreviews = false;

    protected Alignment | string | Closure | null $addActionAlignment = null;

    protected ?Closure $modifyAddActionUsing = null;

    protected ?Closure $modifyAddBetweenActionUsing = null;

    protected ?Closure $modifyCloneActionUsing = null;

    protected ?Closure $modifyDeleteActionUsing = null;

    protected ?Closure $modifyMoveDownActionUsing = null;

    protected ?Closure $modifyMoveUpActionUsing = null;

    protected ?Closure $modifyReorderActionUsing = null;

    protected ?Closure $modifyCollapseActionUsing = null;

    protected ?Closure $modifyExpandActionUsing = null;

    protected ?Closure $modifyCollapseAllActionUsing = null;

    protected ?Closure $modifyExpandAllActionUsing = null;

    protected ?Closure $modifyEditActionUsing = null;

    protected string | Closure | null $labelBetweenItems = null;

    protected bool | Closure $isBlockLabelTruncated = true;

    /**
     * @var array<string, ?int> | null
     */
    protected ?array $blockPickerColumns = [];

    protected Width | string | Closure | null $blockPickerWidth = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->afterStateHydrated(static function (Builder $component, ?array $rawState): void {
            $items = [];

            foreach ($rawState ?? [] as $itemData) {
                if ($uuid = $component->generateUuid()) {
                    $items[$uuid] = $itemData;
                } else {
                    $items[] = $itemData;
                }
            }

            $component->rawState($items);
        });

        $this->registerActions([
            fn (Builder $component): Action => $component->getAddAction(),
            fn (Builder $component): Action => $component->getAddBetweenAction(),
            fn (Builder $component): Action => $component->getCloneAction(),
            fn (Builder $component): Action => $component->getCollapseAction(),
            fn (Builder $component): Action => $component->getCollapseAllAction(),
            fn (Builder $component): Action => $component->getDeleteAction(),
            fn (Builder $component): Action => $component->getEditAction(),
            fn (Builder $component): Action => $component->getExpandAction(),
            fn (Builder $component): Action => $component->getExpandAllAction(),
            fn (Builder $component): Action => $component->getMoveDownAction(),
            fn (Builder $component): Action => $component->getMoveUpAction(),
            fn (Builder $component): Action => $component->getReorderAction(),
        ]);

        $this->mutateDehydratedStateUsing(static function (?array $state): array {
            return array_values($state ?? []);
        });
    }

    /**
     * @param  array<Block> | Closure  $blocks
     */
    public function blocks(array | Closure $blocks): static
    {
        $this->components($blocks);

        return $this;
    }

    public function getAddAction(): Action
    {
        $action = Action::make($this->getAddActionName())
            ->label(fn (Builder $component) => $component->getAddActionLabel())
            ->color('gray')
            ->action(function (array $arguments, Builder $component, array $data = []): void {
                $newUuid = $component->generateUuid();

                $items = $component->getRawState();

                if ($newUuid) {
                    $items[$newUuid] = [
                        'type' => $arguments['block'],
                        'data' => $data,
                    ];
                } else {
                    $items[] = [
                        'type' => $arguments['block'],
                        'data' => $data,
                    ];
                }

                $component->rawState($items);

                $component->getChildSchema($newUuid ?? array_key_last($items))->fill(filled($data) ? $data : null);

                $component->collapsed(false, shouldMakeComponentCollapsible: false);

                $component->callAfterStateUpdated();
            })
            ->livewireClickHandlerEnabled(false)
            ->button()
            ->size(Size::Small)
            ->visible(fn (Builder $component): bool => $component->isAddable());

        if ($this->hasBlockPreviews()) {
            $action
                ->modalHeading(fn (Builder $component) => __('filament-forms::components.builder.actions.add.modal.heading', [
                    'label' => $component->getLabel(),
                ]))
                ->modalSubmitActionLabel(__('filament-forms::components.builder.actions.add.modal.actions.add.label'))
                ->schema(function (array $arguments, Builder $component): array {
                    return $component->getBlock($arguments['block'])->getDefaultChildComponents();
                });
        }

        if ($this->modifyAddActionUsing) {
            $action = $this->evaluate($this->modifyAddActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function addActionAlignment(Alignment | string | Closure | null $addActionAlignment): static
    {
        $this->addActionAlignment = $addActionAlignment;

        return $this;
    }

    public function getAddActionAlignment(): Alignment | string | null
    {
        $alignment = $this->evaluate($this->addActionAlignment);

        if (is_string($alignment)) {
            $alignment = Alignment::tryFrom($alignment) ?? $alignment;
        }

        return $alignment;
    }

    public function addAction(?Closure $callback): static
    {
        $this->modifyAddActionUsing = $callback;

        return $this;
    }

    public function getAddActionName(): string
    {
        return 'add';
    }

    public function getAddBetweenAction(): Action
    {
        $action = Action::make($this->getAddBetweenActionName())
            ->label(fn (Builder $component) => $component->getAddBetweenActionLabel())
            ->color('gray')
            ->action(function (array $arguments, Builder $component, array $data = []): void {
                $newKey = $component->generateUuid();

                $items = [];

                foreach ($component->getRawState() ?? [] as $key => $item) {
                    $items[$key] = $item;

                    if ($key === $arguments['afterItem']) {
                        if ($newKey) {
                            $items[$newKey] = [
                                'type' => $arguments['block'],
                                'data' => $data,
                            ];
                        } else {
                            $items[] = [
                                'type' => $arguments['block'],
                                'data' => $data,
                            ];

                            $newKey = array_key_last($items);
                        }
                    }
                }

                $component->rawState($items);

                $component->getChildSchema($newKey)->fill(filled($data) ? $data : null);

                $component->collapsed(false, shouldMakeComponentCollapsible: false);

                $component->callAfterStateUpdated();

                $component->partiallyRender();
            })
            ->livewireClickHandlerEnabled(false)
            ->button()
            ->size(Size::Small)
            ->visible(fn (Builder $component): bool => $component->isAddable());

        if ($this->hasBlockPreviews()) {
            $action
                ->modalHeading(fn (Builder $component) => __('filament-forms::components.builder.actions.add_between.modal.heading', [
                    'label' => $component->getLabel(),
                ]))
                ->modalSubmitActionLabel(__('filament-forms::components.builder.actions.add_between.modal.actions.add.label'))
                ->schema(function (array $arguments, Builder $component): array {
                    return $component->getBlock($arguments['block'])->getDefaultChildComponents();
                });
        }

        if ($this->modifyAddBetweenActionUsing) {
            $action = $this->evaluate($this->modifyAddBetweenActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function addBetweenAction(?Closure $callback): static
    {
        $this->modifyAddBetweenActionUsing = $callback;

        return $this;
    }

    public function getAddBetweenActionName(): string
    {
        return 'addBetween';
    }

    public function getCloneAction(): Action
    {
        $action = Action::make($this->getCloneActionName())
            ->label(__('filament-forms::components.builder.actions.clone.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_CLONE) ?? Heroicon::Square2Stack)
            ->color('gray')
            ->action(function (array $arguments, Builder $component): void {
                $newUuid = $component->generateUuid();

                $items = $component->getRawState();

                if ($newUuid) {
                    $items[$newUuid] = $items[$arguments['item']];
                } else {
                    $items[] = $items[$arguments['item']];
                }

                $component->rawState($items);

                $component->collapsed(false, shouldMakeComponentCollapsible: false);

                $component->callAfterStateUpdated();

                $component->partiallyRender();
            })
            ->iconButton()
            ->size(Size::Small)
            ->visible(fn (Builder $component): bool => $component->isCloneable());

        if ($this->modifyCloneActionUsing) {
            $action = $this->evaluate($this->modifyCloneActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function cloneAction(?Closure $callback): static
    {
        $this->modifyCloneActionUsing = $callback;

        return $this;
    }

    public function getCloneActionName(): string
    {
        return 'clone';
    }

    public function getDeleteAction(): Action
    {
        $action = Action::make($this->getDeleteActionName())
            ->label(__('filament-forms::components.builder.actions.delete.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_DELETE) ?? Heroicon::Trash)
            ->color('danger')
            ->action(function (array $arguments, Builder $component): void {
                $items = $component->getRawState();
                unset($items[$arguments['item']]);

                $component->rawState($items);

                $component->callAfterStateUpdated();

                $component->partiallyRender();
            })
            ->iconButton()
            ->size(Size::Small)
            ->visible(fn (Builder $component): bool => $component->isDeletable());

        if ($this->modifyDeleteActionUsing) {
            $action = $this->evaluate($this->modifyDeleteActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function deleteAction(?Closure $callback): static
    {
        $this->modifyDeleteActionUsing = $callback;

        return $this;
    }

    public function getDeleteActionName(): string
    {
        return 'delete';
    }

    public function getMoveDownAction(): Action
    {
        $action = Action::make($this->getMoveDownActionName())
            ->label(__('filament-forms::components.builder.actions.move_down.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_MOVE_DOWN) ?? Heroicon::ArrowDown)
            ->color('gray')
            ->action(function (array $arguments, Builder $component): void {
                $items = array_move_after($component->getRawState(), $arguments['item']);

                $component->rawState($items);

                $component->callAfterStateUpdated();

                $component->partiallyRender();
            })
            ->iconButton()
            ->size(Size::Small)
            ->visible(fn (Builder $component): bool => $component->isReorderable());

        if ($this->modifyMoveDownActionUsing) {
            $action = $this->evaluate($this->modifyMoveDownActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function moveDownAction(?Closure $callback): static
    {
        $this->modifyMoveDownActionUsing = $callback;

        return $this;
    }

    public function getMoveDownActionName(): string
    {
        return 'moveDown';
    }

    public function getMoveUpAction(): Action
    {
        $action = Action::make($this->getMoveUpActionName())
            ->label(__('filament-forms::components.builder.actions.move_up.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_MOVE_UP) ?? Heroicon::ArrowUp)
            ->color('gray')
            ->action(function (array $arguments, Builder $component): void {
                $items = array_move_before($component->getRawState(), $arguments['item']);

                $component->rawState($items);

                $component->callAfterStateUpdated();

                $component->partiallyRender();
            })
            ->iconButton()
            ->size(Size::Small)
            ->visible(fn (Builder $component): bool => $component->isReorderable());

        if ($this->modifyMoveUpActionUsing) {
            $action = $this->evaluate($this->modifyMoveUpActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function moveUpAction(?Closure $callback): static
    {
        $this->modifyMoveUpActionUsing = $callback;

        return $this;
    }

    public function labelBetweenItems(string | Closure | null $label): static
    {
        $this->labelBetweenItems = $label;

        return $this;
    }

    public function getMoveUpActionName(): string
    {
        return 'moveUp';
    }

    public function getReorderAction(): Action
    {
        $action = Action::make($this->getReorderActionName())
            ->label(__('filament-forms::components.builder.actions.reorder.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_REORDER) ?? Heroicon::ArrowsUpDown)
            ->color('gray')
            ->action(function (array $arguments, Builder $component): void {
                $items = [
                    ...array_flip($arguments['items']),
                    ...$component->getRawState(),
                ];

                $component->rawState($items);

                $component->callAfterStateUpdated();

                $component->partiallyRender();
            })
            ->livewireClickHandlerEnabled(false)
            ->iconButton()
            ->size(Size::Small)
            ->visible(fn (Builder $component): bool => $component->isReorderableWithDragAndDrop());

        if ($this->modifyReorderActionUsing) {
            $action = $this->evaluate($this->modifyReorderActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function reorderAction(?Closure $callback): static
    {
        $this->modifyReorderActionUsing = $callback;

        return $this;
    }

    public function getReorderActionName(): string
    {
        return 'reorder';
    }

    public function getCollapseAction(): Action
    {
        $action = Action::make($this->getCollapseActionName())
            ->label(__('filament-forms::components.builder.actions.collapse.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_COLLAPSE) ?? Heroicon::ChevronUp)
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->iconButton()
            ->size(Size::Small);

        if ($this->modifyCollapseActionUsing) {
            $action = $this->evaluate($this->modifyCollapseActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function collapseAction(?Closure $callback): static
    {
        $this->modifyCollapseActionUsing = $callback;

        return $this;
    }

    public function getCollapseActionName(): string
    {
        return 'collapse';
    }

    public function getExpandAction(): Action
    {
        $action = Action::make($this->getExpandActionName())
            ->label(__('filament-forms::components.builder.actions.expand.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_EXPAND) ?? Heroicon::ChevronDown)
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->iconButton()
            ->size(Size::Small);

        if ($this->modifyExpandActionUsing) {
            $action = $this->evaluate($this->modifyExpandActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function expandAction(?Closure $callback): static
    {
        $this->modifyExpandActionUsing = $callback;

        return $this;
    }

    public function getExpandActionName(): string
    {
        return 'expand';
    }

    public function getCollapseAllAction(): Action
    {
        $action = Action::make($this->getCollapseAllActionName())
            ->label(__('filament-forms::components.builder.actions.collapse_all.label'))
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->link()
            ->size(Size::Small);

        if ($this->modifyCollapseAllActionUsing) {
            $action = $this->evaluate($this->modifyCollapseAllActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function collapseAllAction(?Closure $callback): static
    {
        $this->modifyCollapseAllActionUsing = $callback;

        return $this;
    }

    public function getCollapseAllActionName(): string
    {
        return 'collapseAll';
    }

    public function getExpandAllAction(): Action
    {
        $action = Action::make($this->getExpandAllActionName())
            ->label(__('filament-forms::components.builder.actions.expand_all.label'))
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->link()
            ->size(Size::Small);

        if ($this->modifyExpandAllActionUsing) {
            $action = $this->evaluate($this->modifyExpandAllActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function expandAllAction(?Closure $callback): static
    {
        $this->modifyExpandAllActionUsing = $callback;

        return $this;
    }

    public function getExpandAllActionName(): string
    {
        return 'expandAll';
    }

    public function getEditAction(): Action
    {
        $action = Action::make($this->getEditActionName())
            ->label(__('filament-forms::components.builder.actions.edit.label'))
            ->modalHeading(__('filament-forms::components.builder.actions.edit.modal.heading'))
            ->modalSubmitActionLabel(__('filament-forms::components.builder.actions.edit.modal.actions.save.label'))
            ->color('gray')
            ->fillForm(function (array $arguments, Builder $component) {
                $state = $component->getState();

                return $state[$arguments['item']]['data'];
            })
            ->schema(function (array $arguments, Builder $component) {
                return $component->getChildSchema($arguments['item'])
                    ->getComponents(withHidden: true);
            })
            ->action(function (array $arguments, Builder $component, $data): void {
                $state = $component->getRawState();

                $state[$arguments['item']]['data'] = $data;

                $component->rawState($state);

                $component->getChildSchema($arguments['item'])->fill($data);

                $component->callAfterStateUpdated();

                $component->partiallyRender();
            })
            ->iconButton()
            ->icon(Heroicon::Cog6Tooth)
            ->size(Size::Small)
            ->visible(fn (Builder $component): bool => (! $component->isDisabled()) && $component->hasBlockPreviews());

        if ($this->modifyEditActionUsing) {
            $action = $this->evaluate($this->modifyEditActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function editAction(?Closure $callback): static
    {
        $this->modifyEditActionUsing = $callback;

        return $this;
    }

    public function getEditActionName(): string
    {
        return 'edit';
    }

    public function truncateBlockLabel(bool | Closure $condition = true): static
    {
        $this->isBlockLabelTruncated = $condition;

        return $this;
    }

    public function addBetweenActionLabel(string | Closure | null $label): static
    {
        $this->addBetweenActionLabel = $label;

        return $this;
    }

    /**
     * @deprecated Use `addBetweenActionLabel()` instead.
     */
    public function createItemBetweenButtonLabel(string | Closure | null $label): static
    {
        $this->addBetweenActionLabel($label);

        return $this;
    }

    public function addActionLabel(string | Closure | null $label): static
    {
        $this->addActionLabel = $label;

        return $this;
    }

    /**
     * @deprecated Use `addActionLabel()` instead.
     */
    public function createItemButtonLabel(string | Closure | null $label): static
    {
        $this->addActionLabel($label);

        return $this;
    }

    public function addable(bool | Closure $condition = true): static
    {
        $this->isAddable = $condition;

        return $this;
    }

    public function deletable(bool | Closure $condition = true): static
    {
        $this->isDeletable = $condition;

        return $this;
    }

    public function reorderable(bool | Closure $condition = true): static
    {
        $this->isReorderable = $condition;

        return $this;
    }

    /**
     * @deprecated Use `addable()` instead.
     */
    public function disableItemCreation(bool | Closure $condition = true): static
    {
        $this->addable(fn (Builder $component): bool => ! $this->evaluate($condition));

        return $this;
    }

    /**
     * @deprecated Use `deletable()` instead.
     */
    public function disableItemDeletion(bool | Closure $condition = true): static
    {
        $this->deletable(fn (Builder $component): bool => ! $this->evaluate($condition));

        return $this;
    }

    /**
     * @deprecated Use `reorderable()` instead.
     */
    public function disableItemMovement(bool | Closure $condition = true): static
    {
        $this->reorderable(fn (Builder $component): bool => ! $this->evaluate($condition));

        return $this;
    }

    /**
     * @deprecated No longer part of the design system.
     */
    public function inset(bool | Closure $condition = true): static
    {
        return $this;
    }

    public function reorderableWithDragAndDrop(bool | Closure $condition = true): static
    {
        $this->isReorderableWithDragAndDrop = $condition;

        return $this;
    }

    public function reorderableWithButtons(bool | Closure $condition = true): static
    {
        $this->isReorderableWithButtons = $condition;

        return $this;
    }

    /**
     * @deprecated Use `blockLabels()` instead.
     */
    public function showBlockLabels(bool | Closure $condition = true): static
    {
        $this->withBlockLabels($condition);

        return $this;
    }

    /**
     * @deprecated Use `blockLabels()` instead.
     */
    public function withBlockLabels(bool | Closure $condition = true): static
    {
        $this->blockLabels($condition);

        return $this;
    }

    /**
     * @deprecated Use `blockNumbers()` instead.
     */
    public function withBlockNumbers(bool | Closure $condition = true): static
    {
        $this->blockNumbers($condition);

        return $this;
    }

    public function blockLabels(bool | Closure $condition = true): static
    {
        $this->hasBlockLabels = $condition;

        return $this;
    }

    public function blockNumbers(bool | Closure $condition = true): static
    {
        $this->hasBlockNumbers = $condition;

        return $this;
    }

    public function blockIcons(bool | Closure $condition = true): static
    {
        $this->hasBlockIcons = $condition;

        return $this;
    }

    public function blockPreviews(bool | Closure $condition = true, bool | Closure $areInteractive = false): static
    {
        $this->hasBlockPreviews = $condition;
        $this->hasInteractiveBlockPreviews = $areInteractive;

        return $this;
    }

    public function getBlock(string $name): ?Block
    {
        return Arr::first(
            $this->getBlocks(),
            fn (Block $block): bool => $block->getName() === $name,
        );
    }

    /**
     * @return array<Block>
     */
    public function getBlocks(): array
    {
        /** @var array<Block> $blocks */
        $blocks = $this->getChildSchema()->getComponents();

        return $blocks;
    }

    /**
     * @return array<Schema>
     */
    public function getItems(): array
    {
        return collect($this->getRawState())
            ->filter(fn (array $itemData): bool => filled($itemData['type'] ?? null) && $this->hasBlock($itemData['type']))
            ->map(
                fn (array $itemData, $itemIndex): Schema => $this
                    ->getBlock($itemData['type'])
                    ->getChildSchema()
                    ->statePath("{$itemIndex}.data")
                    ->constantState($itemData['data'] ?? [])
                    ->inlineLabel(false)
                    ->getClone(),
            )
            ->all();
    }

    /**
     * @return array<Schema>
     */
    public function getDefaultChildSchemas(): array
    {
        return $this->getItems();
    }

    public function getAddBetweenActionLabel(): string
    {
        return $this->evaluate($this->addBetweenActionLabel) ?? __('filament-forms::components.builder.actions.add_between.label');
    }

    public function getAddActionLabel(): string
    {
        return $this->evaluate($this->addActionLabel) ?? __('filament-forms::components.builder.actions.add.label', [
            'label' => Str::lcfirst($this->getLabel()),
        ]);
    }

    public function hasBlock(string $name): bool
    {
        return (bool) $this->getBlock($name);
    }

    public function isReorderable(): bool
    {
        if ($this->isDisabled()) {
            return false;
        }

        return (bool) $this->evaluate($this->isReorderable);
    }

    public function isReorderableWithDragAndDrop(): bool
    {
        return $this->evaluate($this->isReorderableWithDragAndDrop) && $this->isReorderable();
    }

    public function isReorderableWithButtons(): bool
    {
        return $this->evaluate($this->isReorderableWithButtons) && $this->isReorderable();
    }

    public function isAddable(): bool
    {
        if ($this->isDisabled()) {
            return false;
        }

        if (filled($this->getMaxItems()) && ($this->getMaxItems() <= $this->getItemsCount())) {
            return false;
        }

        return (bool) $this->evaluate($this->isAddable);
    }

    public function isDeletable(): bool
    {
        if ($this->isDisabled()) {
            return false;
        }

        return (bool) $this->evaluate($this->isDeletable);
    }

    public function hasBlockLabels(): bool
    {
        return (bool) $this->evaluate($this->hasBlockLabels);
    }

    public function hasBlockNumbers(): bool
    {
        return (bool) $this->evaluate($this->hasBlockNumbers);
    }

    public function hasBlockIcons(): bool
    {
        return (bool) $this->evaluate($this->hasBlockIcons);
    }

    public function hasBlockPreviews(): bool
    {
        return (bool) $this->evaluate($this->hasBlockPreviews);
    }

    public function hasInteractiveBlockPreviews(): bool
    {
        return (bool) $this->evaluate($this->hasInteractiveBlockPreviews);
    }

    public function canConcealComponents(): bool
    {
        return $this->isCollapsible();
    }

    public function getLabelBetweenItems(): ?string
    {
        return $this->evaluate($this->labelBetweenItems);
    }

    public function isBlockLabelTruncated(): bool
    {
        return (bool) $this->evaluate($this->isBlockLabelTruncated);
    }

    /**
     * @return array<Block>
     */
    public function getBlockPickerBlocks(): array
    {
        $state = $this->getRawState();

        /** @var array<Block> $blocks */
        $blocks = array_filter($this->getBlocks(), function (Block $block) use ($state): bool {
            /** @var Block $block */
            $maxItems = $block->getMaxItems();

            if ($maxItems === null) {
                return true;
            }

            $count = count(array_filter($state, function (array $item) use ($block): bool {
                return $item['type'] === $block->getName();
            }));

            return $count < $maxItems;
        });

        return $blocks;
    }

    /**
     * @param  array<string, ?int> | int | null  $columns
     */
    public function blockPickerColumns(array | int | null $columns = 2): static
    {
        if (! is_array($columns)) {
            $columns = [
                'lg' => $columns,
            ];
        }

        $this->blockPickerColumns = [
            ...($this->blockPickerColumns ?? []),
            ...$columns,
        ];

        return $this;
    }

    /**
     * @return array<string, ?int> | int | null
     */
    public function getBlockPickerColumns(?string $breakpoint = null): array | int | null
    {
        $columns = $this->blockPickerColumns ?? [
            'default' => 1,
            'sm' => null,
            'md' => null,
            'lg' => null,
            'xl' => null,
            '2xl' => null,
        ];

        if ($breakpoint !== null) {
            return $columns[$breakpoint] ?? null;
        }

        return $columns;
    }

    public function blockPickerWidth(Width | string | Closure | null $width): static
    {
        $this->blockPickerWidth = $width;

        return $this;
    }

    public function getBlockPickerWidth(): Width | string | null
    {
        $width = $this->evaluate($this->blockPickerWidth);

        if (filled($width)) {
            return $width;
        }

        $columns = $this->getBlockPickerColumns();

        if (empty($columns)) {
            return null;
        }

        return match (max($columns)) {
            2 => 'md',
            3 => '2xl',
            4 => '4xl',
            5 => '6xl',
            6 => '7xl',
            default => null,
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function getItemState(string $key): array
    {
        return $this->getChildSchema($key)->getState(shouldCallHooksBefore: false);
    }

    /**
     * @return array<string, mixed>
     */
    public function getRawItemState(string $key): array
    {
        return $this->getChildSchema($key)->getRawState();
    }

    public function getHeadingsCount(): int
    {
        if (! $this->hasBlockLabels()) {
            return 0;
        }

        return 1;
    }

    /**
     * @param  array<string, array<mixed>>  $rules
     */
    public function dehydrateValidationRules(array &$rules): void
    {
        parent::dehydrateValidationRules($rules);

        $rules["{$this->getStatePath()}.*.type"] = ['required'];
    }
}
