<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class EqualsOperator extends Operator
{
    public function getName(): string
    {
        return 'equals';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Does not have' : 'Has';
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('count')
                ->integer()
                ->required()
                ->minValue(1),
        ];
    }

    public function getSummary(): string
    {
        return $this->isInverse() ? "Does not have {$this->getSettings()['count']} {$this->getConstraint()->getAttributeLabel()}" : "Has {$this->getSettings()['count']} {$this->getConstraint()->getAttributeLabel()}";
    }

    public function applyToBaseQuery(Builder $query): Builder
    {
        return $query->has($this->getConstraint()->getRelationshipName(), $this->isInverse() ? '!=' : '=', intval($this->getSettings()['count']));
    }
}
