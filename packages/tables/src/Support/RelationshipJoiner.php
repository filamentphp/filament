<?php

namespace Filament\Tables\Support;

use Illuminate\Database\Eloquent\Builder;
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
}
