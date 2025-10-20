<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Enums\IconPosition;

trait HasIconPosition
{
    protected IconPosition | string | Closure | null $iconPosition = null;

    public function iconPosition(IconPosition | string | Closure | null $position): static
    {
        $this->iconPosition = $position;

        return $this;
    }

    public function getIconPosition(): IconPosition
    {
        $position = $this->evaluate($this->iconPosition);

        if ($position instanceof IconPosition) {
            return $position;
        }

        if (blank($position)) {
            return IconPosition::Before;
        }

        return IconPosition::tryFrom($position) ?? IconPosition::Before;
    }
}
