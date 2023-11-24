<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeAccepted
{
    protected bool | Closure $mustBeAccepted = false;

    protected bool | Closure $mustBeDeclined = false;

    public function accepted(bool | Closure $condition = true): static
    {
        $this->mustBeAccepted = $condition;

        $this->rule('accepted', $condition);

        return $this;
    }

    public function declined(bool | Closure $condition = true): static
    {
        $this->mustBeDeclined = $condition;

        $this->rule('declined', $condition);

        return $this;
    }

    public function mustBeAccepted(): bool
    {
        return (bool) $this->evaluate($this->mustBeAccepted);
    }

    public function mustBeDeclined(): bool
    {
        return (bool) $this->evaluate($this->mustBeDeclined);
    }
}
