<?php

namespace Filament\Schemas\Concerns;

use Closure;

trait HasInlineLabels
{
    protected bool | Closure | null $hasInlineLabels = null;

    protected bool | Closure | null $labelCanGrow = null;

    public function inlineLabel(bool | Closure | null $condition = true, bool $labelCanGrow = false): static
    {
        $this->hasInlineLabels = $condition;
        $this->labelCanGrow = $labelCanGrow;

        return $this;
    }

    public function hasInlineLabel(): ?bool
    {
        return $this->evaluate($this->hasInlineLabels) ?? $this->getParentComponent()?->hasInlineLabel();
    }

    public function labelCanGrow(): ?bool
    {
        return $this->evaluate($this->labelCanGrow) ?? $this->getParentComponent()?->labelCanGrow();
    }
}
