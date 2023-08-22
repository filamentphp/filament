<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasAnimation
{
    protected string | Htmlable | Closure | null $animation = null;

    public function animation(string | Htmlable | Closure | null $animation = null): static
    {
        $this->aniamtion = $animation;

        return $this;
    }

    public function getAnimation(): string | Htmlable | null
    {
        return $this->evaluate($this->description);
    }
}
