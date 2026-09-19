<?php

namespace Filament\Support\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use function Filament\Support\is_database_driver_supported;

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
            $relationshipJoinClause = $this->getJoinForRelationship($relationshipQuery, $relationship);

            if ($relationshipJoinClause) {
                $relationshipJoinClause->type = 'left';

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

                $relationshipJoinClause->wheres = array_merge(
                    $relationshipJoinClause->wheres,
                    $relationshipQueryPivotWheres,
                );

                $relationshipQuery->getQuery()->wheres = Arr::except(
                    $relationshipQuery->getQuery()->wheres,
                    array_keys($relationshipQueryPivotWheres),
                );

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
        }

        return $relationshipQuery;
    }

    public function removeJoinForRelationship(Builder $query, BelongsToMany $relationship): Builder
    {
        $relationshipJoinClause = $this->getJoinForRelationship($query, $relationship);

        if (! $relationshipJoinClause) {
            return $query;
        }

        $query->getQuery()->joins = array_values(array_filter(
            $query->getQuery()->joins,
            static fn (JoinClause $joinClause): bool => $joinClause !== $relationshipJoinClause,
        ));

        return $query;
    }

    protected function getJoinForRelationship(Builder $query, BelongsToMany $relationship): ?JoinClause
    {
        foreach ($query->getQuery()->joins ?? [] as $joinClause) {
            if (
                ($joinClause instanceof JoinClause) &&
                ($joinClause->table === $relationship->getTable())
            ) {
                return $joinClause;
            }
        }

        return null;
    }

    public function getUnattachedQuery(BelongsToMany $relationship): Builder
    {
        $newUnattachedQueryMethod = 'newUnattachedQuery';

        if (
            (! is_database_driver_supported($relationship->getConnection())) &&
            method_exists($relationship, $newUnattachedQueryMethod)
        ) {
            /** @var Builder $query */
            $query = $relationship->{$newUnattachedQueryMethod}();

            return $query;
        }

        return $this->applyNotAttachedConstraint(
            $this->prepareQueryForNoConstraints($relationship),
            $relationship,
        );
    }

    protected function applyNotAttachedConstraint(Builder $query, BelongsToMany $relationship): Builder
    {
        return $query->whereNotExists(function (QueryBuilder $query) use ($relationship): void {
            $query
                ->select($relationship->getConnection()->raw(1))
                ->from($relationship->getTable())
                ->whereColumn(
                    $relationship->getQualifiedRelatedPivotKeyName(),
                    $relationship->getQualifiedRelatedKeyName(),
                )
                ->where(
                    $relationship->getQualifiedForeignPivotKeyName(),
                    $relationship->getParent()->getAttribute($relationship->getParentKeyName()),
                );

            if ($relationship instanceof MorphToMany) {
                $query->where(
                    $relationship->qualifyPivotColumn($relationship->getMorphType()),
                    $relationship->getMorphClass(),
                );
            }
        });
    }
}
