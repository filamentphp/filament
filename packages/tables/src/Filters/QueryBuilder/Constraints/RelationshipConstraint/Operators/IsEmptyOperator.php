<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
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
                'filament-tables::filters/query-builder.operators.relationship.is_empty.label.inverse' :
                'filament-tables::filters/query-builder.operators.relationship.is_empty.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.relationship.is_empty.summary.inverse' :
                'filament-tables::filters/query-builder.operators.relationship.is_empty.summary.direct',
            ['relationship' => $this->getConstraint()->getAttributeLabel()],
        );
    }

    public function applyToBaseQuery(Builder $query): Builder
    {
        return $query->{$this->isInverse() ? 'has' : 'doesntHave'}($this->getConstraint()->getRelationshipName());
    }
}
