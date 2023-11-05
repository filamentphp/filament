<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\Operators;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

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
        $qualifiedStringColumn = $qualifiedColumn;

        if ($query->getConnection()->getDriverName() === 'pgsql') {
            $qualifiedStringColumn = new Expression("{$qualifiedColumn}::text");
        }

        return $query->where(
            fn (Builder $query) => $query
                ->{$this->isInverse() ? 'whereNull' : 'whereNotNull'}($qualifiedColumn)
                ->{$this->isInverse() ? 'where' : 'whereNot'}($qualifiedStringColumn, ''),
        );
    }
}
