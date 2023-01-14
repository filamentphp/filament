<?php

namespace Filament\Infolists\Components;

use Closure;

class ListEntry extends Entry
{
    use Concerns\CanFormatState;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.list-entry';

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
