<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasGroupedIcon
{
    protected string | Closure | null $groupedIcon = null;

    public function groupedIcon(string | Closure | null $icon): static
    {
        $this->groupedIcon = $icon;

        return $this;
    }

    public function getGroupedIcon(): ?string
    {
        return $this->evaluate($this->groupedIcon) ?? $this->getIcon();
    }
}
