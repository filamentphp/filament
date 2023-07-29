<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Enums\IconSize;

trait HasIcon
{
    protected string | Closure | null $icon = null;

    protected string | Closure | null $iconPosition = null;

    protected IconSize | string | Closure | null $iconSize = null;

    public function icon(IconSize | string | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function iconPosition(string | Closure | null $position): static
    {
        $this->iconPosition = $position;

        return $this;
    }

    public function iconSize(IconSize | string | Closure | null $size): static
    {
        $this->iconSize = $size;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    public function getIconPosition(): ?string
    {
        return $this->evaluate($this->iconPosition) ?? 'before';
    }

    public function getIconSize(): IconSize | string | null
    {
        return $this->evaluate($this->iconSize);
    }
}
