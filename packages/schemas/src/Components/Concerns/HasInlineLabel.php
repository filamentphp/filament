<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;

trait HasInlineLabel
{
    protected bool | Closure | null $hasInlineLabel = null;

    protected bool | Closure | null $labelCanGrow = null;

    public function inlineLabel(bool | Closure | null $condition = true, bool $labelCanGrow = false): static
    {
        $this->hasInlineLabel = $condition;
        $this->labelCanGrow = $labelCanGrow;

        return $this;
    }

    public function hasInlineLabel(): ?bool
    {
        return $this->evaluate($this->hasInlineLabel) ?? $this->getContainer()->hasInlineLabel();
    }

    public function labelCanGrow(): ?bool
    {
        return $this->evaluate($this->labelCanGrow) ?? $this->getContainer()->labelCanGrow();
    }
}
