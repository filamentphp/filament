<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class IsMinOperator extends Operator
{
    use Concerns\CanAggregateRelationships;

    public function getName(): string
    {
        return 'isMin';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Is less than' : 'Is minimum';
    }

    /**
     * @return array<Component>
     */
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
        return $this->isInverse() ? "{$this->getAttributeLabel()} is less than {$this->getSettings()['number']}" : "{$this->getAttributeLabel()} is minimum {$this->getSettings()['number']}";
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->where($this->replaceQualifiedColumnWithQualifiedAggregateColumn($qualifiedColumn), $this->isInverse() ? '<' : '>=', floatval($this->getSettings()['number']));
    }
}
