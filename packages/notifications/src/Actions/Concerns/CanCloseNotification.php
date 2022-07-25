<?php

namespace Filament\Notifications\Actions\Concerns;

use Closure;

trait CanCloseNotification
{
    protected bool | Closure $shouldCloseNotification = false;

    public function close(bool | Closure $condition = true): static
    {
        $this->shouldCloseNotification = $condition;

        return $this;
    }

    public function shouldCloseNotification(): bool
    {
        return $this->evaluate($this->shouldCloseNotification);
    }
}
