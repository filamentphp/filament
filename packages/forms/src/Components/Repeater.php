<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\View\FormsIconAlias;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\CanBeCollapsed;
use Filament\Schemas\Components\Concerns\CanBeCompact;
use Filament\Schemas\Components\Concerns\HasContainerGridLayout;
use Filament\Schemas\Components\Contracts\CanConcealComponents;
use Filament\Schemas\Components\Contracts\HasExtraItemActions;
use Filament\Schemas\Schema;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasReorderAnimationDuration;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use LogicException;

use function Filament\Forms\array_move_after;
use function Filament\Forms\array_move_before;

class Repeater extends Field implements CanConcealComponents, HasEmbeddedView, HasExtraItemActions
{
    use CanBeCollapsed;
    use CanBeCompact;
    use Concerns\CanBeCloned;
    use Concerns\CanGenerateUuids;
    use Concerns\CanLimitItemsLength;
    use Concerns\HasExtraItemActions;
    use HasContainerGridLayout;
    use HasReorderAnimationDuration;

    protected string | Closure | null $addActionLabel = null;

    protected string | Closure | null $addBetweenActionLabel = null;

    protected bool | Closure $isAddable = true;

    protected bool | Closure $isDeletable = true;

    protected bool | Closure $isReorderable = true;

    protected bool | Closure $isReorderableWithDragAndDrop = true;

    protected bool | Closure $isReorderableWithButtons = false;

    protected ?Collection $cachedExistingRecords = null;

    /**
     * @var array<Schema> | null
     */
    protected ?array $cachedItems = null;

    protected string | Closure | null $orderColumn = null;

    protected string | Closure | null $relationship = null;

    protected string | Htmlable | Closure | null $itemLabel = null;

    protected bool | Closure $hasItemNumbers = false;

    protected bool | Closure $hasItemHeaders = true;

    protected Field | Closure | null $simpleField = null;

    protected Alignment | string | Closure | null $addActionAlignment = null;

    protected ?Closure $modifyRelationshipQueryUsing = null;

    protected ?Closure $modifyRelationshipRecordsUsing = null;

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

    protected ?Closure $mutateRelationshipDataBeforeCreateUsing = null;

    protected ?Closure $mutateRelationshipDataBeforeFillUsing = null;

    protected ?Closure $mutateRelationshipDataBeforeSaveUsing = null;

    protected ?Closure $afterCreate = null;

    protected ?Closure $afterUpdate = null;

    protected ?Closure $afterDelete = null;

    /**
     * @var array<string, mixed> | null
     */
    protected ?array $hydratedDefaultState = null;

    protected string | Closure | null $labelBetweenItems = null;

    protected bool | Closure $isItemLabelTruncated = true;

    protected ?Field $cachedSimpleField = null;

    /**
     * @var array<TableColumn> | Closure | null
     */
    protected array | Closure | null $tableColumns = null;

    protected bool $shouldMergeHydratedDefaultStateWithItemsStateAfterStateHydrated = true;

    protected bool | Closure | null $shouldPartiallyRenderAfterActionsCalled = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultItems(1);

        $this->afterStateHydrated(static function (Repeater $component): void {
            $component->hydrateItems();
        });

        $this->registerActions([
            fn (Repeater $component): Action => $component->getAddAction(),
            fn (Repeater $component): Action => $component->getAddBetweenAction(),
            fn (Repeater $component): Action => $component->getCloneAction(),
            fn (Repeater $component): Action => $component->getCollapseAction(),
            fn (Repeater $component): Action => $component->getCollapseAllAction(),
            fn (Repeater $component): Action => $component->getDeleteAction(),
            fn (Repeater $component): Action => $component->getExpandAction(),
            fn (Repeater $component): Action => $component->getExpandAllAction(),
            fn (Repeater $component): Action => $component->getMoveDownAction(),
            fn (Repeater $component): Action => $component->getMoveUpAction(),
            fn (Repeater $component): Action => $component->getReorderAction(),
        ]);

        $this->mutateDehydratedStateUsing(static function (Repeater $component, ?array $state): array {
            return $component->dehydrateItems($state);
        });
    }

    public function hydrateItems(): void
    {
        if (
            is_array($this->hydratedDefaultState) &&
            $this->shouldMergeHydratedDefaultStateWithItemsStateAfterStateHydrated
        ) {
            $this->mergeHydratedDefaultStateWithItemsState();
        }

        if (is_array($this->hydratedDefaultState)) {
            return;
        }

        $items = [];

        $simpleField = $this->getSimpleField();

        foreach ($this->getRawState() ?? [] as $itemData) {
            if ($simpleField) {
                $itemData = [$simpleField->getName() => $itemData];
            }

            if ($uuid = $this->generateUuid()) {
                $items[$uuid] = $itemData;
            } else {
                $items[] = $itemData;
            }
        }

        $this->rawState($items);
    }

    /**
     * @param  array<string, array<string, mixed>> | null  $state
     * @return array<int, array<string, mixed>>
     */
    public function dehydrateItems(?array $state): array
    {
        if ($simpleField = $this->getSimpleField()) {
            return collect($state ?? [])
                ->values()
                ->pluck($simpleField->getName())
                ->all();
        }

        return array_values($state ?? []);
    }

    public function getAddAction(): Action
    {
        $action = Action::make($this->getAddActionName())
            ->label(fn (Repeater $component) => $component->getAddActionLabel())
            ->color('gray')
            ->action(function (Repeater $component): void {
                $newUuid = $component->generateUuid();

                $items = $component->getRawState();

                if ($newUuid) {
                    $items[$newUuid] = [];
                } else {
                    $items[] = [];
                }

                $component->rawState($items);

                $component->getChildSchema($newUuid ?? array_key_last($items))->fill();

                $component->collapsed(false, shouldMakeComponentCollapsible: false);

                $component->callAfterStateUpdated();

                $component->shouldPartiallyRenderAfterActionsCalled() ? $component->partiallyRender() : null;
            })
            ->button()
            ->size(Size::Small)
            ->visible(fn (Repeater $component): bool => $component->isAddable());

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
            ->label(fn (Repeater $component) => $component->getAddBetweenActionLabel())
            ->color('gray')
            ->action(function (array $arguments, Repeater $component): void {
                $newKey = $component->generateUuid();

                $items = [];

                foreach ($component->getRawState() ?? [] as $key => $item) {
                    $items[$key] = $item;

                    if ($key === $arguments['afterItem']) {
                        if ($newKey) {
                            $items[$newKey] = [];
                        } else {
                            $items[] = [];

                            $newKey = array_key_last($items);
                        }
                    }
                }

                $component->rawState($items);

                $component->getChildSchema($newKey)->fill();

                $component->collapsed(false, shouldMakeComponentCollapsible: false);

                $component->callAfterStateUpdated();

                $component->shouldPartiallyRenderAfterActionsCalled() ? $component->partiallyRender() : null;
            })
            ->button()
            ->size(Size::Small)
            ->visible(false);

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

    public function addBetweenActionLabel(string | Closure | null $label): static
    {
        $this->addBetweenActionLabel = $label;

        return $this;
    }

    public function getAddBetweenActionLabel(): string
    {
        return $this->evaluate($this->addBetweenActionLabel) ?? __('filament-forms::components.repeater.actions.add_between.label');
    }

    public function getCloneAction(): Action
    {
        $action = Action::make($this->getCloneActionName())
            ->label(__('filament-forms::components.repeater.actions.clone.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_CLONE) ?? Heroicon::Square2Stack)
            ->color('gray')
            ->action(function (array $arguments, Repeater $component): void {
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

                $component->shouldPartiallyRenderAfterActionsCalled() ? $component->partiallyRender() : null;
            })
            ->iconButton()
            ->size(Size::Small)
            ->visible(fn (Repeater $component): bool => $component->isCloneable());

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
            ->label(__('filament-forms::components.repeater.actions.delete.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_DELETE) ?? Heroicon::Trash)
            ->color('danger')
            ->action(function (array $arguments, Repeater $component): void {
                $items = $component->getRawState();
                unset($items[$arguments['item']]);

                $component->rawState($items);

                $component->callAfterStateUpdated();

                $component->shouldPartiallyRenderAfterActionsCalled() ? $component->partiallyRender() : null;
            })
            ->iconButton()
            ->size(Size::Small)
            ->visible(fn (Repeater $component): bool => $component->isDeletable());

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
            ->label(__('filament-forms::components.repeater.actions.move_down.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_MOVE_DOWN) ?? Heroicon::ArrowDown)
            ->color('gray')
            ->action(function (array $arguments, Repeater $component): void {
                $items = array_move_after($component->getRawState(), $arguments['item']);

                $component->rawState($items);

                $component->callAfterStateUpdated();

                $component->shouldPartiallyRenderAfterActionsCalled() ? $component->partiallyRender() : null;
            })
            ->iconButton()
            ->size(Size::Small)
            ->visible(fn (Repeater $component): bool => $component->isReorderable());

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
            ->label(__('filament-forms::components.repeater.actions.move_up.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_MOVE_UP) ?? Heroicon::ArrowUp)
            ->color('gray')
            ->action(function (array $arguments, Repeater $component): void {
                $items = array_move_before($component->getRawState(), $arguments['item']);

                $component->rawState($items);

                $component->callAfterStateUpdated();

                $component->shouldPartiallyRenderAfterActionsCalled() ? $component->partiallyRender() : null;
            })
            ->iconButton()
            ->size(Size::Small)
            ->visible(fn (Repeater $component): bool => $component->isReorderable());

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

    public function getMoveUpActionName(): string
    {
        return 'moveUp';
    }

    public function getReorderAction(): Action
    {
        $action = Action::make($this->getReorderActionName())
            ->label(__('filament-forms::components.repeater.actions.reorder.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_REORDER) ?? Heroicon::ArrowsUpDown)
            ->color('gray')
            ->action(function (array $arguments, Repeater $component): void {
                $items = [
                    ...array_flip($arguments['items']),
                    ...$component->getRawState(),
                ];

                $component->rawState($items);

                $component->callAfterStateUpdated();

                $component->shouldPartiallyRenderAfterActionsCalled() ? $component->partiallyRender() : null;
            })
            ->livewireClickHandlerEnabled(false)
            ->iconButton()
            ->size(Size::Small)
            ->visible(fn (Repeater $component): bool => $component->isReorderableWithDragAndDrop());

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
            ->label(__('filament-forms::components.repeater.actions.collapse.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_COLLAPSE) ?? Heroicon::ChevronUp)
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
            ->label(__('filament-forms::components.repeater.actions.expand.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_EXPAND) ?? Heroicon::ChevronDown)
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
            ->label(__('filament-forms::components.repeater.actions.collapse_all.label'))
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
            ->label(__('filament-forms::components.repeater.actions.expand_all.label'))
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

    public function labelBetweenItems(string | Closure | null $label): static
    {
        $this->labelBetweenItems = $label;

        return $this;
    }

    public function truncateItemLabel(bool | Closure $condition = true): static
    {
        $this->isItemLabelTruncated = $condition;

        return $this;
    }

    public function defaultItems(int | Closure $count): static
    {
        $this->default(static function (Repeater $component) use ($count): array {
            $count = $component->evaluate($count);

            if (! $count) {
                return [];
            }

            return array_fill(0, $count, $component->isSimple() ? null : []);
        });

        $this->shouldMergeHydratedDefaultStateWithItemsStateAfterStateHydrated = false;

        return $this;
    }

    public function default(mixed $state): static
    {
        parent::default(function (Repeater $component) use ($state) {
            $state = $component->evaluate($state);

            $simpleField = $component->getSimpleField();

            $items = [];

            foreach ($state ?? [] as $itemData) {
                if ($simpleField) {
                    $itemData = [$simpleField->getName() => $itemData];
                }

                if ($uuid = $component->generateUuid()) {
                    $items[$uuid] = $itemData;
                } else {
                    $items[] = $itemData;
                }
            }

            $component->hydratedDefaultState = $items;

            return $items;
        });

        $this->shouldMergeHydratedDefaultStateWithItemsStateAfterStateHydrated = true;

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
        $this->addable(fn (Repeater $component): bool => ! $this->evaluate($condition));

        return $this;
    }

    /**
     * @deprecated Use `deletable()` instead.
     */
    public function disableItemDeletion(bool | Closure $condition = true): static
    {
        $this->deletable(fn (Repeater $component): bool => ! $this->evaluate($condition));

        return $this;
    }

    /**
     * @deprecated Use `reorderable()` instead.
     */
    public function disableItemMovement(bool | Closure $condition = true): static
    {
        $this->reorderable(fn (Repeater $component): bool => ! $this->evaluate($condition));

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
     * @deprecated No longer part of the design system.
     */
    public function inset(bool | Closure $condition = true): static
    {
        return $this;
    }

    /**
     * @return array<Schema>
     */
    public function getItems(): array
    {
        if ($this->cachedItems !== null) {
            return $this->cachedItems;
        }

        $relationship = $this->getRelationship();

        $records = $relationship ? $this->getCachedExistingRecords() : null;

        $items = [];

        foreach ($this->getRawState() ?? [] as $itemKey => $itemData) {
            $items[$itemKey] = $this
                ->getChildSchema()
                ->statePath($itemKey)
                ->constantState(((! ($relationship && $records->has($itemKey))) && is_array($itemData)) ? $itemData : null)
                ->model($relationship ? $records[$itemKey] ?? $this->getRelatedModel() : null)
                ->inlineLabel(false)
                ->getClone();
        }

        return $this->cachedItems = $items;
    }

    /**
     * @return array<Schema>
     */
    public function getDefaultChildSchemas(): array
    {
        return $this->getItems();
    }

    public function getAddActionLabel(): string
    {
        return $this->evaluate($this->addActionLabel) ?? __('filament-forms::components.repeater.actions.add.label', [
            'label' => Str::lcfirst($this->getLabel()),
        ]);
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

    public function orderColumn(string | Closure | null $column = 'sort'): static
    {
        $this->orderColumn = $column;
        $this->reorderable($column);

        return $this;
    }

    /**
     * @deprecated Use `orderColumn()` instead.
     */
    public function orderable(string | Closure | null $column = 'sort'): static
    {
        $this->orderColumn($column);

        return $this;
    }

    public function relationship(string | Closure | null $name = null, ?Closure $modifyQueryUsing = null, ?Closure $modifyRecordsUsing = null): static
    {
        $this->relationship = $name ?? $this->getName();
        $this->modifyRelationshipQueryUsing = $modifyQueryUsing;
        $this->modifyRelationshipRecordsUsing = $modifyRecordsUsing;

        $this->afterStateHydrated(static function (Repeater $component): void {
            if (! is_array($component->hydratedDefaultState)) {
                return;
            }

            $component->mergeHydratedDefaultStateWithItemsState();
        });

        $this->loadStateFromRelationshipsUsing(static function (Repeater $component): void {
            $component->clearCachedExistingRecords();

            $component->fillFromRelationship();
        });

        $this->saveRelationshipsUsing(static function (Repeater $component): void {
            $component->saveToRelationship();
        });

        $this->dehydrated(false);

        $this->reorderable(false);

        return $this;
    }

    public function saveToRelationship(): void
    {
        // The raw state may have been mutated through an ancestor schema (e.g. `Schema::rawState()`),
        // which clears that ancestor's cached child schemas but not this component's. Rebuild the
        // memoized items so the save reflects the current state rather than a stale set.
        $this->cachedItems = null;

        $state = $this->getState();

        if (! is_array($state)) {
            $state = [];
        }

        $relationship = $this->getRelationship();

        $existingRecords = $this->getCachedExistingRecords();

        $recordsToDelete = [];

        foreach ($existingRecords->pluck($relationship->getRelated()->getKeyName()) as $keyToCheckForDeletion) {
            if (array_key_exists("record-{$keyToCheckForDeletion}", $state)) {
                continue;
            }

            $recordsToDelete[] = $keyToCheckForDeletion;
            $existingRecords->forget("record-{$keyToCheckForDeletion}");
        }

        if (filled($recordsToDelete)) {
            $relationship
                ->whereKey($recordsToDelete)
                ->get()
                ->each(function (Model $record): void {
                    $record->delete();
                    $this->callAfterDelete($record);
                });
        }

        $itemOrder = 1;
        $orderColumn = $this->getOrderColumn();

        $translatableContentDriver = $this->getLivewire()->makeFilamentTranslatableContentDriver();

        foreach ($this->getItems() as $itemKey => $item) {
            $itemData = $item->getState(shouldCallHooksBefore: false);

            if ($orderColumn) {
                $itemData[$orderColumn] = $itemOrder;

                $itemOrder++;
            }

            if ($record = ($existingRecords[$itemKey] ?? null)) {
                $itemData = $this->mutateRelationshipDataBeforeSave($itemData, record: $record);

                if ($itemData === null) {
                    continue;
                }

                $translatableContentDriver ?
                    $translatableContentDriver->updateRecord($record, $itemData) :
                    $record->fill($itemData)->save();

                $this->callAfterUpdate($itemData, $record);

                continue;
            }

            $relatedModel = $this->getRelatedModel();

            $itemData = $this->mutateRelationshipDataBeforeCreate($itemData);

            if ($itemData === null) {
                continue;
            }

            if ($translatableContentDriver) {
                $record = $translatableContentDriver->makeRecord($relatedModel, $itemData);
            } else {
                $record = new $relatedModel;
                $record->fill($itemData);
            }

            $record = $relationship->save($record);
            $item->model($record)->saveRelationships();
            $this->callAfterCreate($itemData, $record);
            $existingRecords->push($record);
        }

        $this->getRecord()->setRelation($this->getRelationshipName(), $existingRecords);
    }

    /**
     * After hydrating the state of child component containers, the default state
     * of fields inside the repeater can be lost, if it was defined on the repeater
     * itself. This method merges the hydrated default state with the state of the
     * child component containers, so that the default state of the fields inside
     * the repeater is preserved.
     */
    protected function mergeHydratedDefaultStateWithItemsState(): void
    {
        $state = $this->getRawState();
        $items = $this->hydratedDefaultState;

        foreach ($items as $itemKey => $itemData) {
            $items[$itemKey] = [
                ...$state[$itemKey] ?? [],
                ...$itemData,
            ];
        }

        $this->rawState($items);
    }

    public function itemLabel(string | Htmlable | Closure | null $label): static
    {
        $this->itemLabel = $label;

        return $this;
    }

    public function itemNumbers(bool | Closure $condition = true): static
    {
        $this->hasItemNumbers = $condition;

        return $this;
    }

    public function itemHeaders(bool | Closure $condition = true): static
    {
        $this->hasItemHeaders = $condition;

        return $this;
    }

    public function fillFromRelationship(): void
    {
        $this->state(
            $this->getStateFromRelatedRecords($this->getCachedExistingRecords()),
        );
    }

    /**
     * @return array<array<string, mixed>>
     */
    protected function getStateFromRelatedRecords(Collection $records): array
    {
        if (! $records->count()) {
            return [];
        }

        $translatableContentDriver = $this->getLivewire()->makeFilamentTranslatableContentDriver();

        return $records
            ->map(function (Model $record) use ($translatableContentDriver): array {
                $data = $translatableContentDriver ?
                    $translatableContentDriver->getRecordAttributesToArray($record) :
                    $record->attributesToArray();

                return $this->mutateRelationshipDataBeforeFill($data);
            })
            ->toArray();
    }

    public function getLabel(): string | Htmlable | null
    {
        if ($this->label === null && $this->hasRelationship()) {
            $label = (string) str($this->getRelationshipName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();

            return ($this->shouldTranslateLabel) ? __($label) : $label;
        }

        return parent::getLabel();
    }

    public function getOrderColumn(): ?string
    {
        return $this->evaluate($this->orderColumn);
    }

    public function getRelationship(): HasOneOrMany | BelongsToMany | null
    {
        if (! $this->hasRelationship()) {
            return null;
        }

        $record = $this->getModelInstance();

        $relationshipName = $this->getRelationshipName();

        if ($record->hasAttribute($relationshipName) || (! $record->isRelation($relationshipName))) {
            throw new LogicException("The relationship [{$relationshipName}] does not exist on the model [{$this->getModel()}].");
        }

        return $this->getModelInstance()->{$relationshipName}();
    }

    public function getRelationshipName(): ?string
    {
        return $this->evaluate($this->relationship);
    }

    protected function modifyRelationshipRecords(Collection $records): Collection
    {
        return $this->evaluate(
            $this->modifyRelationshipRecordsUsing,
            namedInjections: [
                'records' => $records,
            ],
            typedInjections: [
                Collection::class => $records,
            ],
        ) ?? $records;
    }

    public function getCachedExistingRecords(): Collection
    {
        if ($this->cachedExistingRecords) {
            return $this->cachedExistingRecords;
        }

        if (! $this->getModelInstance()?->exists) {
            return $this->cachedExistingRecords = new Collection;
        }

        $relationship = $this->getRelationship();
        $relatedKeyName = $relationship->getRelated()->getKeyName();

        $relationshipName = $this->getRelationshipName();
        $orderColumn = $this->getOrderColumn();

        if (
            $this->getModelInstance()->relationLoaded($relationshipName) &&
            (! $this->modifyRelationshipQueryUsing)
        ) {
            return $this->cachedExistingRecords = $this->modifyRelationshipRecords($this->getRecord()->getRelationValue($relationshipName)
                ->when(filled($orderColumn), fn (Collection $records) => $records->sortBy($orderColumn))
                ->mapWithKeys(
                    fn (Model $item): array => ["record-{$item[$relatedKeyName]}" => $item],
                ));
        }

        $relationshipQuery = $relationship->getQuery();

        // Explicitly select the related table's columns so the query is not ambiguous if it is
        // later modified to include a join (for example, through `modifyRelationshipQueryUsing()`).
        // Without this, `select *` across a join can hydrate the key from the wrong table.
        if ($relationship instanceof BelongsToMany) {
            $relationshipQuery->select([
                $relationship->getTable() . '.*',
                $relationshipQuery->getModel()->getTable() . '.*',
            ]);
        } else {
            $relationshipQuery->select($relationshipQuery->getModel()->getTable() . '.*');
        }

        if ($this->modifyRelationshipQueryUsing) {
            $relationshipQuery = $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $relationshipQuery,
            ]) ?? $relationshipQuery;
        }

        if (filled($orderColumn)) {
            // Qualify the order column so it is not ambiguous when the query includes a join.
            $relationshipQuery->orderBy($relationshipQuery->qualifyColumn($orderColumn));
        }

        return $this->cachedExistingRecords = $this->modifyRelationshipRecords($relationshipQuery->get()->mapWithKeys(
            fn (Model $item): array => ["record-{$item[$relatedKeyName]}" => $item],
        ));
    }

    public function getItemLabel(string $key, ?int $index = null): string | Htmlable | null
    {
        $container = $this->getChildSchema($key);

        return $this->evaluate($this->itemLabel, [
            'container' => $container,
            'item' => $container,
            'key' => $key,
            'schema' => $container,
            'state' => $container->getStateSnapshot(),
            'uuid' => $key,
            'index' => $index,
        ]);
    }

    public function hasItemLabels(): bool
    {
        return $this->itemLabel !== null;
    }

    public function hasItemNumbers(): bool
    {
        return (bool) $this->evaluate($this->hasItemNumbers);
    }

    public function hasItemHeaders(): bool
    {
        return (bool) $this->evaluate($this->hasItemHeaders);
    }

    public function simple(Field | Closure | null $field): static
    {
        $this->simpleField = $field;
        $this->schema(fn (Repeater $component): array => [$component->getSimpleField()]);

        return $this;
    }

    public function isSimple(): bool
    {
        return $this->simpleField !== null;
    }

    /**
     * @param  array<TableColumn> | Closure | null  $columns
     */
    public function table(array | Closure | null $columns): static
    {
        $this->tableColumns = $columns;

        return $this;
    }

    /**
     * @return ?array<TableColumn>
     */
    public function getTableColumns(): ?array
    {
        return $this->evaluate($this->tableColumns);
    }

    public function isTable(): bool
    {
        return filled($this->getTableColumns());
    }

    public function getSimpleField(): ?Field
    {
        return ($this->cachedSimpleField ??= $this->evaluate($this->simpleField))?->hiddenLabel();
    }

    public function clearCachedExistingRecords(): void
    {
        $this->cachedExistingRecords = null;
        $this->cachedItems = null;
    }

    public function clearCachedChildSchemas(): void
    {
        parent::clearCachedChildSchemas();

        $this->cachedItems = null;
    }

    /**
     * @return class-string<Model>
     */
    public function getRelatedModel(): string
    {
        return $this->getRelationship()->getModel()::class;
    }

    public function hasRelationship(): bool
    {
        return filled($this->getRelationshipName());
    }

    public function mutateRelationshipDataBeforeCreateUsing(?Closure $callback): static
    {
        $this->mutateRelationshipDataBeforeCreateUsing = $callback;

        return $this;
    }

    /**
     * @param  array<array<string, mixed>>  $data
     * @return array<array<string, mixed>> | null
     */
    public function mutateRelationshipDataBeforeCreate(array $data): ?array
    {
        if ($this->mutateRelationshipDataBeforeCreateUsing instanceof Closure) {
            $data = $this->evaluate($this->mutateRelationshipDataBeforeCreateUsing, [
                'data' => $data,
            ]);
        }

        return $data;
    }

    public function mutateRelationshipDataBeforeSaveUsing(?Closure $callback): static
    {
        $this->mutateRelationshipDataBeforeSaveUsing = $callback;

        return $this;
    }

    /**
     * @param  array<array<string, mixed>>  $data
     * @return array<array<string, mixed>>
     */
    public function mutateRelationshipDataBeforeFill(array $data): array
    {
        if ($this->mutateRelationshipDataBeforeFillUsing instanceof Closure) {
            $data = $this->evaluate($this->mutateRelationshipDataBeforeFillUsing, [
                'data' => $data,
            ]);
        }

        return $data;
    }

    public function mutateRelationshipDataBeforeFillUsing(?Closure $callback): static
    {
        $this->mutateRelationshipDataBeforeFillUsing = $callback;

        return $this;
    }

    /**
     * @param  array<array<string, mixed>>  $data
     * @return array<array<string, mixed>> | null
     */
    public function mutateRelationshipDataBeforeSave(array $data, Model $record): ?array
    {
        if ($this->mutateRelationshipDataBeforeSaveUsing instanceof Closure) {
            $data = $this->evaluate(
                $this->mutateRelationshipDataBeforeSaveUsing,
                namedInjections: [
                    'data' => $data,
                    'record' => $record,
                ],
                typedInjections: [
                    Model::class => $record,
                    $record::class => $record,
                ],
            );
        }

        return $data;
    }

    public function afterCreate(?Closure $callback): static
    {
        $this->afterCreate = $callback;

        return $this;
    }

    public function afterUpdate(?Closure $callback): static
    {
        $this->afterUpdate = $callback;

        return $this;
    }

    public function afterDelete(?Closure $callback): static
    {
        $this->afterDelete = $callback;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function callAfterCreate(array $data, Model $record): void
    {
        if ($this->afterCreate instanceof Closure) {
            $this->evaluate(
                $this->afterCreate,
                namedInjections: [
                    'data' => $data,
                    'record' => $record,
                ],
                typedInjections: [
                    Model::class => $record,
                    $record::class => $record,
                ],
            );
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function callAfterUpdate(array $data, Model $record): void
    {
        if ($this->afterUpdate instanceof Closure) {
            $this->evaluate(
                $this->afterUpdate,
                namedInjections: [
                    'data' => $data,
                    'record' => $record,
                ],
                typedInjections: [
                    Model::class => $record,
                    $record::class => $record,
                ],
            );
        }
    }

    protected function callAfterDelete(Model $record): void
    {
        if ($this->afterDelete instanceof Closure) {
            $this->evaluate(
                $this->afterDelete,
                namedInjections: [
                    'record' => $record,
                ],
                typedInjections: [
                    Model::class => $record,
                    $record::class => $record,
                ],
            );
        }
    }

    public function canConcealComponents(): bool
    {
        return $this->isCollapsible();
    }

    public function getPublishedViewOverrideCheckPath(): ?string
    {
        if ($this->isTable()) {
            return 'filament-forms::components.repeater.table';
        }

        if ($this->isSimple()) {
            return 'filament-forms::components.repeater.simple';
        }

        return 'filament-forms::components.repeater.index';
    }

    public function toEmbeddedHtml(): string
    {
        if ($this->isTable()) {
            return $this->toTableEmbeddedHtml();
        }

        if ($this->isSimple()) {
            return $this->toSimpleEmbeddedHtml();
        }

        $items = $this->getItems();

        $addAction = $this->getAction($this->getAddActionName());
        $addActionAlignment = $this->getAddActionAlignment();
        $addBetweenAction = $this->getAction($this->getAddBetweenActionName());
        $cloneAction = $this->getAction($this->getCloneActionName());
        $collapseAllAction = $this->getAction($this->getCollapseAllActionName());
        $expandAllAction = $this->getAction($this->getExpandAllActionName());
        $deleteAction = $this->getAction($this->getDeleteActionName());
        $moveDownAction = $this->getAction($this->getMoveDownActionName());
        $moveUpAction = $this->getAction($this->getMoveUpActionName());
        $reorderAction = $this->getAction($this->getReorderActionName());
        $extraItemActions = $this->getExtraItemActions();

        $hasItemNumbers = $this->hasItemNumbers();
        $hasItemHeaders = $this->hasItemHeaders();
        $isAddable = $this->isAddable();
        $isCloneable = $this->isCloneable();
        $isCollapsible = $this->isCollapsible();
        $isDeletable = $this->isDeletable();
        $isReorderableWithButtons = $this->isReorderableWithButtons();
        $isReorderableWithDragAndDrop = $this->isReorderableWithDragAndDrop();

        $collapseAllActionIsVisible = $isCollapsible && $collapseAllAction->isVisible();
        $expandAllActionIsVisible = $isCollapsible && $expandAllAction->isVisible();
        $persistCollapsed = $this->shouldPersistCollapsed();

        $key = $this->getKey();
        $statePath = $this->getStatePath();

        $itemLabelHeadingTag = $this->getHeadingTag();
        $isItemLabelTruncated = $this->isItemLabelTruncated();
        $labelBetweenItems = $this->getLabelBetweenItems();

        $id = $this->getId();

        $outerAttributes = (new FilamentComponentAttributeBag)
            ->merge($this->getExtraAttributes(), escape: false)
            ->merge([
                'aria-labelledby' => "{$id}-label",
                'id' => $id,
                'role' => 'group',
            ], escape: false)
            ->class([
                'fi-fo-repeater',
                'fi-collapsible' => $isCollapsible,
            ]);

        $itemsAttributes = (new FilamentComponentAttributeBag)
            ->grid($this->getGridColumns())
            ->merge([
                'data-sortable-animation-duration' => $this->getReorderAnimationDuration(),
                'x-on:end.stop' => '$wire.mountAction(\'reorder\', { items: $event.target.sortable.toArray() }, { schemaComponent: \'' . $key . '\' })',
            ], escape: false)
            ->class(['fi-fo-repeater-items']);

        $itemCount = count($items);
        $itemIndex = 0;

        ob_start(); ?>

        <div <?= $outerAttributes->toHtml() ?>>
            <?php if ($collapseAllActionIsVisible || $expandAllActionIsVisible) { ?>
                <div
                    <?= (new FilamentComponentAttributeBag)->class([
                        'fi-fo-repeater-actions',
                        'fi-hidden' => $itemCount < 2,
                    ])->toHtml() ?>
                >
                    <?php if ($collapseAllActionIsVisible) { ?>
                        <span x-on:click="$dispatch('repeater-collapse', '<?= e($statePath) ?>')">
                            <?= $collapseAllAction->toHtml() ?>
                        </span>
                    <?php } ?>

                    <?php if ($expandAllActionIsVisible) { ?>
                        <span x-on:click="$dispatch('repeater-expand', '<?= e($statePath) ?>')">
                            <?= $expandAllAction->toHtml() ?>
                        </span>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if ($itemCount) { ?>
                <ul x-sortable <?= $itemsAttributes->toHtml() ?>>
                    <?php foreach ($items as $itemKey => $item) { ?>
                        <?php
                            $itemIndex++;
                        $isFirst = $itemIndex === 1;
                        $isLast = $itemIndex === $itemCount;

                        $itemLabel = $this->getItemLabel($itemKey, $itemIndex - 1);
                        $visibleExtraItemActions = array_filter(
                            $extraItemActions,
                            fn (Action $action): bool => $action(['item' => $itemKey])->isVisible(),
                        );
                        $itemCloneAction = $cloneAction(['item' => $itemKey]);
                        $cloneActionIsVisible = $isCloneable && $itemCloneAction->isVisible();
                        $itemDeleteAction = $deleteAction(['item' => $itemKey]);
                        $deleteActionIsVisible = $isDeletable && $itemDeleteAction->isVisible();
                        $itemMoveDownAction = $moveDownAction(['item' => $itemKey])->disabled($isLast);
                        $moveDownActionIsVisible = $isReorderableWithButtons && $itemMoveDownAction->isVisible();
                        $itemMoveUpAction = $moveUpAction(['item' => $itemKey])->disabled($isFirst);
                        $moveUpActionIsVisible = $isReorderableWithButtons && $itemMoveUpAction->isVisible();
                        $reorderActionIsVisible = $isReorderableWithDragAndDrop && $reorderAction->isVisible();
                        $hasItemHeader = $hasItemHeaders && ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible || filled($itemLabel) || $cloneActionIsVisible || $deleteActionIsVisible || $isCollapsible || $visibleExtraItemActions);
                        ?>

                        <li
                            wire:ignore.self
                            wire:key="<?= e($item->getLivewireKey()) ?>.item"
                            x-data="{
                                isCollapsed: <?php if ($persistCollapsed) { ?>$persist(<?= Js::from($this->isCollapsed($item)) ?>).as(`repeater-${<?= Js::from($key) ?>}-${<?= Js::from($itemKey) ?>}-isCollapsed`)<?php } else { ?><?= Js::from($this->isCollapsed($item)) ?><?php } ?>,
                            }"
                            x-on:repeater-expand.window="$event.detail === '<?= e($statePath) ?>' && (isCollapsed = false)"
                            x-on:repeater-collapse.window="$event.detail === '<?= e($statePath) ?>' && (isCollapsed = true)"
                            x-on:expand="isCollapsed = false"
                            x-sortable-item="<?= e($itemKey) ?>"
                            <?= (new FilamentComponentAttributeBag)->class([
                                'fi-fo-repeater-item',
                                'fi-fo-repeater-item-has-header' => $hasItemHeader,
                            ])->toHtml() ?>
                            x-bind:class="{ 'fi-collapsed': isCollapsed }"
                        >
                            <?php if ($hasItemHeader) { ?>
                                <div
                                    <?php if ($isCollapsible) { ?>
                                        x-on:click.stop="isCollapsed = !isCollapsed"
                                    <?php } ?>
                                    class="fi-fo-repeater-item-header"
                                >
                                    <?php if ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible) { ?>
                                        <ul class="fi-fo-repeater-item-header-start-actions">
                                            <?php if ($reorderActionIsVisible) { ?>
                                                <li x-on:click.stop>
                                                    <?= $reorderAction->extraAttributes(['x-sortable-handle' => true], merge: true)->toHtml() ?>
                                                </li>
                                            <?php } ?>

                                            <?php if ($moveUpActionIsVisible || $moveDownActionIsVisible) { ?>
                                                <li x-on:click.stop><?= $itemMoveUpAction->toHtml() ?></li>
                                                <li x-on:click.stop><?= $itemMoveDownAction->toHtml() ?></li>
                                            <?php } ?>
                                        </ul>
                                    <?php } ?>

                                    <?php if (filled($itemLabel)) { ?>
                                        <<?= e($itemLabelHeadingTag) ?>
                                            <?= (new FilamentComponentAttributeBag)->class([
                                                'fi-fo-repeater-item-header-label',
                                                'fi-truncated' => $isItemLabelTruncated,
                                            ])->toHtml() ?>
                                        >
                                            <?= e($itemLabel) ?>
                                            <?php if ($hasItemNumbers) { ?>
                                                <?= e($itemIndex) ?>
                                            <?php } ?>
                                        </<?= e($itemLabelHeadingTag) ?>>
                                    <?php } ?>

                                    <?php if ($cloneActionIsVisible || $deleteActionIsVisible || $isCollapsible || $visibleExtraItemActions) { ?>
                                        <ul class="fi-fo-repeater-item-header-end-actions">
                                            <?php foreach ($visibleExtraItemActions as $extraItemAction) { ?>
                                                <li x-on:click.stop><?= $extraItemAction(['item' => $itemKey])->toHtml() ?></li>
                                            <?php } ?>

                                            <?php if ($cloneActionIsVisible) { ?>
                                                <li x-on:click.stop><?= $itemCloneAction->toHtml() ?></li>
                                            <?php } ?>

                                            <?php if ($deleteActionIsVisible) { ?>
                                                <li x-on:click.stop><?= $itemDeleteAction->toHtml() ?></li>
                                            <?php } ?>

                                            <?php if ($isCollapsible) { ?>
                                                <li class="fi-fo-repeater-item-header-collapsible-actions" x-on:click.stop="isCollapsed = !isCollapsed">
                                                    <div class="fi-fo-repeater-item-header-collapse-action">
                                                        <?= $this->getAction('collapse')->toHtml() ?>
                                                    </div>
                                                    <div class="fi-fo-repeater-item-header-expand-action">
                                                        <?= $this->getAction('expand')->toHtml() ?>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <div x-show="! isCollapsed" class="fi-fo-repeater-item-content">
                                <?= $item->toHtml() ?>
                            </div>
                        </li>

                        <?php if (! $isLast) { ?>
                            <?php if ($isAddable && $addBetweenAction(['afterItem' => $itemKey])->isVisible()) { ?>
                                <li class="fi-fo-repeater-add-between-items-ctn">
                                    <div class="fi-fo-repeater-add-between-items">
                                        <?= $addBetweenAction(['afterItem' => $itemKey])->toHtml() ?>
                                    </div>
                                </li>
                            <?php } elseif (filled($labelBetweenItems)) { ?>
                                <li class="fi-fo-repeater-label-between-items-ctn">
                                    <div class="fi-fo-repeater-label-between-items-divider-before"></div>
                                    <span class="fi-fo-repeater-label-between-items"><?= e($labelBetweenItems) ?></span>
                                    <div class="fi-fo-repeater-label-between-items-divider-after"></div>
                                </li>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </ul>
            <?php } ?>

            <?php if ($isAddable && $addAction->isVisible()) { ?>
                <div
                    <?= (new FilamentComponentAttributeBag)->class([
                        'fi-fo-repeater-add',
                        ($addActionAlignment instanceof Alignment) ? ('fi-align-' . $addActionAlignment->value) : $addActionAlignment,
                    ])->toHtml() ?>
                >
                    <?= $addAction->toHtml() ?>
                </div>
            <?php } ?>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean(), labelTag: 'div');
    }

    protected function toSimpleEmbeddedHtml(): string
    {
        $items = $this->getItems();

        $addAction = $this->getAction($this->getAddActionName());
        $addActionAlignment = $this->getAddActionAlignment();
        $cloneAction = $this->getAction($this->getCloneActionName());
        $deleteAction = $this->getAction($this->getDeleteActionName());
        $moveDownAction = $this->getAction($this->getMoveDownActionName());
        $moveUpAction = $this->getAction($this->getMoveUpActionName());
        $reorderAction = $this->getAction($this->getReorderActionName());
        $extraItemActions = $this->getExtraItemActions();

        $isAddable = $this->isAddable();
        $isCloneable = $this->isCloneable();
        $isDeletable = $this->isDeletable();
        $isReorderableWithButtons = $this->isReorderableWithButtons();
        $isReorderableWithDragAndDrop = $this->isReorderableWithDragAndDrop();

        $key = $this->getKey();
        $statePath = $this->getStatePath();

        $id = $this->getId();

        $outerAttributes = (new FilamentComponentAttributeBag)
            ->merge($this->getExtraAttributes(), escape: false)
            ->merge([
                'aria-labelledby' => "{$id}-label",
                'id' => $id,
                'role' => 'group',
            ], escape: false)
            ->class(['fi-fo-simple-repeater']);

        $itemsAttributes = (new FilamentComponentAttributeBag)
            ->grid($this->getGridColumns())
            ->merge([
                'data-sortable-animation-duration' => $this->getReorderAnimationDuration(),
                'x-on:end.stop' => '$wire.mountAction(\'reorder\', { items: $event.target.sortable.toArray() }, { schemaComponent: \'' . $key . '\' })',
            ], escape: false)
            ->class(['fi-fo-simple-repeater-items']);

        $itemCount = count($items);
        $itemIndex = 0;

        ob_start(); ?>

        <div <?= $outerAttributes->toHtml() ?>>
            <?php if ($itemCount) { ?>
                <ul x-sortable <?= $itemsAttributes->toHtml() ?>>
                    <?php foreach ($items as $itemKey => $item) { ?>
                        <?php
                            $itemIndex++;
                        $isFirst = $itemIndex === 1;
                        $isLast = $itemIndex === $itemCount;

                        $visibleExtraItemActions = array_filter(
                            $extraItemActions,
                            fn (Action $action): bool => $action(['item' => $itemKey])->isVisible(),
                        );
                        $itemCloneAction = $cloneAction(['item' => $itemKey]);
                        $cloneActionIsVisible = $isCloneable && $itemCloneAction->isVisible();
                        $itemDeleteAction = $deleteAction(['item' => $itemKey]);
                        $deleteActionIsVisible = $isDeletable && $itemDeleteAction->isVisible();
                        $itemMoveDownAction = $moveDownAction(['item' => $itemKey])->disabled($isLast);
                        $moveDownActionIsVisible = $isReorderableWithButtons && $itemMoveDownAction->isVisible();
                        $itemMoveUpAction = $moveUpAction(['item' => $itemKey])->disabled($isFirst);
                        $moveUpActionIsVisible = $isReorderableWithButtons && $itemMoveUpAction->isVisible();
                        $reorderActionIsVisible = $isReorderableWithDragAndDrop && $reorderAction->isVisible();
                        ?>

                        <li
                            wire:key="<?= e($item->getLivewireKey()) ?>.item"
                            x-sortable-item="<?= e($itemKey) ?>"
                            class="fi-fo-simple-repeater-item"
                        >
                            <div class="fi-fo-simple-repeater-item-content">
                                <?= $item->toHtml() ?>
                            </div>

                            <?php if ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible || $cloneActionIsVisible || $deleteActionIsVisible || $visibleExtraItemActions) { ?>
                                <ul class="fi-fo-simple-repeater-item-actions">
                                    <?php if ($reorderActionIsVisible) { ?>
                                        <li x-on:click.stop>
                                            <?= $reorderAction->extraAttributes(['x-sortable-handle' => true], merge: true)->toHtml() ?>
                                        </li>
                                    <?php } ?>

                                    <?php if ($moveUpActionIsVisible || $moveDownActionIsVisible) { ?>
                                        <li x-on:click.stop>
                                            <?= $itemMoveUpAction->toHtml() ?>
                                        </li>

                                        <li x-on:click.stop>
                                            <?= $itemMoveDownAction->toHtml() ?>
                                        </li>
                                    <?php } ?>

                                    <?php foreach ($visibleExtraItemActions as $extraItemAction) { ?>
                                        <li x-on:click.stop>
                                            <?= $extraItemAction(['item' => $itemKey])->toHtml() ?>
                                        </li>
                                    <?php } ?>

                                    <?php if ($cloneActionIsVisible) { ?>
                                        <li x-on:click.stop>
                                            <?= $itemCloneAction->toHtml() ?>
                                        </li>
                                    <?php } ?>

                                    <?php if ($deleteActionIsVisible) { ?>
                                        <li x-on:click.stop>
                                            <?= $itemDeleteAction->toHtml() ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>

            <?php if ($isAddable && $addAction->isVisible()) { ?>
                <div
                    <?= (new FilamentComponentAttributeBag)->class([
                        'fi-fo-simple-repeater-add',
                        ($addActionAlignment instanceof Alignment) ? ('fi-align-' . $addActionAlignment->value) : $addActionAlignment,
                    ])->toHtml() ?>
                >
                    <?= $addAction->toHtml() ?>
                </div>
            <?php } ?>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean(), labelTag: 'div');
    }

    protected function toTableEmbeddedHtml(): string
    {
        $items = $this->getItems();

        $addAction = $this->getAction($this->getAddActionName());
        $addActionAlignment = $this->getAddActionAlignment();
        $cloneAction = $this->getAction($this->getCloneActionName());
        $deleteAction = $this->getAction($this->getDeleteActionName());
        $moveDownAction = $this->getAction($this->getMoveDownActionName());
        $moveUpAction = $this->getAction($this->getMoveUpActionName());
        $reorderAction = $this->getAction($this->getReorderActionName());
        $extraItemActions = $this->getExtraItemActions();

        $isAddable = $this->isAddable();
        $isCloneable = $this->isCloneable();
        $isDeletable = $this->isDeletable();
        $isReorderableWithButtons = $this->isReorderableWithButtons();
        $isReorderableWithDragAndDrop = $this->isReorderableWithDragAndDrop();

        $key = $this->getKey();
        $statePath = $this->getStatePath();

        $tableColumns = $this->getTableColumns() ?? [];

        $isCompact = $this->isCompact();

        $id = $this->getId();

        $outerAttributes = (new FilamentComponentAttributeBag)
            ->merge($this->getExtraAttributes(), escape: false)
            ->merge([
                'aria-labelledby' => "{$id}-label",
                'id' => $id,
                'role' => 'group',
            ], escape: false)
            ->class([
                'fi-fo-table-repeater',
                'fi-compact' => $isCompact,
            ]);

        $tbodyAttributes = (new FilamentComponentAttributeBag)
            ->merge([
                'data-sortable-animation-duration' => $this->getReorderAnimationDuration(),
                'x-on:end.stop' => '$wire.mountAction(\'reorder\', { items: $event.target.sortable.toArray() }, { schemaComponent: \'' . $key . '\' })',
            ], escape: false);

        $itemCount = count($items);
        $itemIndex = 0;
        $hasReorderColumn = ($itemCount > 1) && ($isReorderableWithButtons || $isReorderableWithDragAndDrop);
        $hasActionsColumn = count($extraItemActions) || $isCloneable || $isDeletable;

        ob_start(); ?>

        <div <?= $outerAttributes->toHtml() ?>>
            <?php if ($itemCount) { ?>
                <table>
                    <thead>
                        <tr>
                            <?php if ($hasReorderColumn) { ?>
                                <th scope="col" class="fi-fo-table-repeater-empty-header-cell">
                                    <span class="fi-sr-only"><?= e(__('filament-forms::components.repeater.columns.reorder.label')) ?></span>
                                </th>
                            <?php } ?>

                            <?php foreach ($tableColumns as $column) { ?>
                                <?php
                                    $columnAlignment = $column->getAlignment();
                                $columnWidth = $column->getWidth();
                                $thAttributes = (new FilamentComponentAttributeBag)
                                    ->class([
                                        'fi-wrapped' => $column->canHeaderWrap(),
                                        ($columnAlignment instanceof Alignment) ? ('fi-align-' . $columnAlignment->value) : $columnAlignment,
                                    ]);

                                if (filled($columnWidth)) {
                                    $thAttributes = $thAttributes->style(['width: ' . e($columnWidth)]);
                                }
                                ?>
                                <th scope="col" <?= $thAttributes->toHtml() ?>>
                                    <?php if (! $column->isHeaderLabelHidden()) { ?>
                                        <?= e($column->getLabel()) ?><?php if ($column->isMarkedAsRequired()) { ?><sup class="fi-fo-table-repeater-header-required-mark">*</sup><?php } ?>
                                    <?php } else { ?>
                                        <span class="fi-sr-only">
                                            <?= e($column->getLabel()) ?>
                                        </span>
                                    <?php } ?>
                                </th>
                            <?php } ?>

                            <?php if ($hasActionsColumn) { ?>
                                <th scope="col" class="fi-fo-table-repeater-empty-header-cell">
                                    <span class="fi-sr-only"><?= e(__('filament-forms::components.repeater.columns.actions.label')) ?></span>
                                </th>
                            <?php } ?>
                        </tr>
                    </thead>

                    <tbody x-sortable <?= $tbodyAttributes->toHtml() ?>>
                        <?php foreach ($items as $itemKey => $item) { ?>
                            <?php
                                $itemIndex++;
                            $isFirst = $itemIndex === 1;
                            $isLast = $itemIndex === $itemCount;

                            $visibleExtraItemActions = array_filter(
                                $extraItemActions,
                                fn (Action $action): bool => $action(['item' => $itemKey])->isVisible(),
                            );
                            $itemCloneAction = $cloneAction(['item' => $itemKey]);
                            $cloneActionIsVisible = $isCloneable && $itemCloneAction->isVisible();
                            $itemDeleteAction = $deleteAction(['item' => $itemKey]);
                            $deleteActionIsVisible = $isDeletable && $itemDeleteAction->isVisible();
                            $itemMoveDownAction = $moveDownAction(['item' => $itemKey])->disabled($isLast);
                            $moveDownActionIsVisible = $isReorderableWithButtons && $itemMoveDownAction->isVisible();
                            $itemMoveUpAction = $moveUpAction(['item' => $itemKey])->disabled($isFirst);
                            $moveUpActionIsVisible = $isReorderableWithButtons && $itemMoveUpAction->isVisible();
                            $reorderActionIsVisible = $isReorderableWithDragAndDrop && $reorderAction->isVisible();
                            ?>

                            <tr
                                wire:key="<?= e($item->getLivewireKey()) ?>.item"
                                x-sortable-item="<?= e($itemKey) ?>"
                            >
                                <?php if ($hasReorderColumn) { ?>
                                    <td>
                                        <?php if ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible) { ?>
                                            <div class="fi-fo-table-repeater-actions">
                                                <?php if ($reorderActionIsVisible) { ?>
                                                    <div x-on:click.stop>
                                                        <?= $reorderAction->extraAttributes(['x-sortable-handle' => true], merge: true)->toHtml() ?>
                                                    </div>
                                                <?php } ?>

                                                <?php if ($moveUpActionIsVisible || $moveDownActionIsVisible) { ?>
                                                    <div x-on:click.stop>
                                                        <?= $itemMoveUpAction->toHtml() ?>
                                                    </div>

                                                    <div x-on:click.stop>
                                                        <?= $itemMoveDownAction->toHtml() ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </td>
                                <?php } ?>

                                <?php
                                    $counter = 0;
                            ?>

                                <?php foreach ($item->getComponents(withHidden: true) as $schemaComponent) { ?>
                                    <?php
                                    throw_unless(
                                        $schemaComponent instanceof Component,
                                        new \Exception('Table repeaters must only contain schema components, but [' . $schemaComponent::class . '] was used.'),
                                    );
                                    ?>

                                    <?php if (count($tableColumns) > $counter) { ?>
                                        <?php if ($schemaComponent instanceof Hidden) { ?>
                                            <?= $schemaComponent->toHtml() ?>
                                        <?php } else { ?>
                                            <?php
                                                $counter++;
                                            ?>

                                            <?php if ($schemaComponent->isVisible()) { ?>
                                                <?php
                                                    $currentColumn = $tableColumns[$counter - 1] ?? null;
                                                $columnVerticalAlignment = $currentColumn?->getVerticalAlignment();
                                                $tdAttributes = (new FilamentComponentAttributeBag)
                                                    ->class([
                                                        ($columnVerticalAlignment instanceof VerticalAlignment) ? ('fi-vertical-align-' . $columnVerticalAlignment->value) : (is_string($columnVerticalAlignment) ? $columnVerticalAlignment : ''),
                                                    ]);
                                                ?>
                                                <td <?= $tdAttributes->toHtml() ?>>
                                                    <?= $schemaComponent->toSchemaHtml() ?>
                                                </td>
                                            <?php } else { ?>
                                                <td class="fi-hidden"></td>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>

                                <?php if ($hasActionsColumn) { ?>
                                    <td>
                                        <?php if ($visibleExtraItemActions || $cloneActionIsVisible || $deleteActionIsVisible) { ?>
                                            <div class="fi-fo-table-repeater-actions">
                                                <?php foreach ($visibleExtraItemActions as $extraItemAction) { ?>
                                                    <div x-on:click.stop>
                                                        <?= $extraItemAction(['item' => $itemKey])->toHtml() ?>
                                                    </div>
                                                <?php } ?>

                                                <?php if ($cloneActionIsVisible) { ?>
                                                    <div x-on:click.stop>
                                                        <?= $itemCloneAction->toHtml() ?>
                                                    </div>
                                                <?php } ?>

                                                <?php if ($deleteActionIsVisible) { ?>
                                                    <div x-on:click.stop>
                                                        <?= $itemDeleteAction->toHtml() ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>

            <?php if ($isAddable && $addAction->isVisible()) { ?>
                <div
                    <?= (new FilamentComponentAttributeBag)->class([
                        'fi-fo-table-repeater-add',
                        ($addActionAlignment instanceof Alignment) ? ('fi-align-' . $addActionAlignment->value) : $addActionAlignment,
                    ])->toHtml() ?>
                >
                    <?= $addAction->toHtml() ?>
                </div>
            <?php } ?>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean(), labelTag: 'div');
    }

    public function getLabelBetweenItems(): ?string
    {
        return $this->evaluate($this->labelBetweenItems);
    }

    public function isItemLabelTruncated(): bool
    {
        return (bool) $this->evaluate($this->isItemLabelTruncated);
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
        return $this->getChildSchema($key)->getStateSnapshot();
    }

    public function getHeadingsCount(): int
    {
        if (! $this->hasItemLabels()) {
            return 0;
        }

        return 1;
    }

    public function partiallyRenderAfterActionsCalled(bool | Closure | null $condition = true): static
    {
        $this->shouldPartiallyRenderAfterActionsCalled = $condition;

        return $this;
    }

    public function shouldPartiallyRenderAfterActionsCalled(): bool
    {
        $condition = $this->evaluate($this->shouldPartiallyRenderAfterActionsCalled);

        if ($condition !== null) {
            return (bool) $condition;
        }

        return ! $this->isLive();
    }
}
