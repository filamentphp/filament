<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasToggleIcons
{
    protected string | Closure | null $offIcon = null;

    protected string | Closure | null $onIcon = null;

    public function offIcon(string | Closure | null $icon): static
    {
        $this->offIcon = $icon;

        return $this;
    }

    public function onIcon(string | Closure | null $icon): static
    {
        $this->onIcon = $icon;

        return $this;
    }

    public function getOffIcon(): ?string
    {
        return $this->evaluate($this->offIcon);
    }

    public function getOnIcon(): ?string
    {
        return $this->evaluate($this->onIcon);
    }

    public function hasOffIcon(): bool
    {
        return (bool) $this->getOffIcon();
    }

    public function hasOnIcon(): bool
    {
        return (bool) $this->getOnIcon();
    }
}
