<?php

namespace Filament\Support\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\JoinClause;
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

    public function getConvertedQuery(Relation $relationship): Builder
    {
        ray($relationship);

        $relationshipQuery = $relationship->getQuery();

        // By default, `BelongsToMany` relationships use an inner join to scope the results to only
        // those that are attached in the pivot table. We need to change this to a left join so
        // that we can still get results when the relationship is not attached to the record.
        if ($relationship instanceof BelongsToMany) {
            /** @var ?JoinClause $firstRelationshipJoinClause */
            $firstRelationshipJoinClause = $relationshipQuery->getQuery()->joins[0] ?? null;

            if ($firstRelationshipJoinClause) {
                $firstRelationshipJoinClause->type = 'left';

                // We need to move any where clauses (which will be from pivotWhere et al) in to the join's where clauses
                // so they are only filtering the join itself, not the entire result set, and the final query looks like ...
                //   SELECT table.* FROM table LEFT JOIN pivot_table ON table.id = pivot.parent_id AND [pivot clauses] WHERE [query clauses]
                // ... instead of ...
                //   SELECT table.* FROM table LEFT JOIN pivot_table ON table.id = pivot.parent_id WHERE [pivot clauses] AND [query clauses]

                $relationshipQueryWheres = $relationshipQuery->getQuery()->wheres;

                if (filled($relationshipQueryWheres)) {
                    $firstRelationshipJoinClause->wheres   = array_merge($firstRelationshipJoinClause->wheres, $relationshipQueryWheres);
                    $relationshipQuery->getQuery()->wheres = [];
                }
            }

            $relationshipQuery->select($relationshipQuery->getModel()->getTable() . '.*');
        }

        return $relationshipQuery;
    }
}
