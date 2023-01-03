<?php

namespace Filament\Tables\Columns;

use Closure;

class ListColumn extends Column
{
    use Concerns\CanFormatState;

    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.list-column';

    protected bool | Closure $isBulleted = true;

    protected mixed $defaultState = [];

    public function bulleted(bool | Closure $condition = true): static
    {
        $this->isBulleted = $condition;

        return $this;
    }

    public function isBulleted(): bool
    {
        return (bool) $this->evaluate($this->isBulleted);
    }
}
