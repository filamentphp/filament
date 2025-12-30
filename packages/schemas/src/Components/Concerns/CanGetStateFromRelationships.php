<?php

namespace Filament\Schemas\Components\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

trait CanGetStateFromRelationships
{
    protected ?bool $hasMultipleStateRelationshipCache = null;

    protected ?Relation $stateRelationshipCache = null;

    public function hasStateRelationship(Model $record): bool
    {
        return $this->getStateRelationship($record) !== null;
    }

    public function getStateRelationship(Model $record, ?string $statePath = null): ?Relation
    {
        if ($this->stateRelationshipCache) {
            return $this->stateRelationshipCache;
        }

        if (blank($statePath) && (! str($this->getStateRelationshipPath())->contains('.'))) {
            return null;
        }

        $relationship = null;

        foreach (explode('.', $statePath ?? $this->getStateRelationshipName()) as $nestedRelationshipName) {
            if ($record->hasAttribute($nestedRelationshipName)) {
                $relationship = null;

                break;
            }

            if (! $record->isRelation($nestedRelationshipName)) {
                $relationship = null;

                break;
            }

            $relationship = $record->{$nestedRelationshipName}();
            $record = $relationship->getRelated();
        }

        return $this->stateRelationshipCache = $relationship;
    }

    public function hasMultipleStateRelationship(Model $record): bool
    {
        if (isset($this->hasMultipleStateRelationshipCache)) {
            return $this->hasMultipleStateRelationshipCache;
        }

        $relationships = explode('.', $this->getStateRelationshipName($record));

        while (count($relationships)) {
            $currentRelationshipName = array_shift($relationships);

            $currentRelationshipValue = $record->getRelationValue($currentRelationshipName);

            if ($currentRelationshipValue instanceof Collection) {
                return $this->hasMultipleStateRelationshipCache = true;
            }

            if (! $currentRelationshipValue instanceof Model) {
                break;
            }

            if (! count($relationships)) {
                break;
            }

            $record = $currentRelationshipValue;
        }

        return $this->hasMultipleStateRelationshipCache = false;
    }

    /**
     * @param  array<string> | null  $relationships
     * @return array<Model>
     */
    public function getStateRelationshipResults(Model $record, ?array $relationships = null): array
    {
        $results = [];

        $relationships ??= explode('.', $this->getStateRelationshipName());

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
                        ...$this->getStateRelationshipResults(
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

    public function getStateRelationshipAttribute(?string $statePath = null): string
    {
        $statePath ??= $this->getStateRelationshipPath();

        if (! str($statePath)->contains('.')) {
            return $statePath;
        }

        return (string) str($statePath)->afterLast('.');
    }

    public function getStateRelationshipName(?string $statePath = null): ?string
    {
        $statePath ??= $this->getStateRelationshipPath();

        if (! str($statePath)->contains('.')) {
            return null;
        }

        return (string) str($statePath)->beforeLast('.');
    }

    public function getStateRelationshipPath(): ?string
    {
        return $this->getStatePath(isAbsolute: false);
    }
}
