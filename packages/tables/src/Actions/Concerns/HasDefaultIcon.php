<?php

namespace Filament\Tables\Actions\Concerns;

trait HasDefaultIcon
{
    protected ?string $defaultIcon = null;

    public function icon(?string $icon): static
    {
        try {
            return parent::icon($icon);
        } finally {
            $this->defaultIcon(null);
        }
    }

    public function defaultIcon(?string $icon): static
    {
        $this->defaultIcon = $icon;

        return $this;
    }

    public function getDefaultIcon(): ?string
    {
        return $this->hasDefaultIcon() ? $this->defaultIcon : null;
    }

    protected function hasDefaultIcon(): bool
    {
        return $this->getView() !== $this->getLinkView();
    }
}
