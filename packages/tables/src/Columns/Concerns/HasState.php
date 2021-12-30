<?php

namespace Filament\Tables\Columns\Concerns;

use BackedEnum;
use Closure;
use Illuminate\Support\Arr;

trait HasState
{
    protected $defaultState = null;

    protected ?Closure $getStateUsing = null;

    public function getStateUsing(?Closure $callback): static
    {
        $this->getStateUsing = $callback;

        return $this;
    }

    public function default($state): static
    {
        $this->defaultState = $state;

        return $this;
    }

    public function getDefaultState()
    {
        return $this->defaultState;
    }

    public function getState()
    {
        if ($this->getStateUsing instanceof Closure) {
            $state = app()->call($this->getStateUsing, [
                'livewire' => $this->getLivewire(),
                'record' => $this->getRecord(),
            ]);
        } else {
            $state = Arr::get($this->getRecord(), $this->getName());
        }

        if (
            interface_exists(BackedEnum::class) &&
            ($state instanceof BackedEnum) &&
            property_exists($state, 'value')
        ) {
            $state = $state->value;
        }

        if ($state === null) {
            $state = value($this->getDefaultState());
        }

        return $state;
    }
}
