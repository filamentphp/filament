<?php

namespace Filament\Forms\Components\Concerns;

trait HasIcon
{
    protected ?string $icon = null;

    public function icon(string $icon): static
    {
        ray($icon);
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }
}
