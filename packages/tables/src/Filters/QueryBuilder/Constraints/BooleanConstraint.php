<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints;

use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint\Operators\IsTrueOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;
use Filament\Tables\View\TablesIconAlias;

class BooleanConstraint extends Constraint
{
    use Concerns\CanBeNullable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(FilamentIcon::resolve(TablesIconAlias::FILTERS_QUERY_BUILDER_CONSTRAINTS_BOOLEAN) ?? Heroicon::CheckCircle);

        $this->operators([
            IsTrueOperator::class,
            IsFilledOperator::make()
                ->visible(fn (): bool => $this->isNullable()),
        ]);
    }
}
