<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;

trait HasIcon
{
    protected string | Closure | null $icon = null;

    public function icon(string | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }
}
