<?php

namespace Filament\Forms\Components;

use Closure;
use function Filament\Forms\array_move_after;
use function Filament\Forms\array_move_before;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Builder\Block;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Builder extends Field implements Contracts\CanConcealComponents
{
    use Concerns\CanBeCollapsed;
    use Concerns\CanLimitItemsLength;
    use Concerns\CanBeCloned;

    protected string $view = 'forms::components.builder';

    protected string | Closure | null $createItemBetweenButtonLabel = null;

    protected string | Closure | null $createItemButtonLabel = null;

    protected bool | Closure $isItemMovementDisabled = false;

    protected bool | Closure $isReorderableWithButtons = false;

    protected bool | Closure $isItemCreationDisabled = false;

    protected bool | Closure $isItemDeletionDisabled = false;

    protected bool | Closure $hasBlockLabels = true;

    protected bool | Closure $hasBlockNumbers = true;

    protected bool | Closure $isInset = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->afterStateHydrated(static function (Builder $component, ?array $state): void {
            $items = [];

            foreach ($state ?? [] as $itemData) {
                $items[(string) Str::uuid()] = $itemData;
            }

            $component->state($items);
        });

        $this->registerListeners([
            'builder::createItem' => [
                function (Builder $component, string $statePath, string $block, ?string $afterUuid = null): void {
                    if ($component->isItemCreationDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $livewire = $component->getLivewire();

                    $newUuid = (string) Str::uuid();
                    $newItem = [
                        'type' => $block,
                        'data' => [],
                    ];

                    if ($afterUuid) {
                        $newItems = [];

                        foreach ($component->getState() ?? [] as $uuid => $item) {
                            $newItems[$uuid] = $item;

                            if ($uuid === $afterUuid) {
                                $newItems[$newUuid] = $newItem;
                            }
                        }

                        data_set($livewire, $statePath, $newItems);
                    } else {
                        data_set($livewire, "{$statePath}.{$newUuid}", $newItem);
                    }

                    $component->getChildComponentContainers()[$newUuid]->fill();

                    $component->collapsed(false, shouldMakeComponentCollapsible: false);
                },
            ],
            'builder::deleteItem' => [
                function (Builder $component, string $statePath, string $uuidToDelete): void {
                    if ($component->isItemDeletionDisabled()) {
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
            'builder::cloneItem' => [
                function (Builder $component, string $statePath, string $uuidToDuplicate): void {
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
            'builder::moveItemDown' => [
                function (Builder $component, string $statePath, string $uuidToMoveDown): void {
                    if ($component->isItemMovementDisabled()) {
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
            'builder::moveItemUp' => [
                function (Builder $component, string $statePath, string $uuidToMoveUp): void {
                    if ($component->isItemMovementDisabled()) {
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
            'builder::moveItems' => [
                function (Builder $component, string $statePath, array $uuids): void {
                    if ($component->isItemMovementDisabled()) {
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

        $this->createItemBetweenButtonLabel(__('forms::components.builder.buttons.create_item_between.label'));

        $this->createItemButtonLabel(static function (Builder $component) {
            return __('forms::components.builder.buttons.create_item.label', [
                'label' => lcfirst($component->getLabel()),
            ]);
        });

        $this->mutateDehydratedStateUsing(static function (?array $state): array {
            return array_values($state ?? []);
        });
    }

    public function blocks(array | Closure $blocks): static
    {
        $this->childComponents($blocks);

        return $this;
    }

    public function createItemBetweenButtonLabel(string | Closure | null $label): static
    {
        $this->createItemBetweenButtonLabel = $label;

        return $this;
    }

    public function createItemButtonLabel(string | Closure | null $label): static
    {
        $this->createItemButtonLabel = $label;

        return $this;
    }

    public function disableItemMovement(bool | Closure $condition = true): static
    {
        $this->isItemMovementDisabled = $condition;

        return $this;
    }

    public function disableItemCreation(bool | Closure $condition = true): static
    {
        $this->isItemCreationDisabled = $condition;

        return $this;
    }

    public function disableItemDeletion(bool | Closure $condition = true): static
    {
        $this->isItemDeletionDisabled = $condition;

        return $this;
    }

    public function inset(bool | Closure $condition = true): static
    {
        $this->isInset = $condition;

        return $this;
    }

    public function reorderableWithButtons(bool | Closure $condition = true): static
    {
        $this->isReorderableWithButtons = $condition;

        return $this;
    }

    /**
     * @deprecated Use `withBlockLabels()` instead.
     */
    public function showBlockLabels(bool | Closure $condition = true): static
    {
        $this->withBlockLabels($condition);

        return $this;
    }

    public function withBlockLabels(bool | Closure $condition = true): static
    {
        $this->hasBlockLabels = $condition;

        return $this;
    }

    public function withBlockNumbers(bool | Closure $condition = true): static
    {
        $this->hasBlockNumbers = $condition;

        return $this;
    }

    public function getBlock($name): ?Block
    {
        return Arr::first(
            $this->getBlocks(),
            fn (Block $block) => $block->getName() === $name,
        );
    }

    public function getBlocks(): array
    {
        return $this->getChildComponentContainer()->getComponents();
    }

    public function getChildComponentContainers(bool $withHidden = false): array
    {
        return collect($this->getState())
            ->filter(fn (array $itemData): bool => $this->hasBlock($itemData['type']))
            ->map(
                fn (array $itemData, $itemIndex): ComponentContainer => $this
                    ->getBlock($itemData['type'])
                    ->getChildComponentContainer()
                    ->statePath("{$itemIndex}.data")
                    ->inlineLabel(false)
                    ->getClone(),
            )
            ->all();
    }

    public function getCreateItemBetweenButtonLabel(): string
    {
        return $this->evaluate($this->createItemBetweenButtonLabel);
    }

    public function getCreateItemButtonLabel(): string
    {
        return $this->evaluate($this->createItemButtonLabel);
    }

    public function hasBlock($name): bool
    {
        return (bool) $this->getBlock($name);
    }

    public function isReorderableWithButtons(): bool
    {
        return $this->evaluate($this->isReorderableWithButtons) && (! $this->isItemMovementDisabled());
    }

    public function isItemMovementDisabled(): bool
    {
        return $this->evaluate($this->isItemMovementDisabled) || $this->isDisabled();
    }

    public function isItemCreationDisabled(): bool
    {
        return $this->evaluate($this->isItemCreationDisabled) || $this->isDisabled() || (filled($this->getMaxItems()) && ($this->getMaxItems() <= $this->getItemsCount()));
    }

    public function isItemDeletionDisabled(): bool
    {
        return $this->evaluate($this->isItemDeletionDisabled) || $this->isDisabled();
    }

    public function hasBlockLabels(): bool
    {
        return (bool) $this->evaluate($this->hasBlockLabels);
    }

    public function hasBlockNumbers(): bool
    {
        return (bool) $this->evaluate($this->hasBlockNumbers);
    }

    public function isInset(): bool
    {
        return (bool) $this->evaluate($this->isInset);
    }

    public function canConcealComponents(): bool
    {
        return $this->isCollapsible();
    }
}
