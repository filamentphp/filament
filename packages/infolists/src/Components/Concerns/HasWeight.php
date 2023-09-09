<?php

namespace Filament\Infolists\Components\Concerns;

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

    public function getWeight(mixed $state): FontWeight | string | null
    {
        return $this->evaluate($this->weight, [
            'state' => $state,
        ]);
    }
}
