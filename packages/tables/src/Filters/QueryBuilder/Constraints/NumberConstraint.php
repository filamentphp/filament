<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints;

use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\IsMaxOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\IsMinOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\EqualsOperator;

class NumberConstraint extends Constraint
{
    /**
     * @var array<string, string>
     */
    protected array $existingAggregateAliases = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-variable');

        $this->operators([
            IsMinOperator::class,
            IsMaxOperator::class,
            EqualsOperator::class,
            IsFilledOperator::class,
        ]);
    }

    public function reportAggregateAlias(string $alias): static
    {
        $this->existingAggregateAliases[$alias] = $alias;

        return $this;
    }

    public function isExistingAggregateAlias(string $alias): bool
    {
        return array_key_exists($alias, $this->existingAggregateAliases);
    }
}
