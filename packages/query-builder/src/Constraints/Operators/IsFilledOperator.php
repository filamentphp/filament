<?php

namespace Filament\QueryBuilder\Constraints\Operators;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;

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
                    'filament-query-builder::query-builder.operators.is_filled.label.inverse' :
                    'filament-query-builder::query-builder.operators.is_filled.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                    'filament-query-builder::query-builder.operators.is_filled.summary.inverse' :
                    'filament-query-builder::query-builder.operators.is_filled.summary.direct',
            ['attribute' => $this->getConstraint()->getAttributeLabel()],
        );
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $qualifiedStringColumn = $qualifiedColumn;

        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $isPostgres = $databaseConnection->getDriverName() === 'pgsql';

        if ((Str::lower($qualifiedColumn) !== $qualifiedColumn) && $isPostgres) {
            $qualifiedColumn = (string) str($qualifiedColumn)->wrap('"');
        }

        if ($isPostgres) {
            $qualifiedStringColumn = new Expression("{$qualifiedColumn}::text");
        }

        return $query->where(
            fn (Builder $query) => $query
                ->{$this->isInverse() ? 'whereNull' : 'whereNotNull'}($qualifiedColumn)
                ->{$this->isInverse() ? 'orWhere' : 'whereNot'}($qualifiedStringColumn, ''),
        );
    }
}
