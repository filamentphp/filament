<?php

namespace Filament\QueryBuilder\Constraints;

use Closure;
use Filament\QueryBuilder\Constraints\DateConstraint\Operators\IsAfterOperator;
use Filament\QueryBuilder\Constraints\DateConstraint\Operators\IsBeforeOperator;
use Filament\QueryBuilder\Constraints\DateConstraint\Operators\IsDateOperator;
use Filament\QueryBuilder\Constraints\DateConstraint\Operators\IsMonthOperator;
use Filament\QueryBuilder\Constraints\DateConstraint\Operators\IsYearOperator;
use Filament\QueryBuilder\Constraints\Operators\IsFilledOperator;
use Filament\QueryBuilder\View\QueryBuilderIconAlias;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;

class DateConstraint extends Constraint
{
    use Concerns\CanBeNullable;

    protected bool | Closure $hasTime = false;

    public function time(bool | Closure $condition = true): static
    {
        $this->hasTime = $condition;

        return $this;
    }

    public function hasTime(): bool
    {
        return (bool) $this->evaluate($this->hasTime);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(FilamentIcon::resolve(QueryBuilderIconAlias::CONSTRAINTS_DATE) ?? Heroicon::Calendar);

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
