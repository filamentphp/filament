<?php

namespace Filament\Forms\Components;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Builder\Block;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Builder extends Field
{
    protected string $view = 'forms::components.builder';

    protected $createItemBetweenButtonLabel = null;

    protected $createItemButtonLabel = null;

    protected $isItemMovementDisabled = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerListeners([
            'builder::createItem' => [
                function (Builder $component, string $statePath, string $block, ?string $afterUuid = null): void {
                    if ($component->isDisabled()) {
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

                        foreach ($component->getNormalisedState() as $uuid => $item) {
                            $newItems[$uuid] = $item;

                            if ($uuid === $afterUuid) {
                                $newItems[$newUuid] = $newItem;
                            }
                        }

                        data_set($livewire, $statePath, $newItems);
                    } else {
                        data_set($livewire, "{$statePath}.{$newUuid}", $newItem);
                    }

                    $component->hydrateDefaultItemState($newUuid);
                },
            ],
            'builder::deleteItem' => [
                function (Builder $component, string $statePath, string $uuidToDelete): void {
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
            'builder::moveItemDown' => [
                function (Builder $component, string $statePath, string $uuidToMoveDown): void {
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
            'builder::moveItemUp' => [
                function (Builder $component, string $statePath, string $uuidToMoveUp): void {
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

        $this->createItemBetweenButtonLabel(__('forms::components.builder.buttons.create_item_between.label'));

        $this->createItemButtonLabel(function (Builder $component) {
            return __('forms::components.builder.buttons.create_item.label', [
                'label' => lcfirst($component->getLabel()),
            ]);
        });
    }

    public function blocks(array $blocks): static
    {
        $this->childComponents($blocks);

        return $this;
    }

    public function createItemBetweenButtonLabel(string | callable $label): static
    {
        $this->createItemBetweenButtonLabel = $label;

        return $this;
    }

    public function createItemButtonLabel(string | callable $label): static
    {
        $this->createItemButtonLabel = $label;

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

    public function getChildComponentContainers(): array
    {
        return collect($this->getNormalisedState())
            ->map(function ($itemData, $itemIndex): ComponentContainer {
                return $this->getBlock($itemData['type'])
                    ->getChildComponentContainer()
                    ->getClone()
                    ->statePath("{$itemIndex}.data");
            })->toArray();
    }

    public function getCreateItemBetweenButtonLabel(): string
    {
        return $this->evaluate($this->createItemBetweenButtonLabel);
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

        return array_filter(
            $state,
            fn ($item) => is_array($item) && $this->hasBlock($item['type'] ?? null),
        );
    }

    public function hasBlock($name): bool
    {
        return (bool) $this->getBlock($name);
    }

    public function isItemMovementDisabled(): bool
    {
        return $this->evaluate($this->isItemMovementDisabled);
    }
}
