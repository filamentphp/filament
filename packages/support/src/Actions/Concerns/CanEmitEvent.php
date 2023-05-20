<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;

trait CanEmitEvent
{
    protected string | Closure | null $event = null;

    protected array | Closure $eventData = [];

    protected string | bool $emitDirection = false;

    protected ?string $emitToComponent = null;

    public function emit(
        string | Closure | null $event,
        array | Closure $data = [],
    ): static {
        $this->event = $event;
        $this->eventData = $data;
        $this->emitDirection = false;

        return $this;
    }

    public function emitSelf(
        string | Closure | null $event,
        array | Closure $data = [],
    ): static {
        $this->emit($event, $data);
        $this->emitDirection = 'self';

        return $this;
    }

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

    public function eventData(array | Closure $data): static
    {
        $this->eventData = $data;

        return $this;
    }

    public function getEventData(): array
    {
        return $this->evaluate($this->eventData);
    }

    public function getEmitDirection(): string | bool
    {
        return $this->emitDirection;
    }

    public function getEmitToComponent(): ?string
    {
        return $this->emitToComponent;
    }

    public function getLivewireMountAction(): ?string
    {
        $event = $this->getEvent();

        if (blank($event)) {
            return null;
        }

        $arguments = collect([$event])
            ->merge($this->getEventData())
            ->when(
                $this->emitToComponent,
                fn (Collection $collection, string $component) => $collection->prepend($component),
            )
            ->map(fn (mixed $value): string => Js::from($value)->toHtml())
            ->implode(', ');

        return match ($this->emitDirection) {
            'self' => "\$emitSelf($arguments)",
            'to' => "\$emitTo($arguments)",
            'up' => "\$emitUp($arguments)",
            default => "\$emit($arguments)"
        };
    }
}
