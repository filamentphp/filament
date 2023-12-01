<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanDispatchEvent
{
    protected string | Closure | null $event = null;

    /**
     * @var array<mixed> | Closure
     */
    protected array | Closure $eventData = [];

    protected string | bool $dispatchDirection = false;

    protected ?string $dispatchToComponent = null;

    /**
     * @param  array<mixed> | Closure  $data
     */
    public function dispatch(
        string | Closure | null $event,
        array | Closure $data = [],
    ): static {
        $this->event = $event;
        $this->eventData($data);
        $this->dispatchDirection = false;

        return $this;
    }

    /**
     * @param  array<int, mixed> | Closure  $data
     */
    public function dispatchSelf(
        string | Closure | null $event,
        array | Closure $data = [],
    ): static {
        $this->dispatch($event, $data);
        $this->dispatchDirection = 'self';

        return $this;
    }

    /**
     * @param  array<int, mixed> | Closure  $data
     */
    public function dispatchTo(
        string $component,
        string | Closure | null $event,
        array | Closure $data = [],
    ): static {
        $this->dispatch($event, $data);
        $this->dispatchDirection = 'to';
        $this->dispatchToComponent = $component;

        return $this;
    }

    /**
     * @deprecated Use `dispatch()` instead.
     *
     * @param  array<int, mixed> | Closure  $data
     */
    public function emit(
        string | Closure | null $event,
        array | Closure $data = [],
    ): static {
        $this->dispatch($event, $data);

        return $this;
    }

    /**
     * @deprecated Use `dispatchSelf()` instead.
     *
     * @param  array<int, mixed> | Closure  $data
     */
    public function emitSelf(
        string | Closure | null $event,
        array | Closure $data = [],
    ): static {
        $this->dispatchSelf($event, $data);

        return $this;
    }

    /**
     * @deprecated Use `dispatchTo()` instead.
     *
     * @param  array<int, mixed> | Closure  $data
     */
    public function emitTo(
        string $component,
        string | Closure | null $event,
        array | Closure $data = [],
    ): static {
        $this->dispatchTo($component, $event, $data);

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->evaluate($this->event);
    }

    /**
     * @param  array<mixed> | Closure  $data
     */
    public function eventData(array | Closure $data): static
    {
        $this->eventData = $data;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getEventData(): array
    {
        return $this->evaluate($this->eventData);
    }

    public function getDispatchDirection(): string | bool
    {
        return $this->dispatchDirection;
    }

    public function getDispatchToComponent(): ?string
    {
        return $this->dispatchToComponent;
    }
}
