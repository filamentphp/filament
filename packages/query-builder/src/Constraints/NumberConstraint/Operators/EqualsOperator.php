<?php

namespace Filament\QueryBuilder\Constraints\NumberConstraint\Operators;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Schemas\Components\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class EqualsOperator extends Operator
{
    use Concerns\CanAggregateRelationships;

    public function getName(): string
    {
        return 'equals';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.number.equals.label.inverse' :
                'filament-query-builder::query-builder.operators.number.equals.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.number.equals.summary.inverse' :
                'filament-query-builder::query-builder.operators.number.equals.summary.direct',
            [
                'attribute' => $this->getAttributeLabel(),
                'number' => Number::format($this->getSettings()['number']),
            ],
        );
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getFormSchema(): array
    {
        return [
            TextInput::make('number')
                ->label(__('filament-query-builder::query-builder.operators.number.form.number.label'))
                ->numeric()
                ->integer($this->getConstraint()->isInteger())
                ->required(),
            $this->getAggregateSelect(),
        ];
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        if (filled($this->getAggregate())) {
            $operator = $this->isInverse() ? '!=' : '=';
            $value = floatval($this->getSettings()['number']);

            return $this->applyAggregateComparison($query, $operator, $value);
        }

        return $query->where($qualifiedColumn, $this->isInverse() ? '!=' : '=', floatval($this->getSettings()['number']));
    }
}
