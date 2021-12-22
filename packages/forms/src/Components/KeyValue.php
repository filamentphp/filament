<?php

namespace Filament\Forms\Components;

class KeyValue extends Field
{
    use Concerns\HasExtraAlpineAttributes;

    protected string $view = 'forms::components.key-value';

    protected $addButtonLabel = null;

    protected $shouldDisableAddingRows = false;

    protected $shouldDisableDeletingRows = false;

    protected $shouldDisableEditingKeys = false;

    protected $deleteButtonLabel = null;

    protected $keyLabel = null;

    protected $valueLabel = null;

    protected $keyPlaceholder = null;

    protected $valuePlaceholder = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->addButtonLabel(__('forms::components.key_value.buttons.add.label'));

        $this->deleteButtonLabel(__('forms::components.key_value.buttons.delete.label'));

        $this->keyLabel(__('forms::components.key_value.fields.key.label'));

        $this->valueLabel(__('forms::components.key_value.fields.value.label'));
    }

    public function addButtonLabel(string | callable $label): static
    {
        $this->addButtonLabel = $label;

        return $this;
    }

    public function deleteButtonLabel(string | callable $label): static
    {
        $this->deleteButtonLabel = $label;

        return $this;
    }

    public function disableAddingRows(bool | callable $condition = true): static
    {
        $this->shouldDisableAddingRows = $condition;

        return $this;
    }

    public function disableDeletingRows(bool | callable $condition = true): static
    {
        $this->shouldDisableDeletingRows = $condition;

        return $this;
    }

    public function disableEditingKeys(bool | callable $condition = true): static
    {
        $this->shouldDisableEditingKeys = $condition;

        return $this;
    }

    public function keyLabel(string | callable $label): static
    {
        $this->keyLabel = $label;

        return $this;
    }

    public function valueLabel(string | callable $label): static
    {
        $this->valueLabel = $label;

        return $this;
    }

    public function keyPlaceholder(string | callable | null $placeholder): static
    {
        $this->keyPlaceholder = $placeholder;

        return $this;
    }

    public function valuePlaceholder(string | callable | null $placeholder): static
    {
        $this->valuePlaceholder = $placeholder;

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
}
