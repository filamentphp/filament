<?php

namespace Filament\QueryBuilder\Constraints;

use Closure;
use Filament\QueryBuilder\Constraints\RelationshipConstraint\Operators\EqualsOperator;
use Filament\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMaxOperator;
use Filament\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMinOperator;
use Filament\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsEmptyOperator;
use Filament\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\QueryBuilder\View\QueryBuilderIconAlias;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;

class RelationshipConstraint extends Constraint
{
    protected bool | Closure $isMultiple = false;

    protected bool | Closure | null $canBeEmpty = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(FilamentIcon::resolve(QueryBuilderIconAlias::CONSTRAINTS_RELATIONSHIP) ?? Heroicon::ArrowsPointingOut);

        $this->operators([
            HasMinOperator::make()
                ->visible(fn (): bool => $this->isMultiple()),
            HasMaxOperator::make()
                ->visible(fn (): bool => $this->isMultiple()),
            EqualsOperator::make()
                ->visible(fn (): bool => $this->isMultiple()),
            IsEmptyOperator::make()
                ->visible(fn (): bool => $this->canBeEmpty()),
        ]);
    }

    public function selectable(IsRelatedToOperator $operator): static
    {
        $this->unshiftOperators([$operator]);

        return $this;
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function emptyable(bool | Closure | null $condition = true): static
    {
        $this->canBeEmpty = $condition;

        return $this;
    }

    public function canBeEmpty(): bool
    {
        return $this->evaluate($this->canBeEmpty) ?? $this->isMultiple();
    }
}
