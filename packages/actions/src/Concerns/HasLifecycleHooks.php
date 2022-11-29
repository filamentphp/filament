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

    /**
     * @return mixed
     */
    public function callBefore()
    {
        return $this->evaluate($this->before);
    }

    /**
     * @return mixed
     */
    public function callAfter()
    {
        return $this->evaluate($this->after);
    }

    /**
     * @return mixed
     */
    public function callBeforeFormFilled()
    {
        return $this->evaluate($this->beforeFormFilled);
    }

    /**
     * @return mixed
     */
    public function callAfterFormFilled()
    {
        return $this->evaluate($this->afterFormFilled);
    }

    /**
     * @return mixed
     */
    public function callBeforeFormValidated()
    {
        return $this->evaluate($this->beforeFormValidated);
    }

    /**
     * @return mixed
     */
    public function callAfterFormValidated()
    {
        return $this->evaluate($this->afterFormValidated);
    }
}
