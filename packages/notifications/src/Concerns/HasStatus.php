<?php

namespace Filament\Notifications\Concerns;

use Closure;

trait HasStatus
{
    protected string | Closure | null $status = null;

    public function status(string | Closure | null $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->evaluate($this->status);
    }

    public function danger(): static
    {
        return $this->status('danger');
    }

    public function info(): static
    {
        return $this->status('info');
    }

    public function success(): static
    {
        return $this->status('success');
    }

    public function warning(): static
    {
        return $this->status('warning');
    }
}
