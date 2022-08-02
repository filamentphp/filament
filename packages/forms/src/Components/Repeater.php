<?php

namespace Filament\Forms\Components;

use Closure;

use function Filament\Forms\array_move_after;
use function Filament\Forms\array_move_before;

use Filament\Forms\ComponentContainer;
use Illuminate\Support\Str;

class Repeater extends Field
{
    use Concerns\CanBeCollapsed;
    use Concerns\CanLimitItemsLength;
    use Concerns\HasContainerGridLayout;

    protected string $view = 'forms::components.repeater';

    protected string | Closure | null $createItemButtonLabel = null;

    protected bool | Closure $isItemCreationDisabled = false;

    protected bool | Closure $isItemDeletionDisabled = false;

    protected bool | Closure $isItemMovementDisabled = false;

    protected bool | Closure $isInset = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultItems(1);

        $this->afterStateHydrated(static function (Repeater $component, ?array $state): void {
            $items = collect($state ?? [])
                ->mapWithKeys(static fn ($itemData) => [(string) Str::uuid() => $itemData])
                ->toArray();

            $component->state($items);
        });

        $this->registerListeners([
            'repeater::createItem' => [
                function (Repeater $component, string $statePath): void {
                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $newUuid = (string) Str::uuid();

                    $livewire = $component->getLivewire();
                    data_set($livewire, "{$statePath}.{$newUuid}", []);

                    $component->getChildComponentContainers()[$newUuid]->fill();

                    $component->hydrateDefaultItemState($newUuid);

                    $component->collapsed(false, shouldMakeComponentCollapsible: false);
                },
            ],
            'repeater::deleteItem' => [
                function (Repeater $component, string $statePath, string $uuidToDelete): void {
                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = $component->getState();

                    unset($items[$uuidToDelete]);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
            'repeater::moveItemDown' => [
                function (Repeater $component, string $statePath, string $uuidToMoveDown): void {
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
            'repeater::moveItemUp' => [
                function (Repeater $component, string $statePath, string $uuidToMoveUp): void {
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
            'repeater::moveItems' => [
                function (Repeater $component, string $statePath, array $uuids): void {
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

        $this->createItemButtonLabel(static function (Repeater $component) {
            return __('forms::components.repeater.buttons.create_item.label', [
                'label' => lcfirst($component->getLabel()),
            ]);
        });

        $this->mutateDehydratedStateUsing(static function (?array $state): array {
            return array_values($state ?? []);
        });
    }

    public function createItemButtonLabel(string | Closure | null $label): static
    {
        $this->createItemButtonLabel = $label;

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
                $items[] = [];
            }

            return $items;
        });

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

    public function disableItemMovement(bool | Closure $condition = true): static
    {
        $this->isItemMovementDisabled = $condition;

        return $this;
    }

    public function inset(bool | Closure $condition = true): static
    {
        $this->isInset = $condition;

        return $this;
    }

    public function hydrateDefaultItemState(string $uuid): void
    {
        $this->getChildComponentContainers()[$uuid]->hydrateDefaultState();
    }

    public function getChildComponentContainers(bool $withHidden = false): array
    {
        return collect($this->getState())
            ->map(function ($itemData, $itemIndex): ComponentContainer {
                return $this
                    ->getChildComponentContainer()
                    ->getClone()
                    ->statePath($itemIndex)
                    ->inlineLabel(false);
            })
            ->toArray();
    }

    public function getCreateItemButtonLabel(): string
    {
        return $this->evaluate($this->createItemButtonLabel);
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

    public function isInset(): bool
    {
        return (bool) $this->evaluate($this->isInset);
    }
}
