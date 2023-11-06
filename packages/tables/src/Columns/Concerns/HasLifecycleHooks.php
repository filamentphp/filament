<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait HasLifecycleHooks
{
    protected ?Closure $before = null;

    protected ?Closure $after = null;

    public function before(Closure $callback): static
    {
        $this->before = $callback;

        return $this;
    }

    public function after(Closure $callback): static
    {
        $this->after = $callback;

        return $this;
    }

    public function callBefore(): mixed
    {
        return $this->evaluate($this->before);
    }

    public function callAfter(): mixed
    {
        return $this->evaluate($this->after);
    }
}
