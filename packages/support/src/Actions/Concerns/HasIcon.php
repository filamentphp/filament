<?php

namespace Filament\Support\Actions\Concerns;

use Closure;

trait HasIcon
{
    protected ?string $icon = null;

    protected ?string $iconPosition = null;

    public function icon(Closure | ?string $icon): static
    {
        //$this->icon = $icon;
        return $this->evaluate($icon);

        return $this;
    }

    public function iconPosition(?string $position): static
    {
        $this->iconPosition = $position;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getIconPosition(): ?string
    {
        return $this->iconPosition;
    }
}
