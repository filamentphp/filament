<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\Operators;

use Illuminate\Database\Connection;
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
        return __(
            $this->isInverse() ?
                    'filament-tables::filters/query-builder.operators.is_filled.label.inverse' :
                    'filament-tables::filters/query-builder.operators.is_filled.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                    'filament-tables::filters/query-builder.operators.is_filled.summary.inverse' :
                    'filament-tables::filters/query-builder.operators.is_filled.summary.direct',
            ['attribute' => $this->getConstraint()->getAttributeLabel()],
        );
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $qualifiedStringColumn = $qualifiedColumn;

        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        if ($databaseConnection->getDriverName() === 'pgsql') {
            $qualifiedStringColumn = new Expression("{$qualifiedColumn}::text");
        }

        return $query->where(
            fn (Builder $query) => $query
                ->{$this->isInverse() ? 'whereNull' : 'whereNotNull'}($qualifiedColumn)
                ->{$this->isInverse() ? 'orWhere' : 'whereNot'}($qualifiedStringColumn, ''),
        );
    }
}
