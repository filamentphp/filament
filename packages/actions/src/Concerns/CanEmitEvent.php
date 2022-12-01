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

    /**
     * @param  array<int, mixed> | Closure  $data
     */
    public function emit(
        string | Closure | null $event,
        array | Closure $data = [],
    ): static {
        $this->event = $event;
        $this->eventData = $data;

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
}
