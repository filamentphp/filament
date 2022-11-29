<?php

namespace Filament\Tables\Columns\Concerns;

use BackedEnum;
use Closure;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;

trait HasState
{
    /**
     * @var mixed
     */
    protected $defaultState = null;

    protected ?Closure $getStateUsing = null;

    public function getStateUsing(?Closure $callback): static
    {
        $this->getStateUsing = $callback;

        return $this;
    }

    /**
     * @param  mixed  $state
     */
    public function default($state): static
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

        $relationshipName = $this->getRelationshipName();

        if (! method_exists($record, $relationshipName)) {
            return null;
        }

        $relationship = $record->{$relationshipName}();

        if (! (
            $relationship instanceof HasMany ||
            $relationship instanceof BelongsToMany ||
            $relationship instanceof MorphMany
        )) {
            return null;
        }

        $state = $record->getRelationValue($relationshipName)->pluck($this->getRelationshipAttribute());

        if (! count($state)) {
            return null;
        }

        return $state->toArray();
    }

    /**
     * @param  array<array-key>  $state
     */
    protected function mutateArrayState(array $state): mixed
    {
        return $state;
    }
}
