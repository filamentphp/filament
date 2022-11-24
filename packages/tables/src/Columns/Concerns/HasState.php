<?php

namespace Filament\Tables\Columns\Concerns;

use BackedEnum;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasState
{
    protected $defaultState = null;

    protected ?Closure $getStateUsing = null;

    protected ?Closure $mutateArrayStateUsing = null;

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
        return $this->evaluate($this->defaultState, exceptParameters: ['state']);
    }

    public function getState()
    {
        if (! $this->getRecord()) {
            return null;
        }

        $state = $this->getStateUsing ?
            $this->evaluate($this->getStateUsing, exceptParameters: ['state']) :
            $this->getStateFromRecord();

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

        if (is_array($state)) {
            $state = $this->mutateArrayState($state);
        }

        return $state;
    }

    public function getStateFromRecord()
    {
        $record = $this->getRecord();

        $state = Arr::get($record, $this->getName());

        if ($state !== null) {
            return $state;
        }

        if (! $this->queriesRelationships($record)) {
            return null;
        }

        $state = $this->collectNestedAttributes();

        return count($state) ? $state : null;
    }

    protected function mutateArrayState(array $state)
    {
        return $state;
    }
}
