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
        return $this->evaluate($this->defaultState);
    }

    public function getState()
    {
        $state = $this->getStateUsing ?
            $this->evaluate($this->getStateUsing) :
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

    protected function getStateFromRecord()
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

        $state = $this->collectRelationValues('', $this->getName(), $record, $state);

        return count($state) ? $state : null;
    }

    protected function collectRelationValues($relationshipName, $restOfName, $record, &$results)
    {
        $relationshipName = (string) str($restOfName)->before('.');
        $restOfName = (string) str($restOfName)->after('.');

        if (! method_exists($record, $relationshipName)) {
            return [];
        }

        if (! str($restOfName)->contains('.')) {
            $state = $record->{$relationshipName}()->pluck($restOfName);

            return $state->toArray();
        } else {
            $related = $record->{$relationshipName}();

            foreach ($related->get() as $relatedRecord) {
                $results = array_merge(
                    $results,
                    $this->collectRelationValues($relationshipName, $restOfName, $relatedRecord, $results)
                );
            }
        }

        return $results;
    }

    protected function mutateArrayState(array $state)
    {
        return $state;
    }
}
