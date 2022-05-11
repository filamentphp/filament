<?php

namespace Filament\Support\Actions\Concerns;

use Closure;

trait HasIcon
{
    protected Closure | string | null $icon = null;

    protected ?string $iconPosition = null;

    public function icon(Closure | string | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function iconPosition(?string $position): static
    {
        $this->iconPosition = $position;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    public function getIconPosition(): ?string
    {
        return $this->iconPosition;
    }
}
