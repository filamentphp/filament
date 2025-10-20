<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Enums\FontWeight;

trait HasWeight
{
    protected FontWeight | string | Closure | null $weight = null;

    public function weight(FontWeight | string | Closure | null $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getWeight(mixed $state = null): FontWeight | string | null
    {
        $weight = $this->evaluate($this->weight, [
            'state' => $state,
        ]);

        if (! is_string($weight)) {
            return $weight;
        }

        return FontWeight::tryFrom($weight) ?? $weight;
    }
}
