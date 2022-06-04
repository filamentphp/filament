<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class TableBuilder extends Field
{
    use HasExtraAlpineAttributes;
    use Concerns\CanLimitItemsLength;

    protected string $view = 'forms::components.table-builder';

    protected bool | Closure $shouldDisableAddingRows = false;

    protected bool | Closure $shouldDisableDeletingRows = false;

    protected bool | Closure $shouldDisableAddingColumns = false;

    protected bool | Closure $shouldDisableDeletingColumns = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([[null]]);
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
}
