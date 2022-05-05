<?php

namespace Filament\Support\Actions\Concerns;

trait HasIcon
{
    protected ?string $icon = null;

    protected ?string $iconPosition = null;

    public function icon(?string $icon): static
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
        return $this->icon;
    }

    public function getIconPosition(): ?string
    {
        return $this->iconPosition;
    }
}
