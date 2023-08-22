<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasAnimation
{
    protected bool | Htmlable | Closure | null $animation = null;

    public function animated(bool | Closure | null $animation = true): static
    {
        $this->animation = $animation;

        return $this;
    }

    public function getAnimation(): bool | Htmlable | null
    {
        return $this->evaluate($this->animation);
    }
}
