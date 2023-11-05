<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class HasMaxOperator extends Operator
{
    public function getName(): string
    {
        return 'hasMax';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Has more than' : 'Has maximum';
    }

    /**
     * @return array<Component>
     */
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
        return $this->isInverse() ? "Has more than {$this->getSettings()['count']} {$this->getConstraint()->getAttributeLabel()}" : "Has maximum {$this->getSettings()['count']} {$this->getConstraint()->getAttributeLabel()}";
    }

    public function applyToBaseQuery(Builder $query): Builder
    {
        return $query->has($this->getConstraint()->getRelationshipName(), $this->isInverse() ? '>' : '<=', intval($this->getSettings()['count']));
    }
}
