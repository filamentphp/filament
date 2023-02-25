<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HasState
{
    protected mixed $defaultState = null;

    protected ?Closure $getStateUsing = null;

    protected string | Closure | null $separator = null;

    protected bool | Closure $isDistinctList = false;

    public function distinctList(bool | Closure $condition = true): static
    {
        $this->isDistinctList = $condition;

        return $this;
    }

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

    public function isDistinctList(): bool
    {
        return (bool) $this->evaluate($this->isDistinctList);
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

        if (is_string($state) && ($separator = $this->getSeparator())) {
            $state = explode($separator, $state);
            $state = (count($state) === 1 && blank($state[0])) ?
                [] :
                $state;
        }

        if ($state === null) {
            $state = value($this->getDefaultState());
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
            ->when($this->isDistinctList(), fn (Collection $state) => $state->unique())
            ->values();

        if (! $state->count()) {
            return null;
        }

        return $state->all();
    }

    public function separator(string | Closure | null $separator = ','): static
    {
        $this->separator = $separator;

        return $this;
    }

    public function getSeparator(): ?string
    {
        return $this->evaluate($this->separator);
    }
}
