<?php

namespace Filament\Notifications\Concerns;

use Closure;

trait HasDuration
{
    protected int | string | Closure $duration = 6000;

    public function duration(int | string | Closure | null $duration): static
    {
        $this->duration = $duration ?? 'persistent';

        return $this;
    }

    public function getDuration(): int | string
    {
        return $this->evaluate($this->duration) ?? 'persistent';
    }

    public function seconds(float $seconds): static
    {
        $this->duration((int) ($seconds * 1000));

        return $this;
    }

    public function persistent(): static
    {
        $this->duration('persistent');

        return $this;
    }
}
