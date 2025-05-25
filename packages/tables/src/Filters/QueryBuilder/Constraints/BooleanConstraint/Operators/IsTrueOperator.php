<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint\Operators;

use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class IsTrueOperator extends Operator
{
    public function getName(): string
    {
        return 'isTrue';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.boolean.is_true.label.inverse' :
                'filament-tables::filters/query-builder.operators.boolean.is_true.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.boolean.is_true.summary.inverse' :
                'filament-tables::filters/query-builder.operators.boolean.is_true.summary.direct',
            ['attribute' => $this->getConstraint()->getAttributeLabel()],
        );
    }

    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->where($qualifiedColumn, ! $this->isInverse());
    }
}
