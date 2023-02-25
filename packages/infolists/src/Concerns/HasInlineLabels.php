<?php

namespace Filament\Infolists\Concerns;

use Closure;

trait HasInlineLabels
{
    protected bool | Closure | null $hasInlineLabels = null;

    public function inlineLabel(bool | Closure | null $condition = true): static
    {
        $this->hasInlineLabels = $condition;

        return $this;
    }

    public function hasInlineLabel(): ?bool
    {
        return $this->evaluate($this->hasInlineLabels) ?? $this->getParentComponent()?->hasInlineLabel();
    }
}
