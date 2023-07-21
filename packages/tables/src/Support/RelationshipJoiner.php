<?php

namespace Filament\Tables\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
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
}
