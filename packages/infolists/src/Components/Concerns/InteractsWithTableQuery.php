<?php

namespace Filament\Infolists\Components\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

trait InteractsWithTableQuery
{
    public function queriesRelationships(Model $record): bool
    {
        return $this->getRelationship($record) !== null;
    }

    public function getRelationship(Model $record, ?string $name = null): ?Relation
    {
        if (blank($name) && (! str($this->getName())->contains('.'))) {
            return null;
        }

        $relationship = null;

        foreach (explode('.', $name ?? $this->getRelationshipName()) as $nestedRelationshipName) {
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
                    $results = array_merge($results, $currentRelationshipValue->all());

                    continue;
                }

                foreach ($currentRelationshipValue as $valueRecord) {
                    $results = array_merge(
                        $results,
                        $this->getRelationshipResults(
                            $valueRecord,
                            $relationships,
                        ),
                    );
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

    public function getRelationshipAttribute(?string $name = null): string
    {
        $name ??= $this->getName();

        if (! str($name)->contains('.')) {
            return $name;
        }

        return (string) str($name)->afterLast('.');
    }

    public function getRelationshipName(?string $name = null): ?string
    {
        $name ??= $this->getName();

        if (! str($name)->contains('.')) {
            return null;
        }

        return (string) str($name)->beforeLast('.');
    }
}
