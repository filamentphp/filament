<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints;

use Closure;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\EqualsOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\IsMaxOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\IsMinOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator;

class NumberConstraint extends Constraint
{
    use Concerns\CanBeNullable;

    /**
     * @var array<string, string>
     */
    protected array $existingAggregateAliases = [];

    protected bool | Closure $isInteger = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(FilamentIcon::resolve('tables::filters.query-builder.constraints.number') ?? 'heroicon-m-variable');

        $this->operators([
            IsMinOperator::class,
            IsMaxOperator::class,
            EqualsOperator::class,
            IsFilledOperator::make()
                ->visible(fn (): bool => $this->isNullable()),
        ]);
    }

    public function integer(bool | Closure $condition = true): static
    {
        $this->isInteger = $condition;

        return $this;
    }

    public function isInteger(): bool
    {
        return (bool) $this->evaluate($this->isInteger);
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
