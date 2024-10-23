<?php

namespace Filament\Support\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Kirschbaum\PowerJoins\JoinsHelper;

class RelationshipJoiner
{
    public function leftJoinRelationship(Builder $query, string $relationship): Builder
    {
        if (str($relationship)->contains('.')) {
            /** @phpstan-ignore-next-line */
            $query->joinNestedRelationship(
                $relationship,
                callback: null,
                joinType: JoinsHelper::$joinMethodsMap['leftJoin'] ?? 'leftJoin',
            );

            return $query;
        }

        /** @phpstan-ignore-next-line */
        $query->joinRelationship(
            $relationship,
            callback: null,
            joinType: 'leftJoin',
        );

        return $query;
    }

    /**
     * @return array<JoinClause>
     */
    public function getLeftJoinsForRelationship(Builder $query, string $relationship): array
    {
        /** @phpstan-ignore-next-line */
        $query->leftJoinRelationship($relationship);

        return $query->toBase()->joins;
    }

    public function prepareQueryForNoConstraints(Relation $relationship): Builder
    {
        $relationshipQuery = $relationship->getQuery();

        // By default, `BelongsToMany` relationships use an inner join to scope the results to only
        // those that are attached in the pivot table. We need to change this to a left join so
        // that we can still get results when the relationship is not attached to the record.
        if ($relationship instanceof BelongsToMany) {
            /** @var ?JoinClause $firstRelationshipJoinClause */
            $firstRelationshipJoinClause = $relationshipQuery->getQuery()->joins[0] ?? null;

            if ($firstRelationshipJoinClause) {
                $firstRelationshipJoinClause->type = 'left';

                // Any "where" clauses that are scoped to the pivot table need to be moved to the join.
                // It's expected that any scopes that don't apply to the pivot table do not have
                // a `column` attribute set.
                $relationshipQueryPivotWheres = Arr::where(
                    $relationshipQuery->getQuery()->wheres,
                    function (array $where) use ($relationship): bool {
                        if (! array_key_exists('column', $where)) {
                            return false;
                        }

                        return Str::startsWith($where['column'], "{$relationship->getTable()}.");
                    },
                );

                $firstRelationshipJoinClause->wheres = array_merge(
                    $firstRelationshipJoinClause->wheres,
                    $relationshipQueryPivotWheres,
                );

                $relationshipQuery->getQuery()->wheres = Arr::except(
                    $relationshipQuery->getQuery()->wheres,
                    array_keys($relationshipQueryPivotWheres),
                );
            }

            $relationshipQuery
                ->distinct()
                ->select($relationshipQuery->getModel()->getTable() . '.*');

            /** @phpstan-ignore-next-line */
            foreach (($relationshipQuery->getQuery()->orders ?? []) as $order) {
                if (! array_key_exists('column', $order)) {
                    continue;
                }

                if (str($order['column'])->startsWith("{$relationshipQuery->getModel()->getTable()}.")) {
                    continue;
                }

                $relationshipQuery->addSelect($order['column']);
            }
        }

        return $relationshipQuery;
    }
}
