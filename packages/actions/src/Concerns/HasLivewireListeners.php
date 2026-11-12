<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasLivewireListeners
{
    /**
     * @var array<string, Closure | null> | array<string> | Closure
     */
    protected array | Closure $livewireListeners = [];

    /**
     * @param  array<string, Closure | null> | array<string> | Closure  $listeners
     */
    public function livewireListeners(array | Closure $listeners): static
    {
        $this->livewireListeners = $listeners;

        return $this;
    }

    /**
     * @return array<string, Closure | null>
     */
    public function getLivewireListeners(): array
    {
        $listeners = [];

        foreach ($this->evaluate($this->livewireListeners) as $name => $callback) {
            if (is_int($name)) {
                $listeners[$callback] = null;
            } else {
                $listeners[$name] = $callback;
            }
        }

        return $listeners;
    }
}
