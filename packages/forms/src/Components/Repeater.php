<?php

namespace Filament\Forms\Components;

use Closure;
use function Filament\Forms\array_move_after;
use function Filament\Forms\array_move_before;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Str;

class Repeater extends Field implements Contracts\CanConcealComponents
{
    use Concerns\CanBeCollapsed;
    use Concerns\CanLimitItemsLength;
    use Concerns\HasContainerGridLayout;
    use Concerns\CanBeCloned;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.repeater';

    protected string | Closure | null $addActionLabel = null;

    protected bool | Closure $isAddable = true;

    protected bool | Closure $isDeletable = true;

    protected bool | Closure $isReorderable = true;

    protected bool | Closure $isReorderableWithButtons = false;

    protected bool | Closure $isInset = false;

    protected ?Collection $cachedExistingRecords = null;

    protected string | Closure | null $orderColumn = null;

    protected string | Closure | null $relationship = null;

    protected string | Closure | null $itemLabel = null;

    protected ?Closure $modifyRelationshipQueryUsing = null;

    protected ?Closure $modifyAddActionUsing = null;

    protected ?Closure $modifyCloneActionUsing = null;

    protected ?Closure $modifyDeleteActionUsing = null;

    protected ?Closure $modifyMoveDownActionUsing = null;

    protected ?Closure $modifyMoveUpActionUsing = null;

    protected ?Closure $modifyReorderActionUsing = null;

    protected ?Closure $modifyCollapseActionUsing = null;

    protected ?Closure $modifyExpandActionUsing = null;

    protected ?Closure $modifyCollapseAllActionUsing = null;

    protected ?Closure $modifyExpandAllActionUsing = null;

    protected ?Closure $mutateRelationshipDataBeforeAddUsing = null;

    protected ?Closure $mutateRelationshipDataBeforeFillUsing = null;

    protected ?Closure $mutateRelationshipDataBeforeSaveUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultItems(1);

        $this->afterStateHydrated(static function (Repeater $component, ?array $state): void {
            $items = [];

            foreach ($state ?? [] as $itemData) {
                $items[(string) Str::uuid()] = $itemData;
            }

            $component->state($items);
        });

        $this->registerActions([
            fn (Repeater $component): ?Action => $component->getAddAction(),
            fn (Repeater $component): ?Action => $component->getCloneAction(),
            fn (Repeater $component): ?Action => $component->getCollapseAction(),
            fn (Repeater $component): ?Action => $component->getCollapseAllAction(),
            fn (Repeater $component): ?Action => $component->getDeleteAction(),
            fn (Repeater $component): ?Action => $component->getExpandAction(),
            fn (Repeater $component): ?Action => $component->getExpandAllAction(),
            fn (Repeater $component): ?Action => $component->getMoveDownAction(),
            fn (Repeater $component): ?Action => $component->getMoveUpAction(),
            fn (Repeater $component): ?Action => $component->getReorderAction(),
        ]);

        $this->mutateDehydratedStateUsing(static function (?array $state): array {
            return array_values($state ?? []);
        });
    }

    public function getAddAction(): ?Action
    {
        if (! $this->isAddable()) {
            return null;
        }

        $action = Action::make($this->getAddActionName())
            ->label(fn (Repeater $component) => $component->getAddActionLabel())
            ->action(function (Repeater $component): void {
                $newUuid = (string) Str::uuid();

                $items = $component->getState();
                $items[$newUuid] = [];

                $component->state($items);

                $component->getChildComponentContainers()[$newUuid]->fill();

                $component->collapsed(false, shouldMakeComponentCollapsible: false);
            })
            ->button()
            ->outlined()
            ->size('sm');

        if ($this->modifyAddActionUsing) {
            $action = $this->evaluate($this->modifyAddActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
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

    public function getCloneAction(): ?Action
    {
        if (! $this->isCloneable()) {
            return null;
        }

        $action = Action::make($this->getCloneActionName())
            ->label(__('filament-forms::components.repeater.actions.clone.label'))
            ->icon('heroicon-m-square-2-stack')
            ->color('gray')
            ->action(function (array $arguments, Repeater $component): void {
                $newUuid = (string) Str::uuid();

                $items = $component->getState();
                $items[$newUuid] = $items[$arguments['item']];

                $component->state($items);

                $component->collapsed(false, shouldMakeComponentCollapsible: false);
            })
            ->iconButton()
            ->inline()
            ->size('sm');

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

    public function getDeleteAction(): ?Action
    {
        if (! $this->isDeletable()) {
            return null;
        }

        $action = Action::make($this->getDeleteActionName())
            ->label(__('filament-forms::components.repeater.actions.delete.label'))
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->action(function (array $arguments, Repeater $component): void {
                $items = $component->getState();
                unset($items[$arguments['item']]);

                $component->state($items);
            })
            ->iconButton()
            ->inline()
            ->size('sm');

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

    public function getMoveDownAction(): ?Action
    {
        if (! $this->isReorderable()) {
            return null;
        }

        $action = Action::make($this->getMoveDownActionName())
            ->label(__('filament-forms::components.repeater.actions.move_down.label'))
            ->icon('heroicon-m-chevron-down')
            ->color('gray')
            ->action(function (array $arguments, Repeater $component): void {
                $items = array_move_after($component->getState(), $arguments['item']);

                $component->state($items);
            })
            ->iconButton()
            ->inline()
            ->size('sm');

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

    public function getMoveUpAction(): ?Action
    {
        if (! $this->isReorderable()) {
            return null;
        }

        $action = Action::make($this->getMoveUpActionName())
            ->label(__('filament-forms::components.repeater.actions.move_up.label'))
            ->icon('heroicon-m-chevron-up')
            ->color('gray')
            ->action(function (array $arguments, Repeater $component): void {
                $items = array_move_before($component->getState(), $arguments['item']);

                $component->state($items);
            })
            ->iconButton()
            ->inline()
            ->size('sm');

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

    public function getReorderAction(): ?Action
    {
        if (! $this->isReorderable()) {
            return null;
        }

        $action = Action::make($this->getReorderActionName())
            ->label(__('filament-forms::components.repeater.actions.reorder.label'))
            ->icon('heroicon-m-arrows-up-down')
            ->color('gray')
            ->action(function (array $arguments, Repeater $component): void {
                $items = array_merge(array_flip($arguments['items']), $component->getState());

                $component->state($items);
            })
            ->mountedOnClick(false)
            ->iconButton()
            ->inline()
            ->size('sm');

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

    public function getCollapseAction(): ?Action
    {
        $action = Action::make($this->getCollapseActionName())
            ->label(__('filament-forms::components.repeater.actions.collapse.label'))
            ->icon('heroicon-m-minus')
            ->color('gray')
            ->mountedOnClick(false)
            ->iconButton()
            ->inline()
            ->size('sm');

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

    public function getExpandAction(): ?Action
    {
        $action = Action::make($this->getExpandActionName())
            ->label(__('filament-forms::components.repeater.actions.expand.label'))
            ->icon('heroicon-m-plus')
            ->color('gray')
            ->mountedOnClick(false)
            ->iconButton()
            ->inline()
            ->size('sm');

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

    public function getCollapseAllAction(): ?Action
    {
        $action = Action::make($this->getCollapseAllActionName())
            ->label(__('filament-forms::components.repeater.actions.collapse_all.label'))
            ->mountedOnClick(false)
            ->link()
            ->size('sm');

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

    public function getExpandAllAction(): ?Action
    {
        $action = Action::make($this->getExpandAllActionName())
            ->label(__('filament-forms::components.repeater.actions.expand_all.label'))
            ->mountedOnClick(false)
            ->link()
            ->size('sm');

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

    public function defaultItems(int | Closure $count): static
    {
        $this->default(static function (Repeater $component) use ($count): array {
            $items = [];

            $count = $component->evaluate($count);

            if (! $count) {
                return $items;
            }

            foreach (range(1, $count) as $index) {
                $items[(string) Str::uuid()] = [];
            }

            return $items;
        });

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

    public function reorderableWithButtons(bool | Closure $condition = true): static
    {
        $this->isReorderableWithButtons = $condition;

        return $this;
    }

    public function inset(bool | Closure $condition = true): static
    {
        $this->isInset = $condition;

        return $this;
    }

    /**
     * @return array<ComponentContainer>
     */
    public function getChildComponentContainers(bool $withHidden = false): array
    {
        $relationship = $this->getRelationship();

        $records = $relationship ? $this->getCachedExistingRecords() : null;

        $containers = [];

        foreach ($this->getState() ?? [] as $itemKey => $itemData) {
            $containers[$itemKey] = $this
                ->getChildComponentContainer()
                ->statePath($itemKey)
                ->model($relationship ? $records[$itemKey] ?? $this->getRelatedModel() : null)
                ->inlineLabel(false)
                ->getClone();
        }

        return $containers;
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

        return $this->evaluate($this->isReorderable);
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

        return $this->evaluate($this->isAddable);
    }

    public function isDeletable(): bool
    {
        if ($this->isDisabled()) {
            return false;
        }

        return $this->evaluate($this->isDeletable);
    }

    public function isInset(): bool
    {
        return (bool) $this->evaluate($this->isInset);
    }

    public function orderable(string | Closure | null $column = 'sort'): static
    {
        $this->orderColumn = $column;
        $this->disableItemMovement(static fn (Repeater $component): bool => ! $component->evaluate($column));

        return $this;
    }

    public function relationship(string | Closure | null $name = null, ?Closure $callback = null): static
    {
        $this->relationship = $name ?? $this->getName();
        $this->modifyRelationshipQueryUsing = $callback;

        $this->afterStateHydrated(null);

        $this->loadStateFromRelationshipsUsing(static function (Repeater $component) {
            $component->clearCachedExistingRecords();

            $component->fillFromRelationship();
        });

        $this->saveRelationshipsUsing(static function (Repeater $component, HasForms $livewire, ?array $state) {
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
            }

            $relationship
                ->whereIn($relationship->getRelated()->getQualifiedKeyName(), $recordsToDelete)
                ->get()
                ->each(static fn (Model $record) => $record->delete());

            $childComponentContainers = $component->getChildComponentContainers();

            $itemOrder = 1;
            $orderColumn = $component->getOrderColumn();

            $activeLocale = $livewire->getActiveFormLocale();

            foreach ($childComponentContainers as $itemKey => $item) {
                $itemData = $item->getState(shouldCallHooksBefore: false);

                if ($orderColumn) {
                    $itemData[$orderColumn] = $itemOrder;

                    $itemOrder++;
                }

                if ($record = ($existingRecords[$itemKey] ?? null)) {
                    $activeLocale && method_exists($record, 'setLocale') && $record->setLocale($activeLocale);

                    $itemData = $component->mutateRelationshipDataBeforeSave($itemData, record: $record);

                    $record->fill($itemData)->save();

                    continue;
                }

                $relatedModel = $component->getRelatedModel();

                $record = new $relatedModel();

                if ($activeLocale && method_exists($record, 'setLocale')) {
                    $record->setLocale($activeLocale);
                }

                $itemData = $component->mutateRelationshipDataBeforeAdd($itemData);

                $record->fill($itemData);

                $record = $relationship->save($record);
                $item->model($record)->saveRelationships();
            }
        });

        $this->dehydrated(false);

        $this->disableItemMovement();

        return $this;
    }

    public function itemLabel(string | Closure | null $label): static
    {
        $this->itemLabel = $label;

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

        $activeLocale = $this->getLivewire()->getActiveFormLocale();

        return $records
            ->map(function (Model $record) use ($activeLocale): array {
                $state = $record->attributesToArray();

                if ($activeLocale && method_exists($record, 'getTranslatableAttributes') && method_exists($record, 'getTranslation')) {
                    foreach ($record->getTranslatableAttributes() as $attribute) {
                        $state[$attribute] = $record->getTranslation($attribute, $activeLocale);
                    }
                }

                return $this->mutateRelationshipDataBeforeFill($state);
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

        return $this->getModelInstance()->{$this->getRelationshipName()}();
    }

    public function getRelationshipName(): ?string
    {
        return $this->evaluate($this->relationship);
    }

    public function getCachedExistingRecords(): Collection
    {
        if ($this->cachedExistingRecords) {
            return $this->cachedExistingRecords;
        }

        $relationship = $this->getRelationship();
        $relationshipQuery = $relationship->getQuery();

        if ($this->modifyRelationshipQueryUsing) {
            $relationshipQuery = $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $relationshipQuery,
            ]) ?? $relationshipQuery;
        }

        if ($orderColumn = $this->getOrderColumn()) {
            $relationshipQuery->orderBy($orderColumn);
        }

        $relatedKeyName = $relationship->getRelated()->getKeyName();

        return $this->cachedExistingRecords = $relationshipQuery->get()->mapWithKeys(
            fn (Model $item): array => ["record-{$item[$relatedKeyName]}" => $item],
        );
    }

    public function getItemLabel(string $uuid): ?string
    {
        return $this->evaluate($this->itemLabel, [
            'state' => $this->getChildComponentContainer($uuid)->getRawState(),
            'uuid' => $uuid,
        ]);
    }

    public function hasItemLabels(): bool
    {
        return $this->itemLabel !== null;
    }

    public function clearCachedExistingRecords(): void
    {
        $this->cachedExistingRecords = null;
    }

    public function getRelatedModel(): string
    {
        return $this->getRelationship()->getModel()::class;
    }

    public function hasRelationship(): bool
    {
        return filled($this->getRelationshipName());
    }

    public function mutateRelationshipDataBeforeAddUsing(?Closure $callback): static
    {
        $this->mutateRelationshipDataBeforeAddUsing = $callback;

        return $this;
    }

    /**
     * @param  array<array<string, mixed>>  $data
     * @return array<array<string, mixed>>
     */
    public function mutateRelationshipDataBeforeAdd(array $data): array
    {
        if ($this->mutateRelationshipDataBeforeAddUsing instanceof Closure) {
            $data = $this->evaluate($this->mutateRelationshipDataBeforeAddUsing, [
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
     * @return array<array<string, mixed>>
     */
    public function mutateRelationshipDataBeforeSave(array $data, Model $record): array
    {
        if ($this->mutateRelationshipDataBeforeSaveUsing instanceof Closure) {
            $data = $this->evaluate($this->mutateRelationshipDataBeforeSaveUsing, [
                'data' => $data,
                'record' => $record,
            ]);
        }

        return $data;
    }

    public function canConcealComponents(): bool
    {
        return $this->isCollapsible();
    }
}
