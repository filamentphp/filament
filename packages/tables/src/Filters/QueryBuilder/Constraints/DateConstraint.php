<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints;

use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsAfterOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsBeforeOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsDateOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsMonthOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsYearOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;
use Filament\Tables\View\TablesIconAlias;

class DateConstraint extends Constraint
{
    use Concerns\CanBeNullable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(FilamentIcon::resolve(TablesIconAlias::FILTERS_QUERY_BUILDER_CONSTRAINTS_DATE) ?? Heroicon::Calendar);

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
