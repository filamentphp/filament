<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class KeyValue extends Field
{
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.key-value';

    protected string | Closure | null $addButtonLabel = null;

    protected string | Closure | null $deleteButtonLabel = null;

    protected string | Closure | null $reorderButtonLabel = null;

    protected bool | Closure $isAddable = true;

    protected bool | Closure $isDeletable = true;

    protected bool | Closure $canEditKeys = true;

    protected bool | Closure $canEditValues = true;

    protected string | Closure | null $keyLabel = null;

    protected string | Closure | null $valueLabel = null;

    protected string | Closure | null $keyPlaceholder = null;

    protected string | Closure | null $valuePlaceholder = null;

    protected bool | Closure $isReorderable = false;

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

        $this->addButtonLabel(__('filament-forms::components.key_value.buttons.add.label'));

        $this->deleteButtonLabel(__('filament-forms::components.key_value.buttons.delete.label'));

        $this->reorderButtonLabel(__('filament-forms::components.key_value.buttons.reorder.label'));

        $this->keyLabel(__('filament-forms::components.key_value.fields.key.label'));

        $this->valueLabel(__('filament-forms::components.key_value.fields.value.label'));
    }

    public function addButtonLabel(string | Closure | null $label): static
    {
        $this->addButtonLabel = $label;

        return $this;
    }

    public function deleteButtonLabel(string | Closure | null $label): static
    {
        $this->deleteButtonLabel = $label;

        return $this;
    }

    public function reorderButtonLabel(string | Closure | null $label): static
    {
        $this->reorderButtonLabel = $label;

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

    public function getAddButtonLabel(): string
    {
        return $this->evaluate($this->addButtonLabel);
    }

    public function getDeleteButtonLabel(): string
    {
        return $this->evaluate($this->deleteButtonLabel);
    }

    public function getReorderButtonLabel(): string
    {
        return $this->evaluate($this->reorderButtonLabel);
    }

    public function getKeyLabel(): string
    {
        return $this->evaluate($this->keyLabel);
    }

    public function getValueLabel(): string
    {
        return $this->evaluate($this->valueLabel);
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
