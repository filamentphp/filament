<?php

namespace Filament\Tables\Columns\Concerns;

use BackedEnum;
use Closure;
use Illuminate\Support\Arr;

trait HasState
{
    protected mixed $defaultState = null;

    protected ?Closure $getStateUsing = null;

    public function getStateUsing(?Closure $callback): static
    {
        $this->getStateUsing = $callback;

        return $this;
    }

    public function default(mixed $state): static
    {
        $this->defaultState = $state;

        return $this;
    }

    public function getDefaultState(): mixed
    {
        return $this->evaluate($this->defaultState, exceptParameters: ['state']);
    }

    public function getState(): mixed
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

    public function getStateFromRecord(): mixed
    {
        $record = $this->getRecord();

        $state = Arr::get($record, $this->getName());

        if ($state !== null) {
            return $state;
        }

        if (! $this->queriesRelationships($record)) {
            return null;
        }

        $relationship = $this->getRelationship($record);

        if (! $relationship) {
            return null;
        }

        $state = collect($this->getRelationshipResults($record))
            ->pluck($this->getRelationshipAttribute())
            ->unique()
            ->values();

        if (! $state->count()) {
            return null;
        }

        return $state->all();
    }

    /**
     * @param  array<array-key>  $state
     */
    protected function mutateArrayState(array $state): mixed
    {
        return $state;
    }
}
