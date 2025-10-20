<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\View\FormsIconAlias;
use Filament\Schemas\Components\Concerns\CanBeCollapsed;
use Filament\Schemas\Components\Concerns\CanBeCompact;
use Filament\Schemas\Components\Concerns\HasContainerGridLayout;
use Filament\Schemas\Components\Contracts\CanConcealComponents;
use Filament\Schemas\Components\Contracts\HasExtraItemActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Concerns\HasReorderAnimationDuration;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Str;
use LogicException;

use function Filament\Forms\array_move_after;
use function Filament\Forms\array_move_before;

class Repeater extends Field implements CanConcealComponents, HasExtraItemActions
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

    protected string | Closure | null $orderColumn = null;

    protected string | Closure | null $relationship = null;

    protected string | Closure | null $itemLabel = null;

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

    protected bool | Closure $shouldPartiallyRenderAfterActionsCalled = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultItems(1);

        $this->afterStateHydrated(static function (Repeater $component, ?array $rawState): void {
            if (
                is_array($component->hydratedDefaultState) &&
                $component->shouldMergeHydratedDefaultStateWithItemsStateAfterStateHydrated
            ) {
                $component->mergeHydratedDefaultStateWithItemsState();
            }

            if (is_array($component->hydratedDefaultState)) {
                return;
            }

            $items = [];

            $simpleField = $component->getSimpleField();

            foreach ($rawState ?? [] as $itemData) {
                if ($simpleField) {
                    $itemData = [$simpleField->getName() => $itemData];
                }

                if ($uuid = $component->generateUuid()) {
                    $items[$uuid] = $itemData;
                } else {
                    $items[] = $itemData;
                }
            }

            $component->rawState($items);
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
            if ($simpleField = $component->getSimpleField()) {
                return collect($state ?? [])
                    ->values()
                    ->pluck($simpleField->getName())
                    ->all();
            }

            return array_values($state ?? []);
        });
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

        return $items;
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

        $this->saveRelationshipsUsing(static function (Repeater $component, HasSchemas $livewire, ?array $state): void {
            if (! is_array($state)) {
                $state = [];
            }

            $relationship = $component->getRelationship();

            $existingRecords = $component->getCachedExistingRecords();

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
                    ->each(static fn (Model $record) => $record->delete());
            }

            $itemOrder = 1;
            $orderColumn = $component->getOrderColumn();

            $translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver();

            foreach ($component->getItems() as $itemKey => $item) {
                $itemData = $item->getState(shouldCallHooksBefore: false);

                if ($orderColumn) {
                    $itemData[$orderColumn] = $itemOrder;

                    $itemOrder++;
                }

                if ($record = ($existingRecords[$itemKey] ?? null)) {
                    $itemData = $component->mutateRelationshipDataBeforeSave($itemData, record: $record);

                    if ($itemData === null) {
                        continue;
                    }

                    $translatableContentDriver ?
                        $translatableContentDriver->updateRecord($record, $itemData) :
                        $record->fill($itemData)->save();

                    continue;
                }

                $relatedModel = $component->getRelatedModel();

                $itemData = $component->mutateRelationshipDataBeforeCreate($itemData);

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
                $existingRecords->push($record);
            }

            $component->getRecord()->setRelation($component->getRelationshipName(), $existingRecords);
        });

        $this->dehydrated(false);

        $this->reorderable(false);

        return $this;
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

    public function itemLabel(string | Closure | null $label): static
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

        if ($relationship instanceof BelongsToMany) {
            $relationshipQuery->select([
                $relationship->getTable() . '.*',
                $relationshipQuery->getModel()->getTable() . '.*',
            ]);
        }

        if ($this->modifyRelationshipQueryUsing) {
            $relationshipQuery = $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $relationshipQuery,
            ]) ?? $relationshipQuery;
        }

        if (filled($orderColumn)) {
            $relationshipQuery->orderBy($orderColumn);
        }

        return $this->cachedExistingRecords = $this->modifyRelationshipRecords($relationshipQuery->get()->mapWithKeys(
            fn (Model $item): array => ["record-{$item[$relatedKeyName]}" => $item],
        ));
    }

    public function getItemLabel(string $key): string | Htmlable | null
    {
        $container = $this->getChildSchema($key);

        return $this->evaluate($this->itemLabel, [
            'container' => $container,
            'item' => $container,
            'key' => $key,
            'schema' => $container,
            'state' => $container->getStateSnapshot(),
            'uuid' => $key,
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

    public function canConcealComponents(): bool
    {
        return $this->isCollapsible();
    }

    /**
     * @return view-string
     */
    public function getDefaultView(): string
    {
        if ($this->isTable()) {
            return 'filament-forms::components.repeater.table';
        }

        if ($this->isSimple()) {
            return 'filament-forms::components.repeater.simple';
        }

        return 'filament-forms::components.repeater.index';
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

    public function partiallyRenderAfterActionsCalled(bool | Closure $condition = true): static
    {
        $this->shouldPartiallyRenderAfterActionsCalled = $condition;

        return $this;
    }

    public function shouldPartiallyRenderAfterActionsCalled(): bool
    {
        return (bool) $this->evaluate($this->shouldPartiallyRenderAfterActionsCalled);
    }
}
