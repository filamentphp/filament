<?php

namespace Filament\Pages\Actions\Concerns;

use Closure;

trait HasKeyBindings
{
    protected string|Closure|null $keyBindings = null;

    public function keyBindings(string|Closure|null $keyBindings): static
    {
        $this->keyBindings = $keyBindings;

        return $this;
    }

    public function getKeyBindings(): ?string
    {
        return $this->keyBindings;
    }
}
