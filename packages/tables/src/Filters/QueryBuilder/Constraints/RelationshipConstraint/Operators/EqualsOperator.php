<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;

class EqualsOperator extends Operator
{
    public function getName(): string
    {
        return 'equals';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.relationship.equals.label.inverse' :
                'filament-tables::filters/query-builder.operators.relationship.equals.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.relationship.equals.summary.inverse' :
                'filament-tables::filters/query-builder.operators.relationship.equals.summary.direct',
            [
                'relationship' => $this->getConstraint()->getAttributeLabel(),
                'count' => $this->getSettings()['count'],
            ],
        );
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        return [
            TextInput::make('count')
                ->label(__('filament-tables::filters/query-builder.operators.relationship.form.count.label'))
                ->numeric()
                ->required()
                ->minValue(1),
        ];
    }

    public function applyToBaseQuery(Builder $query): Builder
    {
        return $query->has($this->getConstraint()->getRelationshipName(), $this->isInverse() ? '!=' : '=', intval($this->getSettings()['count']));
    }
}
