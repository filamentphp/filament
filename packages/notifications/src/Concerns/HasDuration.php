<?php

namespace Filament\Notifications\Concerns;

use Closure;

trait HasDuration
{
    protected int | Closure | null $duration = 6000;

    public function duration(int | string | Closure | null $duration): static
    {
        if ($duration === 'persistent') {
            $duration = null;
        }

        $this->duration = $duration;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->evaluate($this->duration);
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
