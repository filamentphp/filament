<?php

namespace Filament\Support\Services;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;
use Znck\Eloquent\Relations\BelongsToThrough;

class RelationshipOrderer
{
    public function buildSubquery(EloquentBuilder $query, string $relationshipName, string $column): Builder
    {
        $relationshipChain = $this->buildRelationshipChain($query->getModel(), $relationshipName);
        $subquery = null;

        foreach (array_reverse($relationshipChain, preserve_keys: true) as $index => $relationship) {
            $relationshipQuery = $relationship->getQuery();
            $relatedQuery = (clone $relationshipQuery)->setQuery(
                $relationshipQuery->getQuery()->newQuery()->from($relationship->getRelated()->getTable()),
            );
            $columns = $subquery
                ? ['filament_ordering_value' => $subquery]
                : [$relationship->getRelated()->qualifyColumn($column)];

            $relatedQuery = $relationship->getRelationExistenceQuery(
                $relatedQuery,
                $index ? $relationshipChain[$index - 1]->getQuery() : $query,
                $columns,
            );

            // Group relationship predicates so an `orWhere()` cannot bypass parent correlation.
            $relatedQuery->where(fn (EloquentBuilder $query) => $query->mergeConstraintsFrom($relationshipQuery));
            $relatedQuery->withoutGlobalScopes($relationshipQuery->removedScopes());

            // `mergeConstraintsFrom()` does not preserve relationship record-selection ordering.
            $relatedQuery->getQuery()->orders = $relationshipQuery->getQuery()->orders;
            $relatedQuery->getQuery()->setBindings($relationshipQuery->getQuery()->getRawBindings()['order'], 'order');

            $subquery = $relatedQuery->limit(1)->toBase();
        }

        return $subquery;
    }

    /**
     * @return array<Relation>
     */
    protected function buildRelationshipChain(Model $baseModel, string $relationshipPath): array
    {
        $relationshipSegments = explode('.', $relationshipPath);
        $currentModel = $baseModel;
        $chain = [];

        foreach ($relationshipSegments as $relationshipSegment) {
            $relationship = Relation::noConstraints(fn () => $currentModel->{$relationshipSegment}());

            $this->validateRelationshipType($relationship);

            $chain[] = $relationship;

            $currentModel = $relationship->getRelated();
        }

        return $chain;
    }

    protected function validateRelationshipType(Relation $relationship): void
    {
        if ($relationship instanceof BelongsTo || $relationship instanceof HasOne || $relationship instanceof MorphOne || $relationship instanceof BelongsToThrough || $relationship instanceof HasOneThrough) {
            return;
        }

        throw new InvalidArgumentException(
            'Nested sorting only supports [BelongsTo], [HasOne], [MorphOne], [BelongsToThrough], and [HasOneThrough] relationships, [' . $relationship::class . '] found.'
        );
    }
}
