<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait ListensToEvents
{
    /**
     * @var array<string, array<Closure>>
     */
    protected array $listeners = [];

    public function dispatchEvent(string $event, mixed ...$parameters): static
    {
        foreach ($this->getListeners($event) as $callback) {
            $callback($this, ...$parameters);
        }

        return $this;
    }

    /**
     * @param  array<string, array<Closure>>  $listeners
     */
    public function registerListeners(array $listeners): static
    {
        foreach ($listeners as $event => $callbacks) {
            $this->listeners[$event] = [
                ...$this->getListeners($event),
                ...$callbacks,
            ];
        }

        return $this;
    }

    /**
     * @return array<string | int, array<Closure> | Closure>
     */
    public function getListeners(?string $event = null): array
    {
        $listeners = $this->listeners;

        if ($event) {
            return $listeners[$event] ?? [];
        }

        return $listeners;
    }
}
