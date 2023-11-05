<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint\Operators;

use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;

class IsFilledOperator extends Operator
{
    public function getName(): string
    {
        return 'isFilled';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Is blank' : 'Is filled';
    }

    public function getSummary(): string
    {
        return $this->isInverse() ? "{$this->getConstraint()->getAttributeLabel()} is blank" : "{$this->getConstraint()->getAttributeLabel()} is filled";
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->where(
            fn (Builder $query) => $query
                ->{$this->isInverse() ? 'whereNull' : 'whereNotNull'}($qualifiedColumn)
                ->{$this->isInverse() ? 'where' : 'whereNot'}($qualifiedColumn, ''),
        );
    }
}
