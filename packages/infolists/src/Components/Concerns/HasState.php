<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HasState
{
    use CanGetStateFromRelationships;

    protected mixed $defaultState = null;

    protected mixed $getStateUsing = null;

    protected ?string $statePath = null;

    protected string | Closure | null $separator = null;

    protected bool | Closure $isDistinctList = false;

    protected string $cachedAbsoluteStatePath;

    public function getStateUsing(mixed $callback): static
    {
        $this->getStateUsing = $callback;

        return $this;
    }

    public function state(mixed $state): static
    {
        $this->getStateUsing($state);

        return $this;
    }

    public function default(mixed $state): static
    {
        $this->defaultState = $state;

        return $this;
    }

    public function distinctList(bool | Closure $condition = true): static
    {
        $this->isDistinctList = $condition;

        return $this;
    }

    public function isDistinctList(): bool
    {
        return (bool) $this->evaluate($this->isDistinctList);
    }

    public function getDefaultState(): mixed
    {
        return $this->evaluate($this->defaultState);
    }

    public function getState(): mixed
    {
        if ($this->getStateUsing !== null) {
            $state = $this->evaluate($this->getStateUsing);
        } else {
            $containerState = $this->getContainer()->getState();

            $state = $containerState instanceof Model ?
                $this->getStateFromRecord($containerState) :
                data_get($containerState, $this->getStatePath());
        }

        if (is_string($state) && ($separator = $this->getSeparator())) {
            $state = explode($separator, $state);
            $state = (count($state) === 1 && blank($state[0])) ?
                [] :
                $state;
        }

        if (blank($state)) {
            $state = $this->getDefaultState();
        }

        return $state;
    }

    public function separator(string | Closure | null $separator = ','): static
    {
        $this->separator = $separator;

        return $this;
    }

    public function statePath(?string $path): static
    {
        $this->statePath = $path;

        return $this;
    }

    public function getSeparator(): ?string
    {
        return $this->evaluate($this->separator);
    }

    public function getRecord(): ?Model
    {
        return $this->getContainer()->getRecord();
    }

    public function hasStatePath(): bool
    {
        return filled($this->statePath);
    }

    public function getStatePath(bool $isAbsolute = true): string
    {
        if (isset($this->cachedAbsoluteStatePath)) {
            return $this->cachedAbsoluteStatePath;
        }

        $pathComponents = [];

        if ($isAbsolute && ($containerStatePath = $this->getContainer()->getStatePath())) {
            $pathComponents[] = $containerStatePath;
        }

        if ($this->hasStatePath()) {
            $pathComponents[] = $this->statePath;
        }

        return $this->cachedAbsoluteStatePath = implode('.', $pathComponents);
    }

    public function getStateFromRecord(Model $record): mixed
    {
        $state = data_get($record, $this->getStatePath());

        if ($state !== null) {
            return $state;
        }

        if (! $this->hasRelationship($record)) {
            return null;
        }

        $relationship = $this->getRelationship($record);

        if (! $relationship) {
            return null;
        }

        $relationshipAttribute = $this->getRelationshipAttribute();

        $state = collect($this->getRelationshipResults($record))
            ->filter(fn (Model $record): bool => array_key_exists($relationshipAttribute, $record->attributesToArray()))
            ->pluck($relationshipAttribute)
            ->when($this->isDistinctList(), fn (Collection $state) => $state->unique())
            ->values();

        if (! $state->count()) {
            return null;
        }

        return $state->all();
    }

    protected function flushCachedAbsoluteStatePath(): void
    {
        unset($this->cachedAbsoluteStatePath);
    }
}
