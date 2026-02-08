<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Filament\Support\ArrayRecord;
use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use LogicException;
use Znck\Eloquent\Relations\BelongsToThrough;

trait HasCellState
{
    protected mixed $defaultState = null;

    protected mixed $getStateUsing = null;

    protected string | Closure | null $separator = null;

    protected bool | Closure $isDistinctList = false;

    protected ?string $inverseRelationshipName = null;

    /**
     * @var array<string, mixed>
     */
    protected array $cachedState = [];

    protected ?bool $hasMultipleRelationshipCache = null;

    protected ?Relation $relationshipCache = null;

    public function inverseRelationship(?string $name): static
    {
        $this->inverseRelationshipName = $name;

        return $this;
    }

    public function distinctList(bool | Closure $condition = true): static
    {
        $this->isDistinctList = $condition;

        return $this;
    }

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
        return $this->cacheState(function (): mixed {
            $state = ($this->getStateUsing !== null) ?
                $this->evaluate($this->getStateUsing) :
                $this->getStateFromRecord();

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
        });
    }

    public function getStateFromRecord(): mixed
    {
        $record = $this->getRecord();

        if ($record instanceof Model) {
            $relationship = $this->getRelationship($record);

            if ($relationship) {
                $relationshipAttribute = $this->getFullAttributeName($record);

                $state = collect($this->getRelationshipResults($record))
                    ->reduce(
                        function (Collection $carry, Model $record) use ($relationshipAttribute): Collection {
                            if (
                                ($record instanceof HasRichContent) &&
                                $record->hasRichContentAttribute($relationshipAttribute)
                            ) {
                                $state = $record->getRichContentAttribute($relationshipAttribute);
                            } else {
                                $state = data_get($record, $relationshipAttribute);
                            }

                            if (blank($state)) {
                                return $carry;
                            }

                            return $carry->push($state);
                        },
                        initial: collect(),
                    )
                    ->when($this->isDistinctList(), fn (Collection $state) => $state->unique())
                    ->values();

                if (! $state->count()) {
                    return null;
                }

                if (($state->count() < 2) && (! $this->hasMultipleRelationship($record))) {
                    return $state->first();
                }

                return $state->all();
            }
        }

        $name = $this->getName();

        if (
            ($record instanceof HasRichContent) &&
            $record->hasRichContentAttribute($name)
        ) {
            $state = $record->getRichContentAttribute($name);
        } else {
            $state = data_get($record, $name);
        }

        return $state;
    }

    public function clearCachedState(): void
    {
        $this->cachedState = [];
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

    public function hasRelationship(Model $record): bool
    {
        $name = $this->getName();

        if (! str($name)->contains('.')) {
            return false;
        }

        if ($record->hasAttribute((string) str($name)->before('.'))) {
            return false;
        }

        return $record->isRelation((string) str($name)->before('.'));
    }

    /**
     * @deprecated Use `hasRelationship()` instead.
     */
    public function queriesRelationships(Model $record): bool
    {
        return $this->hasRelationship($record);
    }

    public function getRelationship(Model $record, ?string $relationshipName = null): ?Relation
    {
        if ($this->relationshipCache) {
            return $this->relationshipCache;
        }

        if (isset($relationshipName)) {
            $nameParts = explode('.', $relationshipName);
        } else {
            $name = $this->getName();

            if (! str($name)->contains('.')) {
                return null;
            }

            $nameParts = explode('.', $name);
            array_pop($nameParts);
        }

        $relationship = null;

        foreach ($nameParts as $namePart) {
            if ($record->hasAttribute($namePart)) {
                break;
            }

            if (! $record->isRelation($namePart)) {
                break;
            }

            $relationship = $record->{$namePart}();
            $record = $relationship->getRelated();
        }

        return $this->relationshipCache = $relationship;
    }

    public function hasMultipleRelationship(Model $record): bool
    {
        if (isset($this->hasMultipleRelationshipCache)) {
            return $this->hasMultipleRelationshipCache;
        }

        $relationships = explode('.', $this->getRelationshipName($record));

        while (count($relationships)) {
            $currentRelationshipName = array_shift($relationships);

            $currentRelationshipValue = $record->getRelationValue($currentRelationshipName);

            if ($currentRelationshipValue instanceof Collection) {
                return $this->hasMultipleRelationshipCache = true;
            }

            if (! $currentRelationshipValue instanceof Model) {
                break;
            }

            if (! count($relationships)) {
                break;
            }

            $record = $currentRelationshipValue;
        }

        return $this->hasMultipleRelationshipCache = false;
    }

    /**
     * @param  array<string> | null  $relationships
     * @return array<Model>
     */
    public function getRelationshipResults(Model $record, ?array $relationships = null): array
    {
        $results = [];

        $relationships ??= explode('.', $this->getRelationshipName($record));

        while (count($relationships)) {
            $currentRelationshipName = array_shift($relationships);

            $currentRelationshipValue = $record->getRelationValue($currentRelationshipName);

            if ($currentRelationshipValue instanceof Collection) {
                if (! count($relationships)) {
                    $results = [
                        ...$results,
                        ...$currentRelationshipValue->all(),
                    ];

                    continue;
                }

                foreach ($currentRelationshipValue as $valueRecord) {
                    $results = [
                        ...$results,
                        ...$this->getRelationshipResults(
                            $valueRecord,
                            $relationships,
                        ),
                    ];
                }

                break;
            }

            if (! $currentRelationshipValue instanceof Model) {
                break;
            }

            if (! count($relationships)) {
                $results[] = $currentRelationshipValue;

                break;
            }

            $record = $currentRelationshipValue;
        }

        return $results;
    }

    public function getAttributeName(Model $record): string
    {
        $name = $this->getName();

        if (! str($name)->contains('.')) {
            return $name;
        }

        $nameParts = explode('.', $name);
        $lastPart = array_pop($nameParts);

        foreach ($nameParts as $namePart) {
            if ($record->hasAttribute($namePart)) {
                break;
            }

            if (! $record->isRelation($namePart)) {
                break;
            }

            array_shift($nameParts);
            $record = $record->{$namePart}()->getRelated();
        }

        return Arr::first([...$nameParts, $lastPart]);
    }

    public function getFullAttributeName(Model $record): string
    {
        $name = $this->getName();

        if (! str($name)->contains('.')) {
            return $name;
        }

        $nameParts = explode('.', $name);
        $lastPart = array_pop($nameParts);

        foreach ($nameParts as $namePart) {
            if ($record->hasAttribute($namePart)) {
                break;
            }

            if (! $record->isRelation($namePart)) {
                break;
            }

            array_shift($nameParts);
            $record = $record->{$namePart}()->getRelated();
        }

        return implode('.', [...$nameParts, $lastPart]);
    }

    public function getInverseRelationshipName(Model $record): string
    {
        if (filled($this->inverseRelationshipName)) {
            return $this->inverseRelationshipName;
        }

        $nameParts = explode('.', $this->getName());
        array_pop($nameParts);

        $inverseRelationshipParts = [];

        foreach ($nameParts as $namePart) {
            if ($record->hasAttribute($namePart)) {
                break;
            }

            if (! $record->isRelation($namePart)) {
                break;
            }

            $relationship = $record->{$namePart}();
            $record = $relationship->getRelated();

            $inverseNestedRelationshipName = (string) str(class_basename($relationship->getParent()::class))
                ->when(
                    ($relationship instanceof BelongsTo ||
                        $relationship instanceof BelongsToMany ||
                        $relationship instanceof BelongsToThrough),
                    fn (Stringable $name) => $name->plural(),
                )
                ->camel();

            if ($record->hasAttribute($inverseNestedRelationshipName) || (! $record->isRelation($inverseNestedRelationshipName))) {
                // The conventional relationship doesn't exist, but we can
                // attempt to use the original relationship name instead.

                if ($record->hasAttribute($namePart) || (! $record->isRelation($namePart))) {
                    $recordClass = $record::class;

                    throw new LogicException("When trying to guess the inverse relationship for column [{$this->getName()}], relationship [{$inverseNestedRelationshipName}] was not found on model [{$recordClass}]. Please define a custom [inverseRelationship()] for this column.");
                }

                $inverseNestedRelationshipName = $namePart;
            }

            array_unshift($inverseRelationshipParts, $inverseNestedRelationshipName);
        }

        return implode('.', $inverseRelationshipParts);
    }

    public function getRelationshipName(Model $record): ?string
    {
        $name = $this->getName();

        if (! str($name)->contains('.')) {
            return null;
        }

        $nameParts = explode('.', $name);
        array_pop($nameParts);

        $relationshipParts = [];

        foreach ($nameParts as $namePart) {
            if ($record->hasAttribute($namePart)) {
                break;
            }

            if (! $record->isRelation($namePart)) {
                break;
            }

            $relationshipParts[] = $namePart;
            $record = $record->{$namePart}()->getRelated();
        }

        return implode('.', $relationshipParts);
    }

    protected function cacheState(Closure $state): mixed
    {
        $record = $this->getRecord();

        if (! $record) {
            return null;
        }

        if ($this instanceof Column) {
            $recordKey = $this->getLivewire()->getTableRecordKey($record);
        } elseif (is_array($record)) { /** @phpstan-ignore function.impossibleType */
            $recordKey = (string) ($record[ArrayRecord::getKeyName()] ?? null); /** @phpstan-ignore nullCoalesce.offset */
        } else {
            $recordKey = (string) $record->getKey();
        }

        if (blank($recordKey)) {
            return $state();
        }

        if (array_key_exists($recordKey, $this->cachedState)) {
            return $this->cachedState[$recordKey];
        }

        return $this->cachedState[$recordKey] = $state();
    }

    public function getGetStateUsingCallback(): mixed
    {
        return $this->getStateUsing;
    }
}
