<?php

namespace Filament\Support\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RelationshipJoiner
{
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
                // Regular orders: { column: string, direction: 'asc' | 'desc' }
                // Sub-query orders: { column: Illuminate\Database\Query\Expression, direction: 'asc' | 'desc' }
                // Raw orders: { type: 'Raw', sql: string }
                if (! array_key_exists('column', $order) && ! array_key_exists('sql', $order)) {
                    continue;
                }

                if (array_key_exists('type', $order) && $order['type'] === 'Raw' && preg_match('/\b(asc|desc)\b/i', $order['sql'])) {
                    continue;
                }

                $columnValue = $order['column'] ?? new Expression($order['sql']);

                if (
                    $columnValue instanceof Expression
                    && str($columnValue->getValue($relationship->getGrammar()))->contains('?')
                ) {
                    // Heuristic to determine if the expression contains (a) binding(s), if so, as of
                    // yet we cannot reliably determine (which) bindings are used in the expression.
                    continue;
                }

                if (
                    str($columnValue instanceof Expression ? $columnValue->getValue($relationship->getGrammar()) : $columnValue)
                        ->startsWith("{$relationshipQuery->getModel()->getTable()}.")
                ) {
                    continue;
                }

                $relationshipQuery->addSelect($columnValue);
            }
        }

        return $relationshipQuery;
    }
}
