<?php

namespace Filament\Schemas\Concerns;

use Closure;
use Filament\Schemas\Schema;

trait HasGap
{
    protected bool | Closure | null $hasGap = null;

    protected bool | Closure | null $isDense = null;

    public function gap(bool | Closure | null $condition = true): static
    {
        $this->hasGap = $condition;

        return $this;
    }

    public function hasGap(): bool
    {
        if (($condition = $this->evaluate($this->hasGap)) !== null) {
            return (bool) $condition;
        }

        if ($this instanceof Schema) {
            return $this->getParentComponent()?->hasGap() ?? true;
        }

        return $this->getContainer()->hasGap();
    }

    public function dense(bool | Closure | null $condition = true): static
    {
        $this->isDense = $condition;

        return $this;
    }

    public function isDense(): bool
    {
        if (($condition = $this->evaluate($this->isDense)) !== null) {
            return (bool) $condition;
        }

        if ($this instanceof Schema) {
            return $this->isInline() || $this->getParentComponent()?->isDense();
        }

        return $this->getContainer()->isDense();
    }
}
