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
            $state = $this->getMutatedArrayState($state) ?? $this->mutateArrayState($state);
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

        $state = [];

        $state = $this->collectRelationValues(explode('.', $this->getName()), $record, $state);

        return count($state) ? $state : null;
    }

    protected function collectRelationValues(array $relationships, Model $record, array &$results): array
    {
        $relationshipName = array_shift($relationships);

        if (! method_exists($record, $relationshipName)) {
            return [];
        }

        if (count($relationships) === 1) {
            return $record->{$relationshipName}()->pluck(array_shift($relationships))->toArray();
        } else {
            foreach ($record->{$relationshipName}()->get() as $relatedRecord) {
                $results = array_merge(
                    $results,
                    $this->collectRelationValues($relationships, $relatedRecord, $results)
                );
            }

            return $results;
        }
    }

    protected function mutateArrayState(array $state)
    {
        return $state;
    }

    public function mutateArrayStateUsing(?Closure $callback): static
    {
        $this->mutateArrayStateUsing = $callback;

        return $this;
    }

    public function getMutatedArrayState(array $state)
    {
        return $this->evaluate($this->mutateArrayStateUsing, [
            'state' => $state,
        ]);
    }
}
