<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanEmitEvent
{
    protected string | Closure | null $event = null;

    /**
     * @var array<int, mixed> | Closure
     */
    protected array | Closure $eventData = [];

    protected string | bool $emitDirection = false;

    protected ?string $emitToComponent = null;

    /**
     * @param  array<int, mixed> | Closure  $data
     */
    public function emit(
        string | Closure | null $event,
        array | Closure $data = [],
    ): static {
        $this->event = $event;
        $this->eventData = $data;
        $this->emitDirection = false;

        return $this;
    }

    /**
     * @param  array<int, mixed> | Closure  $data
     */
    public function emitSelf(
        string | Closure | null $event,
        array | Closure $data = [],
    ): static {
        $this->emit($event, $data);
        $this->emitDirection = 'self';

        return $this;
    }

    /**
     * @param  array<int, mixed> | Closure  $data
     */
    public function emitTo(
        string $component,
        string | Closure | null $event,
        array | Closure $data = [],
    ): static {
        $this->emit($event, $data);
        $this->emitDirection = 'to';
        $this->emitToComponent = $component;

        return $this;
    }

    /**
     * @param  array<int, mixed> | Closure  $data
     */
    public function emitUp(
        string | Closure | null $event,
        array | Closure $data = [],
    ): static {
        $this->emit($event, $data);
        $this->emitDirection = 'up';

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->evaluate($this->event);
    }

    /**
     * @param  array<int, mixed> | Closure  $data
     */
    public function eventData(array | Closure $data): static
    {
        $this->eventData = $data;

        return $this;
    }

    /**
     * @return array<int, mixed>
     */
    public function getEventData(): array
    {
        return $this->evaluate($this->eventData);
    }

    public function getEmitDirection(): bool | string
    {
        return $this->emitDirection;
    }

    public function getEmitToComponent(): ?string
    {
        return $this->emitToComponent;
    }
}
