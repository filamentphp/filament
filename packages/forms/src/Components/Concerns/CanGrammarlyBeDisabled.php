<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanGrammarlyBeDisabled
{
    protected bool | Closure $isGrammarlyDisabled = false;

    public function disableGrammarly(bool | Closure $condition = true): static
    {
        $this->isGrammarlyDisabled = $condition;

        return $this;
    }

    public function isGrammarlyDisabled(): bool
    {
        return (bool) $this->evaluate($this->isGrammarlyDisabled);
    }
}
