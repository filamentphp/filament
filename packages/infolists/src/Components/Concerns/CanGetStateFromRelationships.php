<?php

namespace Filament\Infolists\Components\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

trait CanGetStateFromRelationships
{
    public function hasRelationship(Model $record): bool
    {
        return $this->getRelationship($record) !== null;
    }

    public function getRelationship(Model $record, ?string $statePath = null): ?Relation
    {
        if (blank($statePath) && (! str($this->getStatePath())->contains('.'))) {
            return null;
        }

        $relationship = null;

        foreach (explode('.', $statePath ?? $this->getRelationshipName()) as $nestedRelationshipName) {
            if (! $record->isRelation($nestedRelationshipName)) {
                $relationship = null;

                break;
            }

            $relationship = $record->{$nestedRelationshipName}();
            $record = $relationship->getRelated();
        }

        return $relationship;
    }

    /**
     * @param  array<string> | null  $relationships
     * @return array<Model>
     */
    public function getRelationshipResults(Model $record, ?array $relationships = null): array
    {
        $results = [];

        $relationships ??= explode('.', $this->getRelationshipName());

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

    public function getRelationshipAttribute(?string $statePath = null): string
    {
        $statePath ??= $this->getStatePath();

        if (! str($statePath)->contains('.')) {
            return $statePath;
        }

        return (string) str($statePath)->afterLast('.');
    }

    public function getRelationshipName(?string $statePath = null): ?string
    {
        $statePath ??= $this->getStatePath();

        if (! str($statePath)->contains('.')) {
            return null;
        }

        return (string) str($statePath)->beforeLast('.');
    }
}
