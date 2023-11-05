<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints;

use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsAfterOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsBeforeOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsDateOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsMonthOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsYearOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;

class DateConstraint extends Constraint
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-calendar');

        $this->operators([
            IsAfterOperator::class,
            IsBeforeOperator::class,
            IsDateOperator::class,
            IsMonthOperator::class,
            IsYearOperator::class,
            IsFilledOperator::class,
        ]);
    }
}
