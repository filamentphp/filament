<?php

namespace Filament\Forms\Components;

use Closure;
use function Filament\Forms\array_move_after;
use function Filament\Forms\array_move_before;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
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

    protected string | Closure | null $addBetweenActionLabel = null;

    protected string | Closure | null $addActionLabel = null;

    protected bool | Closure $isReorderable = true;

    protected bool | Closure $isReorderableWithButtons = false;

    protected bool | Closure $isAddable = true;

    protected bool | Closure $isDeletable = true;

    protected bool | Closure $hasBlockLabels = true;

    protected bool | Closure $hasBlockNumbers = true;

    protected bool | Closure $isInset = false;

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

        $this->registerActions([
            fn (Builder $component): ?Action => $component->getAddAction(),
            fn (Builder $component): ?Action => $component->getAddBetweenAction(),
            fn (Builder $component): ?Action => $component->getCloneAction(),
            fn (Builder $component): ?Action => $component->getCollapseAction(),
            fn (Builder $component): ?Action => $component->getCollapseAllAction(),
            fn (Builder $component): ?Action => $component->getDeleteAction(),
            fn (Builder $component): ?Action => $component->getExpandAction(),
            fn (Builder $component): ?Action => $component->getExpandAllAction(),
            fn (Builder $component): ?Action => $component->getMoveDownAction(),
            fn (Builder $component): ?Action => $component->getMoveUpAction(),
            fn (Builder $component): ?Action => $component->getReorderAction(),
        ]);

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

    public function getAddAction(): ?Action
    {
        if (! $this->isAddable()) {
            return null;
        }

        $action = Action::make($this->getAddActionName())
            ->label(fn (Builder $component) => $component->getAddActionLabel())
            ->action(function (array $arguments, Builder $component): void {
                $newUuid = (string) Str::uuid();

                $items = $component->getState();
                $items[$newUuid] = [
                    'type' => $arguments['block'],
                    'data' => [],
                ];

                $component->state($items);

                $component->getChildComponentContainers()[$newUuid]->fill();

                $component->collapsed(false, shouldMakeComponentCollapsible: false);
            })
            ->mountedOnClick(false)
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

    public function getAddBetweenAction(): ?Action
    {
        if (! $this->isAddable()) {
            return null;
        }

        $action = Action::make($this->getAddBetweenActionName())
            ->label(fn (Builder $component) => $component->getAddBetweenActionLabel())
            ->icon('heroicon-m-plus')
            ->action(function (array $arguments, Builder $component): void {
                $newUuid = (string) Str::uuid();

                $items = [];

                foreach ($component->getState() ?? [] as $uuid => $item) {
                    $items[$uuid] = $item;

                    if ($uuid === $arguments['afterItem']) {
                        $items[$newUuid] = [
                            'type' => $arguments['block'],
                            'data' => [],
                        ];
                    }
                }

                $component->state($items);

                $component->getChildComponentContainers()[$newUuid]->fill();

                $component->collapsed(false, shouldMakeComponentCollapsible: false);
            })
            ->mountedOnClick(false)
            ->iconButton()
            ->size('sm');

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

    public function getCloneAction(): ?Action
    {
        if (! $this->isCloneable()) {
            return null;
        }

        $action = Action::make($this->getCloneActionName())
            ->label(__('filament-forms::components.builder.actions.clone.label'))
            ->icon('heroicon-m-square-2-stack')
            ->color('gray')
            ->action(function (array $arguments, Builder $component): void {
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
            ->label(__('filament-forms::components.builder.actions.delete.label'))
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->action(function (array $arguments, Builder $component): void {
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
            ->label(__('filament-forms::components.builder.actions.move_down.label'))
            ->icon('heroicon-m-chevron-down')
            ->color('gray')
            ->action(function (array $arguments, Builder $component): void {
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
            ->label(__('filament-forms::components.builder.actions.move_up.label'))
            ->icon('heroicon-m-chevron-up')
            ->color('gray')
            ->action(function (array $arguments, Builder $component): void {
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
            ->label(__('filament-forms::components.builder.actions.reorder.label'))
            ->icon('heroicon-m-arrows-up-down')
            ->color('gray')
            ->action(function (array $arguments, Builder $component): void {
                $items = array_merge(array_flip($arguments['items']), $component->getState());

                $component->state($items);
            })
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
            ->label(__('filament-forms::components.builder.actions.collapse.label'))
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
            ->label(__('filament-forms::components.builder.actions.expand.label'))
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
            ->label(__('filament-forms::components.builder.actions.collapse_all.label'))
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
            ->label(__('filament-forms::components.builder.actions.expand_all.label'))
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
                    ->statePath("{$itemIndex}.data")
                    ->inlineLabel(false)
                    ->getClone(),
            )
            ->all();
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
