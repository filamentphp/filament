<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait HasIcons
{
    protected array | Closure $icons = [];

    protected string $iconPosition = 'start';

    public function icons(array | Closure $icons): static
    {
        $this->icons = $icons;

        return $this;
    }

    public function iconPosition(string $iconPosition = 'start'): static
    {
        $this->iconPosition = $iconPosition;

        return $this;
    }

    public function getStateIcon(): ?string
    {
        $state = $this->getState();
        $stateIcon = null;

        foreach ($this->getIcons() as $icon => $condition) {
            if (is_numeric($icon)) {
                $stateIcon = $condition;
            } elseif ($condition instanceof Closure && $condition($state)) {
                $stateIcon = $icon;
            } elseif ($condition === $state) {
                $stateIcon = $icon;
            }
        }

        return $stateIcon;
    }

    public function getIcons(): array
    {
        return $this->evaluate($this->icons);
    }

    public function getIconPosition(): string
    {
        return $this->evaluate($this->iconPosition);
    }
}
