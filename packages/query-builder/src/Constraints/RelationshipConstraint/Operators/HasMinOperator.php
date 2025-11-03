<?php

namespace Filament\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Schemas\Components\Component;
use Illuminate\Database\Eloquent\Builder;

class HasMinOperator extends Operator
{
    public function getName(): string
    {
        return 'hasMin';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.relationship.has_min.label.inverse' :
                'filament-query-builder::query-builder.operators.relationship.has_min.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.relationship.has_min.summary.inverse' :
                'filament-query-builder::query-builder.operators.relationship.has_min.summary.direct',
            [
                'relationship' => $this->getConstraint()->getAttributeLabel(),
                'count' => $this->getSettings()['count'],
            ],
        );
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getFormSchema(): array
    {
        return [
            TextInput::make('count')
                ->label(__('filament-query-builder::query-builder.operators.relationship.form.count.label'))
                ->numeric()
                ->required()
                ->minValue(1),
        ];
    }

    public function applyToBaseQuery(Builder $query): Builder
    {
        return $query->has($this->getConstraint()->getRelationshipName(), $this->isInverse() ? '<' : '>=', intval($this->getSettings()['count']));
    }
}
