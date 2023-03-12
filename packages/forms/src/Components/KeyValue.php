<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class KeyValue extends Field
{
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.key-value';

    protected string | Closure | null $addActionLabel = null;

    protected string | Closure | null $deleteActionLabel = null;

    protected string | Closure | null $reorderActionLabel = null;

    protected bool | Closure $isAddable = true;

    protected bool | Closure $isDeletable = true;

    protected bool | Closure $canEditKeys = true;

    protected bool | Closure $canEditValues = true;

    protected string | Closure | null $keyLabel = null;

    protected string | Closure | null $valueLabel = null;

    protected string | Closure | null $keyPlaceholder = null;

    protected string | Closure | null $valuePlaceholder = null;

    protected bool | Closure $isReorderable = false;

    protected ?Closure $modifyAddActionUsing = null;

    protected ?Closure $modifyDeleteActionUsing = null;

    protected ?Closure $modifyReorderActionUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->dehydrateStateUsing(static function (?array $state) {
            return collect($state ?? [])
                ->filter(static fn (?string $value, ?string $key): bool => filled($key))
                ->map(static fn (?string $value): ?string => filled($value) ? $value : null)
                ->all();
        });

        $this->registerActions([
            fn (KeyValue $component): ?Action => $component->getAddAction(),
            fn (KeyValue $component): ?Action => $component->getDeleteAction(),
            fn (KeyValue $component): ?Action => $component->getReorderAction(),
        ]);
    }

    public function getAddAction(): ?Action
    {
        if (! $this->isAddable()) {
            return null;
        }

        $action = Action::make($this->getAddActionName())
            ->label(fn (KeyValue $component) => $component->getAddActionLabel())
            ->mountedOnClick(false)
            ->link()
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

    public function getDeleteAction(): ?Action
    {
        if (! $this->isDeletable()) {
            return null;
        }

        $action = Action::make($this->getDeleteActionName())
            ->label(__('filament-forms::components.key_value.actions.delete.label'))
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->mountedOnClick(false)
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

    public function getReorderAction(): ?Action
    {
        if (! $this->isReorderable()) {
            return null;
        }

        $action = Action::make($this->getReorderActionName())
            ->label(__('filament-forms::components.key_value.actions.reorder.label'))
            ->icon('heroicon-m-arrows-up-down')
            ->color('gray')
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

    public function addActionLabel(string | Closure | null $label): static
    {
        $this->addActionLabel = $label;

        return $this;
    }

    public function deleteActionLabel(string | Closure | null $label): static
    {
        $this->deleteActionLabel = $label;

        return $this;
    }

    public function reorderActionLabel(string | Closure | null $label): static
    {
        $this->reorderActionLabel = $label;

        return $this;
    }

    /**
     * @deprecated Use `addActionLabel()` instead.
     */
    public function addButtonLabel(string | Closure | null $label): static
    {
        $this->addActionLabel($label);

        return $this;
    }

    /**
     * @deprecated Use `deleteActionLabel()` instead.
     */
    public function deleteButtonLabel(string | Closure | null $label): static
    {
        $this->deleteActionLabel($label);

        return $this;
    }

    /**
     * @deprecated Use `reorderActionLabel()` instead.
     */
    public function reorderButtonLabel(string | Closure | null $label): static
    {
        $this->reorderActionLabel($label);

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

    public function editableKeys(bool | Closure $condition = true): static
    {
        $this->canEditKeys = $condition;

        return $this;
    }

    public function editableValues(bool | Closure $condition = true): static
    {
        $this->canEditValues = $condition;

        return $this;
    }

    /**
     * @deprecated Use `addable()` instead.
     */
    public function disableAddingRows(bool | Closure $condition = true): static
    {
        $this->addable(fn (KeyValue $component): bool => ! $component->evaluate($condition));

        return $this;
    }

    /**
     * @deprecated Use `deletable()` instead.
     */
    public function disableDeletingRows(bool | Closure $condition = true): static
    {
        $this->deletable(fn (KeyValue $component): bool => ! $component->evaluate($condition));

        return $this;
    }

    /**
     * @deprecated Use `editableKeys()` instead.
     */
    public function disableEditingKeys(bool | Closure $condition = true): static
    {
        $this->editableKeys(fn (KeyValue $component): bool => ! $component->evaluate($condition));

        return $this;
    }

    /**
     * @deprecated Use `editableValues()` instead.
     */
    public function disableEditingValues(bool | Closure $condition = true): static
    {
        $this->editableValues(fn (KeyValue $component): bool => ! $component->evaluate($condition));

        return $this;
    }

    public function keyLabel(string | Closure | null $label): static
    {
        $this->keyLabel = $label;

        return $this;
    }

    public function valueLabel(string | Closure | null $label): static
    {
        $this->valueLabel = $label;

        return $this;
    }

    public function keyPlaceholder(string | Closure | null $placeholder): static
    {
        $this->keyPlaceholder = $placeholder;

        return $this;
    }

    public function valuePlaceholder(string | Closure | null $placeholder): static
    {
        $this->valuePlaceholder = $placeholder;

        return $this;
    }

    public function reorderable(bool | Closure $condition = true): static
    {
        $this->isReorderable = $condition;

        return $this;
    }

    public function isAddable(): bool
    {
        return (bool) $this->evaluate($this->isAddable);
    }

    public function isDeletable(): bool
    {
        return (bool) $this->evaluate($this->isDeletable);
    }

    public function canEditKeys(): bool
    {
        return (bool) $this->evaluate($this->canEditKeys);
    }

    public function canEditValues(): bool
    {
        return (bool) $this->evaluate($this->canEditValues);
    }

    public function getAddActionLabel(): string
    {
        return $this->evaluate($this->addActionLabel) ?? __('filament-forms::components.key_value.actions.add.label');
    }

    public function getDeleteActionLabel(): string
    {
        return $this->evaluate($this->deleteActionLabel) ?? __('filament-forms::components.key_value.actions.delete.label');
    }

    public function getReorderActionLabel(): string
    {
        return $this->evaluate($this->reorderActionLabel) ?? __('filament-forms::components.key_value.actions.reorder.label');
    }

    public function getKeyLabel(): string
    {
        return $this->evaluate($this->keyLabel) ?? __('filament-forms::components.key_value.fields.key.label');
    }

    public function getValueLabel(): string
    {
        return $this->evaluate($this->valueLabel) ?? __('filament-forms::components.key_value.fields.value.label');
    }

    public function getKeyPlaceholder(): ?string
    {
        return $this->evaluate($this->keyPlaceholder);
    }

    public function getValuePlaceholder(): ?string
    {
        return $this->evaluate($this->valuePlaceholder);
    }

    public function isReorderable(): bool
    {
        return (bool) $this->evaluate($this->isReorderable);
    }
}
