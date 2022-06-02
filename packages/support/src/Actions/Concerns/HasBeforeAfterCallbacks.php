<?php

namespace Filament\Support\Actions\Concerns;

use Closure;

trait HasBeforeAfterCallbacks
{
    protected ?Closure $beforeCallback = null;

    protected ?Closure $afterCallback = null;

    public function callBefore(Closure $callback): static
    {
        $this->beforeCallback = $callback;

        return $this;
    }

    public function callAfter(Closure $callback): static
    {
        $this->afterCallback = $callback;

        return $this;
    }
}
