<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class TableBuilder extends Field
{
    use HasExtraAlpineAttributes;

    protected string $view = 'forms::components.table-builder';

    protected bool | Closure $shouldDisableAddingRows = false;

    protected bool | Closure $shouldDisableDeletingRows = false;

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
}
