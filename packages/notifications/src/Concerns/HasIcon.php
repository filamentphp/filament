<?php

namespace Filament\Notifications\Concerns;

use Closure;

trait HasIcon
{
    protected string | Closure | null $icon = null;

    protected string | Closure | null $iconColor = 'secondary';

    public function icon(string | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function iconColor(string | Closure | null $color): static
    {
        $this->iconColor = $color;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    public function getIconColor(): ?string
    {
        return $this->evaluate($this->iconColor);
    }
}
