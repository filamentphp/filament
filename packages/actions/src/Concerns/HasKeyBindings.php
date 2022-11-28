<?php

namespace Filament\Actions\Concerns;

use Closure;
use Illuminate\Support\Arr;

trait HasKeyBindings
{
    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $keyBindings = null;

    /**
     * @param  string | array<string> | Closure | null  $bindings
     */
    public function keyBindings(string | array | Closure | null $bindings): static
    {
        $this->keyBindings = $bindings;

        return $this;
    }

    /**
     * @return array<string> | null
     */
    public function getKeyBindings(): ?array
    {
        $keyBindings = Arr::wrap($this->evaluate($this->keyBindings));

        return count($keyBindings) ? $keyBindings : null;
    }
}
