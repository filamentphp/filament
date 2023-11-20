<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints;

use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsAfterOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsBeforeOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsDateOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsMonthOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsYearOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;

class DateConstraint extends Constraint
{
    use Concerns\CanBeNullable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(FilamentIcon::resolve('tables::filters.query-builder.constraints.date') ?? 'heroicon-m-calendar');

        $this->operators([
            IsAfterOperator::class,
            IsBeforeOperator::class,
            IsDateOperator::class,
            IsMonthOperator::class,
            IsYearOperator::class,
            IsFilledOperator::make()
                ->visible(fn (): bool => $this->isNullable()),
        ]);
    }
}
