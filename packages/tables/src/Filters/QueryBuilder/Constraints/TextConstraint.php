<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints;

use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\EndsWithOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\EqualsOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\StartsWithOperator;

class TextConstraint extends Constraint
{
    use Concerns\CanBeNullable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(FilamentIcon::resolve('tables::filters.query-builder.constraints.text') ?? 'heroicon-m-language');

        $this->operators([
            ContainsOperator::class,
            StartsWithOperator::class,
            EndsWithOperator::class,
            EqualsOperator::class,
            IsFilledOperator::make()
                ->visible(fn (): bool => $this->isNullable()),
        ]);
    }
}
