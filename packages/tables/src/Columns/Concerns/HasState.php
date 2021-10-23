<?php

namespace Filament\Tables\Columns\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasState
{
    protected $getStateUsing = null;

    public function getStateUsing(callable $callback): static
    {
        $this->getStateUsing = $callback;

        return $this;
    }

    public function getState()
    {
        if ($this->getStateUsing) {
            return app()->call($this->getStateUsing, [
                'livewire' => $this->getLivewire(),
                'record' => $this->getRecord(),
            ]);
        }

        return Arr::get($this->getRecord(), $this->getName());
    }
}
