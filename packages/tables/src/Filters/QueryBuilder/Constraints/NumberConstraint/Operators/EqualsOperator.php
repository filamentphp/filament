<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class EqualsOperator extends Operator
{
    use Concerns\CanAggregateRelationships;

    public function getName(): string
    {
        return 'equals';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Does not equal' : 'Equals';
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('number')
                ->numeric()
                ->required(),
            $this->getAggregateSelect(),
        ];
    }

    public function getSummary(): string
    {
        return $this->isInverse() ? "{$this->getAttributeLabel()} does not equal {$this->getSettings()['number']}" : "{$this->getAttributeLabel()} equals {$this->getSettings()['number']}";
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->where($this->replaceQualifiedColumnWithQualifiedAggregateColumn($qualifiedColumn), $this->isInverse() ? '!=' : '=', floatval($this->getSettings()['number']));
    }
}
