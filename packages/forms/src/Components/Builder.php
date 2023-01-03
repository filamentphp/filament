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

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.builder';

    protected string | Closure | null $addBetweenButtonLabel = null;

    protected string | Closure | null $addButtonLabel = null;

    protected bool | Closure $isReorderable = true;

    protected bool | Closure $isAddable = true;

    protected bool | Closure $isDeletable = true;

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
            'builder::add' => [
                function (Builder $component, string $statePath, string $block, ?string $afterUuid = null): void {
                    if (! $component->isAddable()) {
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
            'builder::delete' => [
                function (Builder $component, string $statePath, string $uuidToDelete): void {
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
            'builder::cloneItem' => [
                function (Builder $component, string $statePath, string $uuidToDuplicate): void {
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
            'builder::moveItemDown' => [
                function (Builder $component, string $statePath, string $uuidToMoveDown): void {
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
            'builder::moveUp' => [
                function (Builder $component, string $statePath, string $uuidToMoveUp): void {
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
            'builder::reorder' => [
                function (Builder $component, string $statePath, array $uuids): void {
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

        $this->addBetweenButtonLabel(__('filament-forms::components.builder.buttons.add_between.label'));

        $this->addButtonLabel(static function (Builder $component) {
            return __('filament-forms::components.builder.buttons.add.label', [
                'label' => Str::lcfirst($component->getLabel()),
            ]);
        });

        $this->mutateDehydratedStateUsing(static function (?array $state): array {
            return array_values($state ?? []);
        });
    }

    /**
     * @param  array<Block>  $blocks
     */
    public function blocks(array $blocks): static
    {
        $this->childComponents($blocks);

        return $this;
    }

    public function addBetweenButtonLabel(string | Closure | null $label): static
    {
        $this->addBetweenButtonLabel = $label;

        return $this;
    }

    /**
     * @deprecated Use `addBetweenButtonLabel()` instead.
     */
    public function createItemBetweenButtonLabel(string | Closure | null $label): static
    {
        $this->addBetweenButtonLabel($label);

        return $this;
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

    public function getBlock(string $name): ?Block
    {
        return Arr::first(
            $this->getBlocks(),
            fn (Block $block): bool => $block->getName() === $name,
        );
    }

    /**
     * @return array<Component>
     */
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
                    ->getClone()
                    ->statePath("{$itemIndex}.data")
                    ->inlineLabel(false),
            )
            ->all();
    }

    public function getAddBetweenButtonLabel(): string
    {
        return $this->evaluate($this->addBetweenButtonLabel);
    }

    public function getAddButtonLabel(): string
    {
        return $this->evaluate($this->addButtonLabel);
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
