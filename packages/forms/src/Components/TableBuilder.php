<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class TableBuilder extends Field
{
    use HasExtraAlpineAttributes;
    use Concerns\CanLimitItemsLength;

    protected string $view = 'forms::components.table-builder';

    protected string | Closure $addColumnButtonLabel;

    protected string | Closure $addRowButtonLabel;

    protected int | Closure $defaultRows = 1;

    protected int | Closure $defaultColumns = 1;

    protected bool | Closure $canAddRows = false;

    protected bool | Closure $shouldDisableDeletingRows = false;

    protected bool | Closure $shouldDisableAddingColumns = false;

    protected bool | Closure $shouldDisableDeletingColumns = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->addRowButtonLabel(__('forms::components.table_builder.buttons.add_row.label'));
        $this->addColumnButtonLabel(__('forms::components.table_builder.buttons.add_column.label'));

        $this->default(function (TableBuilder $component) {
            $state = [];
            $defaultRows = $component->getDefaultRows();
            $defaultColumns = $component->getDefaultColumns();

            for ($row = 0; $row < $defaultRows; $row++) {
                $state[] = array_fill(0, $defaultColumns, null);
            }

            return $state;
        });
    }

    public function defaultRows(int | Closure $rows): static
    {
        $this->defaultRows = $rows;

        return $this;
    }

    public function defaultColumns(int | Closure $columns): static
    {
        $this->defaultColumns = $columns;

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

    public function getDefaultRows(): int
    {
        return $this->evaluate($this->defaultRows);
    }

    public function getDefaultColumns(): int
    {
        return $this->evaluate($this->defaultColumns);
    }

    public function canAddRows(): bool
    {
        return ! $this->evaluate($this->shouldDisableAddingRows);
    }

    public function canDeleteRows(): bool
    {
        return ! $this->evaluate($this->shouldDisableDeletingRows);
    }

    public function disableAddingColumns(bool | Closure $condition = true): static
    {
        $this->shouldDisableAddingColumns = $condition;

        return $this;
    }

    public function disableDeletingColumns(bool | Closure $condition = true): static
    {
        $this->shouldDisableDeletingColumns = $condition;

        return $this;
    }

    public function canAddColumns(): bool
    {
        return ! $this->evaluate($this->shouldDisableAddingColumns);
    }

    public function canDeleteColumns(): bool
    {
        return ! $this->evaluate($this->shouldDisableDeletingColumns);
    }

    public function addRowButtonLabel(string | Closure $label): static
    {
        $this->addRowButtonLabel = $label;

        return $this;
    }

    public function addColumnButtonLabel(string | Closure $label): static
    {
        $this->addColumnButtonLabel = $label;

        return $this;
    }

    public function getAddRowButtonLabel(): string
    {
        return $this->evaluate($this->addRowButtonLabel);
    }

    public function getAddColumnButtonLabel(): string
    {
        return $this->evaluate($this->addColumnButtonLabel);
    }
}
