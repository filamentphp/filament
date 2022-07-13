<?php

namespace Filament\Notifications\Actions\Concerns;

use Closure;

trait HasEvent
{
    protected string | Closure | null $event = null;

    protected array | Closure $eventData = [];

    public function event(
        string | Closure | null $event,
        array | Closure $data,
    ): static
    {
        $this->event = $event;
        $this->eventData = $data;

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->evaluate($this->event);
    }

    public function eventData(array | Closure $data): static
    {
        $this->eventData = $data;

        return $this;
    }

    public function getEventData(): array
    {
        return $this->evaluate($this->eventData);
    }
}
