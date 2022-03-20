<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasInlineLabel
{
    protected bool | Closure | null $hasInlineLabel = null;

    public function inlineLabel(bool | Closure | null $condition = true): static
    {
        $this->hasInlineLabel = $condition;

        return $this;
    }

    public function hasInlineLabel(): ?bool
    {
        return $this->evaluate($this->hasInlineLabel) ?? $this->getContainer()->hasInlineLabel();
    }
}
