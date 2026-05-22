<?php

namespace Filament\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use Filament\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;

class IsEmptyOperator extends Operator
{
    public function getName(): string
    {
        return 'isEmpty';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.relationship.is_empty.label.inverse' :
                'filament-query-builder::query-builder.operators.relationship.is_empty.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.relationship.is_empty.summary.inverse' :
                'filament-query-builder::query-builder.operators.relationship.is_empty.summary.direct',
            ['relationship' => $this->getConstraint()->getAttributeLabel()],
        );
    }

    public function applyToBaseQuery(Builder $query): Builder
    {
        $relationshipName = $this->getConstraint()->getRelationshipName();
        $modifyRelationshipQueryUsing = $this->getConstraint()->getModifyRelationshipQueryUsing();

        $scopeCallback = function (Builder $query) use ($modifyRelationshipQueryUsing): Builder {
            if ($modifyRelationshipQueryUsing) {
                $query = $this->evaluate($modifyRelationshipQueryUsing, [
                    'query' => $query,
                ]) ?? $query;
            }

            return $query;
        };

        return $this->isInverse()
            ? $query->has($relationshipName, '>=', 1, 'and', $scopeCallback)
            : $query->doesntHave($relationshipName, 'and', $scopeCallback);
    }
}
