<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\Events\ActionCalled;
use Filament\Actions\Events\ActionCalling;
use Illuminate\Support\Facades\Event;

trait HasLifecycleHooks
{
    protected ?Closure $before = null;

    protected ?Closure $after = null;

    protected ?Closure $beforeFormFilled = null;

    protected ?Closure $afterFormFilled = null;

    protected ?Closure $beforeFormValidated = null;

    protected ?Closure $afterFormValidated = null;

    public function before(?Closure $callback): static
    {
        $this->before = $callback;

        return $this;
    }

    public function after(?Closure $callback): static
    {
        $this->after = $callback;

        return $this;
    }

    public function beforeSchemaFilled(?Closure $callback): static
    {
        $this->beforeFormFilled = $callback;

        return $this;
    }

    /**
     * @deprecated Use `beforeSchemaFilled()` instead.
     */
    public function beforeFormFilled(?Closure $callback): static
    {
        return $this->beforeSchemaFilled($callback);
    }

    public function afterSchemaFilled(?Closure $callback): static
    {
        $this->afterFormFilled = $callback;

        return $this;
    }

    /**
     * @deprecated Use `afterSchemaFilled()` instead.
     */
    public function afterFormFilled(?Closure $callback): static
    {
        return $this->afterSchemaFilled($callback);
    }

    public function beforeSchemaValidated(?Closure $callback): static
    {
        $this->beforeFormValidated = $callback;

        return $this;
    }

    /**
     * @deprecated Use `beforeSchemaValidated()` instead.
     */
    public function beforeFormValidated(?Closure $callback): static
    {
        return $this->beforeSchemaValidated($callback);
    }

    public function afterSchemaValidated(?Closure $callback): static
    {
        $this->afterFormValidated = $callback;

        return $this;
    }

    /**
     * @deprecated Use `afterSchemaValidated()` instead.
     */
    public function afterFormValidated(?Closure $callback): static
    {
        return $this->afterSchemaValidated($callback);
    }

    public function callBefore(): mixed
    {
        Event::dispatch(ActionCalling::class, $this);

        return $this->evaluate($this->before);
    }

    public function callAfter(): mixed
    {
        try {
            return $this->evaluate($this->after);
        } finally {
            Event::dispatch(ActionCalled::class, $this);
        }
    }

    public function callBeforeSchemaFilled(): mixed
    {
        return $this->evaluate($this->beforeFormFilled);
    }

    /**
     * @deprecated Use `callBeforeSchemaFilled()` instead.
     */
    public function callBeforeFormFilled(): mixed
    {
        return $this->callBeforeSchemaFilled();
    }

    public function callAfterSchemaFilled(): mixed
    {
        return $this->evaluate($this->afterFormFilled);
    }

    /**
     * @deprecated Use `callAfterSchemaFilled()` instead.
     */
    public function callAfterFormFilled(): mixed
    {
        return $this->callAfterSchemaFilled();
    }

    public function callBeforeSchemaValidated(): mixed
    {
        return $this->evaluate($this->beforeFormValidated);
    }

    /**
     * @deprecated Use `callBeforeSchemaValidated()` instead.
     */
    public function callBeforeFormValidated(): mixed
    {
        return $this->callBeforeSchemaValidated();
    }

    public function callAfterSchemaValidated(): mixed
    {
        return $this->evaluate($this->afterFormValidated);
    }

    /**
     * @deprecated Use `callAfterSchemaValidated()` instead.
     */
    public function callAfterFormValidated(): mixed
    {
        return $this->callAfterSchemaValidated();
    }
}
