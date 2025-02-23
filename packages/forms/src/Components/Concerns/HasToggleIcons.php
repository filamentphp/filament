<?php

namespace Filament\Forms\Components\Concerns;

use BackedEnum;
use Closure;

trait HasToggleIcons
{
    protected string | BackedEnum | Closure | null $offIcon = null;

    protected string | BackedEnum | Closure | null $onIcon = null;

    public function offIcon(string | BackedEnum | Closure | null $icon): static
    {
        $this->offIcon = $icon;

        return $this;
    }

    public function onIcon(string | BackedEnum | Closure | null $icon): static
    {
        $this->onIcon = $icon;

        return $this;
    }

    public function getOffIcon(): string | BackedEnum | null
    {
        return $this->evaluate($this->offIcon);
    }

    public function getOnIcon(): string | BackedEnum | null
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
