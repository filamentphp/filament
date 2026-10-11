<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Enums\IconSize;

trait HasIconSize
{
    protected IconSize | string | Closure | null $iconSize = null;

    public function iconSize(IconSize | string | Closure | null $size): static
    {
        $this->iconSize = $size;

        return $this;
    }

    public function getIconSize(): IconSize | string | null
    {
        $size = $this->evaluate($this->iconSize);

        if (is_string($size)) {
            $size = IconSize::tryFrom($size) ?? $size;
        }

        return $size;
    }

    public function hasIconSize(): bool
    {
        return $this->iconSize !== null;
    }
}
