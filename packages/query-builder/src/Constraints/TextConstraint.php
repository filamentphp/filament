<?php

namespace Filament\QueryBuilder\Constraints;

use Filament\QueryBuilder\Constraints\Operators\IsFilledOperator;
use Filament\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator;
use Filament\QueryBuilder\Constraints\TextConstraint\Operators\EndsWithOperator;
use Filament\QueryBuilder\Constraints\TextConstraint\Operators\EqualsOperator;
use Filament\QueryBuilder\Constraints\TextConstraint\Operators\StartsWithOperator;
use Filament\QueryBuilder\View\QueryBuilderIconAlias;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;

class TextConstraint extends Constraint
{
    use Concerns\CanBeNullable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(FilamentIcon::resolve(QueryBuilderIconAlias::CONSTRAINTS_TEXT) ?? Heroicon::Language);

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
