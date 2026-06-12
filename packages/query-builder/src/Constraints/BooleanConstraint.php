<?php

namespace Filament\QueryBuilder\Constraints;

use Filament\QueryBuilder\Constraints\BooleanConstraint\Operators\IsTrueOperator;
use Filament\QueryBuilder\Constraints\Operators\IsFilledOperator;
use Filament\QueryBuilder\View\QueryBuilderIconAlias;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;

class BooleanConstraint extends Constraint
{
    use Concerns\CanBeNullable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(FilamentIcon::resolve(QueryBuilderIconAlias::CONSTRAINTS_BOOLEAN) ?? Heroicon::CheckCircle);

        $this->operators([
            IsTrueOperator::class,
            IsFilledOperator::make()
                ->visible(fn (): bool => $this->isNullable()),
        ]);
    }
}
