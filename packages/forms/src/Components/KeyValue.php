<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class KeyValue extends Field
{
    use HasExtraAlpineAttributes;

    protected string $view = 'forms::components.key-value';

    protected string | Closure | null $addButtonLabel = null;

    protected bool | Closure $shouldDisableAddingRows = false;

    protected bool | Closure $shouldDisableDeletingRows = false;

    protected bool | Closure $shouldDisableEditingKeys = false;

    protected bool | Closure $shouldDisableEditingValues = false;

    protected string | Closure | null $deleteButtonLabel = null;

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

        $this->addButtonLabel(__('forms::components.key_value.buttons.add.label'));

        $this->deleteButtonLabel(__('forms::components.key_value.buttons.delete.label'));

        $this->keyLabel(__('forms::components.key_value.fields.key.label'));

        $this->valueLabel(__('forms::components.key_value.fields.value.label'));
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

    public function disableAddingRows(bool | Closure $condition = true): static
    {
        $this->shouldDisableAddingRows = $condition;

        return $this;
    }

    public function disableDeletingRows(bool | Closure $condition = true): static
    {
        $this->shouldDisableDeletingRows = $condition;

        return $this;
    }

    public function disableEditingKeys(bool | Closure $condition = true): static
    {
        $this->shouldDisableEditingKeys = $condition;

        return $this;
    }

    public function disableEditingValues(bool | Closure $condition = true): static
    {
        $this->shouldDisableEditingValues = $condition;

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

    public function canAddRows(): bool
    {
        return ! $this->evaluate($this->shouldDisableAddingRows);
    }

    public function canDeleteRows(): bool
    {
        return ! $this->evaluate($this->shouldDisableDeletingRows);
    }

    public function canEditKeys(): bool
    {
        return ! $this->evaluate($this->shouldDisableEditingKeys);
    }

    public function canEditValues(): bool
    {
        return ! $this->evaluate($this->shouldDisableEditingValues);
    }

    public function getAddButtonLabel(): string
    {
        return $this->evaluate($this->addButtonLabel);
    }

    public function getDeleteButtonLabel(): string
    {
        return $this->evaluate($this->deleteButtonLabel);
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
        return $this->evaluate($this->isReorderable);
    }
}
