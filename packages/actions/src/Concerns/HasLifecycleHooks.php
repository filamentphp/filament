<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasLifecycleHooks
{
    protected ?Closure $before = null;

    protected ?Closure $after = null;

    protected ?Closure $beforeFormFilled = null;

    protected ?Closure $afterFormFilled = null;

    protected ?Closure $beforeFormValidated = null;

    protected ?Closure $afterFormValidated = null;

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

    public function beforeFormFilled(Closure $callback): static
    {
        $this->beforeFormFilled = $callback;

        return $this;
    }

    public function afterFormFilled(Closure $callback): static
    {
        $this->afterFormFilled = $callback;

        return $this;
    }

    public function beforeFormValidated(Closure $callback): static
    {
        $this->beforeFormValidated = $callback;

        return $this;
    }

    public function afterFormValidated(Closure $callback): static
    {
        $this->afterFormValidated = $callback;

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

    public function callBeforeFormFilled(): mixed
    {
        return $this->evaluate($this->beforeFormFilled);
    }

    public function callAfterFormFilled(): mixed
    {
        return $this->evaluate($this->afterFormFilled);
    }

    public function callBeforeFormValidated(): mixed
    {
        return $this->evaluate($this->beforeFormValidated);
    }

    public function callAfterFormValidated(): mixed
    {
        return $this->evaluate($this->afterFormValidated);
    }
}
