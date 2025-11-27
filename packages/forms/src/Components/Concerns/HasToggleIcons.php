<?php

namespace Filament\Forms\Components\Concerns;

use BackedEnum;
use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasToggleIcons
{
    protected string | BackedEnum | Htmlable | Closure | null $offIcon = null;

    protected string | BackedEnum | Htmlable | Closure | null $onIcon = null;

    public function offIcon(string | BackedEnum | Htmlable | Closure | null $icon): static
    {
        $this->offIcon = $icon;

        return $this;
    }

    public function onIcon(string | BackedEnum | Htmlable | Closure | null $icon): static
    {
        $this->onIcon = $icon;

        return $this;
    }

    public function getOffIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->evaluate($this->offIcon);
    }

    public function getOnIcon(): string | BackedEnum | Htmlable | null
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
