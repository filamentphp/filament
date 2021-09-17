<?php

namespace Filament\Forms\Components;

use Filament\Forms\ComponentContainer;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Repeater extends Field
{
    protected string $view = 'forms::components.repeater';

    protected $createItemButtonLabel = null;

    protected $defaultItems = 1;

    protected $isItemMovementDisabled = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerListeners([
            'repeater.createItem' => [
                function (Repeater $component, string $statePath): void {
                    if ($component->isDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $newUuid = (string) Str::uuid();

                    $livewire = $component->getLivewire();
                    data_set($livewire, "{$statePath}.{$newUuid}", []);

                    $component->hydrateDefaultItemState($newUuid);
                },
            ],
            'repeater.deleteItem' => [
                function (Repeater $component, string $statePath, string $uuidToDelete): void {
                    if ($component->isDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = $component->getNormalisedState();

                    unset($items[$uuidToDelete]);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
            'repeater.moveItemDown' => [
                function (Repeater $component, string $statePath, string $uuidToMoveDown): void {
                    if ($component->isDisabled()) {
                        return;
                    }

                    if ($component->isItemMovementDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = Arr::moveElementAfter($component->getNormalisedState(), $uuidToMoveDown);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
            'repeater.moveItemUp' => [
                function (Repeater $component, string $statePath, string $uuidToMoveUp): void {
                    if ($component->isDisabled()) {
                        return;
                    }

                    if ($component->isItemMovementDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = Arr::moveElementBefore($component->getNormalisedState(), $uuidToMoveUp);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
        ]);

        $this->createItemButtonLabel(function (Repeater $component) {
            return __('forms::components.repeater.buttons.create_item.label', [
                'label' => lcfirst($component->getLabel()),
            ]);
        });
    }

    public function createItemButtonLabel(string | callable $label): static
    {
        $this->createItemButtonLabel = $label;

        return $this;
    }

    public function defaultItems(int | callable $count): static
    {
        $this->default(function (Repeater $component) use ($count): array {
            $items = [];

            foreach (range(1, $component->evaluate($count)) as $index) {
                $items[(string) Str::uuid()] = [];
            }

            return $items;
        });

        return $this;
    }

    public function disableItemMovement(bool | callable $condition = true): static
    {
        $this->isItemMovementDisabled = $condition;

        return $this;
    }

    public function hydrateDefaultItemState(string $uuid): void
    {
        $this->getChildComponentContainers()[$uuid]->hydrateDefaultState();
    }

    public function getChildComponentContainers(): array
    {
        return collect($this->getNormalisedState())
            ->map(function ($item, $index): ComponentContainer {
                return $this
                    ->getChildComponentContainer()
                    ->getClone()
                    ->statePath($index);
            })->toArray();
    }

    public function getCreateItemButtonLabel(): string
    {
        return $this->evaluate($this->createItemButtonLabel);
    }

    public function getNormalisedState(): array
    {
        if (! is_array($state = $this->getState())) {
            return [];
        }

        return array_filter($state, fn ($item) => is_array($item));
    }

    public function isItemMovementDisabled(): bool
    {
        return $this->evaluate($this->isItemMovementDisabled);
    }
}
