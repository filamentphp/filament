<?php

namespace Filament\Forms\Components;

use Closure;
use function Filament\Forms\array_move_after;
use function Filament\Forms\array_move_before;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Contracts\HasForms;
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

    protected string | Closure | null $addButtonLabel = null;

    protected bool | Closure $isAddable = true;

    protected bool | Closure $isDeletable = true;

    protected bool | Closure $isReorderable = true;

    protected bool | Closure $isInset = false;

    protected ?Collection $cachedExistingRecords = null;

    protected string | Closure | null $orderColumn = null;

    protected string | Closure | null $relationship = null;

    protected string | Closure | null $itemLabel = null;

    protected ?Closure $modifyRelationshipQueryUsing = null;

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

        $this->registerListeners([
            'repeater::add' => [
                function (Repeater $component, string $statePath): void {
                    if (! $component->isAddable()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $newUuid = (string) Str::uuid();

                    $livewire = $component->getLivewire();
                    data_set($livewire, "{$statePath}.{$newUuid}", []);

                    $component->getChildComponentContainers()[$newUuid]->fill();

                    $component->collapsed(false, shouldMakeComponentCollapsible: false);
                },
            ],
            'repeater::delete' => [
                function (Repeater $component, string $statePath, string $uuidToDelete): void {
                    if (! $component->isDeletable()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = $component->getState();

                    unset($items[$uuidToDelete]);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
            'repeater::cloneItem' => [
                function (Repeater $component, string $statePath, string $uuidToDuplicate): void {
                    if (! $component->isCloneable()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $newUuid = (string) Str::uuid();

                    $livewire = $component->getLivewire();
                    data_set(
                        $livewire,
                        "{$statePath}.{$newUuid}",
                        data_get($livewire, "{$statePath}.{$uuidToDuplicate}"),
                    );

                    $component->collapsed(false, shouldMakeComponentCollapsible: false);
                },
            ],
            'repeater::moveItemDown' => [
                function (Repeater $component, string $statePath, string $uuidToMoveDown): void {
                    if (! $component->isReorderable()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = array_move_after($component->getState(), $uuidToMoveDown);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
            'repeater::moveUp' => [
                function (Repeater $component, string $statePath, string $uuidToMoveUp): void {
                    if (! $component->isReorderable()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = array_move_before($component->getState(), $uuidToMoveUp);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
            'repeater::reorder' => [
                function (Repeater $component, string $statePath, array $uuids): void {
                    if (! $component->isReorderable()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = array_merge(array_flip($uuids), $component->getState());

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
        ]);

        $this->addButtonLabel(static function (Repeater $component) {
            return __('filament-forms::components.repeater.buttons.add.label', [
                'label' => Str::lcfirst($component->getLabel()),
            ]);
        });

        $this->mutateDehydratedStateUsing(static function (?array $state): array {
            return array_values($state ?? []);
        });
    }

    public function addButtonLabel(string | Closure | null $label): static
    {
        $this->addButtonLabel = $label;

        return $this;
    }

    /**
     * @deprecated Use `addButtonLabel()` instead.
     */
    public function createItemButtonLabel(string | Closure | null $label): static
    {
        $this->addButtonLabel($label);

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
                ->getClone()
                ->statePath($itemKey)
                ->model($relationship ? $records[$itemKey] ?? $this->getRelatedModel() : null)
                ->inlineLabel(false);
        }

        return $containers;
    }

    public function getAddButtonLabel(): string
    {
        return $this->evaluate($this->addButtonLabel);
    }

    public function isReorderable(): bool
    {
        if ($this->isDisabled()) {
            return false;
        }

        return $this->evaluate($this->isReorderable);
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

    public function getLabel(): string
    {
        if ($this->label === null && $this->hasRelationship()) {
            return (string) str($this->getRelationshipName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
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
