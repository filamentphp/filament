<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints;

use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint\Operators\IsTrueOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;

class BooleanConstraint extends Constraint
{
    use Concerns\CanBeNullable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(FilamentIcon::resolve('tables::filters.query-builder.constraints.boolean') ?? 'heroicon-m-check-circle');

        $this->operators([
            IsTrueOperator::class,
            IsFilledOperator::make()
                ->visible(fn (): bool => $this->isNullable()),
        ]);
    }
}
