<?php

namespace Filament\Support\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Kirschbaum\PowerJoins\PowerJoins;

class RelationshipJoiner
{
    use PowerJoins;

    public function leftJoinRelationship(Builder $query, string $relationship): Builder
    {
        if (str($relationship)->contains('.')) {
            $this->scopeJoinNestedRelationship(
                $query,
                $relationship,
                joinType: static::$joinMethodsMap['leftJoin'] ?? 'leftJoin',
            );

            return $query;
        }

        $this->scopeJoinRelationship(
            $query,
            $relationship,
            joinType: 'leftJoin',
        );

        return $query;
    }

    /**
     * @return array<JoinClause>
     */
    public function getLeftJoinsForRelationship(Builder $query, string $relationship): array
    {
        $this->leftJoinRelationship($query, $relationship);

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

                // We need to move any where clauses added by pivotWhere() et al in to the join's where clauses
                // so they are only filtering the join itself, not the entire result set, and the final query looks like ...
                //   SELECT table.* FROM table LEFT JOIN pivot_table ON table.id = pivot.parent_id AND [pivot clauses] WHERE [query clauses]
                // ... instead of ...
                //   SELECT table.* FROM table LEFT JOIN pivot_table ON table.id = pivot.parent_id WHERE [pivot clauses] AND [query clauses]
                // As far as I can tell, all clauses added with wherePivot and orWherePivot, and variants thereof, are always
                // "Basic" with a column.  Anything added to a relationship with where(), rather than wherePivot(), will be a
                // nested where (so no 'column').  So we don't have to check anything that doesn't have a 'column' attribute.
                $relationshipQueryWheres = Arr::where(
                    $relationshipQuery->getQuery()->wheres,
                    fn (array $where) => array_key_exists('column', $where)
                        && Str::startsWith($where['column'], $relationship->getTable() . '.')
                );

                if (filled($relationshipQueryWheres)) {
                    $firstRelationshipJoinClause->wheres = array_merge($firstRelationshipJoinClause->wheres, $relationshipQueryWheres);
                    $relationshipQuery->getQuery()->wheres = Arr::except($relationshipQuery->getQuery()->wheres, array_keys($relationshipQueryWheres));
                }
            }

            $relationshipQuery->select($relationshipQuery->getModel()->getTable() . '.*');
        }

        return $relationshipQuery;
    }
}
