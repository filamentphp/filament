<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Illuminate\Support\Collection;

trait CanEmitEvent
{
    protected string | Closure | null $event = null;

    protected array | Closure $eventData = [];

    protected string | bool $emitDirection = false;

    protected ?string $emitToTarget = null;

    public function emit(
        string | Closure | null $event,
        array | Closure $data = []
    ): static {
        $this->event = $event;
        $this->eventData = $data;
        $this->emitDirection = false;

        return $this;
    }

    public function emitSelf(
        string | Closure | null $event,
        array | Closure $data = []
    ): static {
        $this->emit($event, $data);
        $this->emitDirection = 'self';

        return $this;
    }

    public function emitTo(
        string $to,
        string | Closure | null $event,
        array | Closure $data = [],
    ): static {
        $this->emit($event, $data);
        $this->emitDirection = 'to';
        $this->emitToTarget = $to;

        return $this;
    }

    public function emitUp(
        string | Closure | null $event,
        array | Closure $data = []
    ): static {
        $this->emit($event, $data);
        $this->emitDirection = 'up';

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

    public function getWireClickAction(): ?string
    {
        $wireClickAction = null;

        if ($this->getEvent()) {
            $emitArguments = collect([$this->getEvent()])
                ->merge($this->getEventData())
                ->when($this->emitToTarget, fn (Collection $collection, string $target) => $collection->prepend($target))
                ->map(fn (mixed $value): string => \Illuminate\Support\Js::from($value)->toHtml())
                ->implode(', ');

            $wireClickAction = match ($this->emitDirection) {
                'self' => "\$emitSelf($emitArguments)",
                'up' => "\$emitUp($emitArguments)",
                'to' => "\$emitTo($emitArguments)",
                default => "\$emit($emitArguments)"
            };
        }

        return $wireClickAction;
    }
}
