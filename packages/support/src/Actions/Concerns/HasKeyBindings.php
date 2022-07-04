<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Illuminate\Support\Arr;

trait HasKeyBindings
{
    protected string | array | Closure | null $keyBindings = null;

    public function keyBindings(string | array | Closure | null $bindings): static
    {
        $this->keyBindings = $bindings;

        return $this;
    }

    public function getKeyBindings(): ?array
    {
        $keyBindings = Arr::wrap($this->evaluate($this->keyBindings));

        return count($keyBindings) ? $keyBindings : null;
    }
}
