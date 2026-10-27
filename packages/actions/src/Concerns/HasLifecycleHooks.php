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

    protected ?Closure $beforeSchemaFilled = null;

    protected ?Closure $afterSchemaFilled = null;

    protected ?Closure $beforeSchemaValidated = null;

    protected ?Closure $afterSchemaValidated = null;

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
        $this->beforeSchemaFilled = $callback;

        return $this;
    }

    public function afterSchemaFilled(?Closure $callback): static
    {
        $this->afterSchemaFilled = $callback;

        return $this;
    }

    public function beforeSchemaValidated(?Closure $callback): static
    {
        $this->beforeSchemaValidated = $callback;

        return $this;
    }

    public function afterSchemaValidated(?Closure $callback): static
    {
        $this->afterSchemaValidated = $callback;

        return $this;
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
        return $this->evaluate($this->beforeSchemaFilled);
    }

    public function callAfterSchemaFilled(): mixed
    {
        return $this->evaluate($this->afterSchemaFilled);
    }

    public function callBeforeSchemaValidated(): mixed
    {
        return $this->evaluate($this->beforeSchemaValidated);
    }

    public function callAfterSchemaValidated(): mixed
    {
        return $this->evaluate($this->afterSchemaValidated);
    }
}
