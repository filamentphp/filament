<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasLanguageAssistance
{
    protected bool | Closure | null $hasLanguageAssistance = null;

    public function hasLanguageAssistance(bool | Closure | null $hasLanguageAssistance = true): static
    {
        $this->hasLanguageAssistance = $hasLanguageAssistance;

        return $this;
    }

    public function getHasLanguageAssistance(): ?string
    {
        return $this->evaluate($this->hasLanguageAssistance);
    }
}
