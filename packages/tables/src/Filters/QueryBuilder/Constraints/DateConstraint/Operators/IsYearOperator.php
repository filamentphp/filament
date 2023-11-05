<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;

class IsYearOperator extends Operator
{
    public function getName(): string
    {
        return 'isYear';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Is not year' : 'Is year';
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        return [
            TextInput::make('year')
                ->integer()
                ->required(),
        ];
    }

    public function getSummary(): string
    {
        return $this->isInverse() ? "{$this->getConstraint()->getAttributeLabel()} is not {$this->getSettings()['year']}" : "{$this->getConstraint()->getAttributeLabel()} is {$this->getSettings()['year']}";
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->whereYear($qualifiedColumn, $this->isInverse() ? '!=' : '=', $this->getSettings()['year']);
    }
}
