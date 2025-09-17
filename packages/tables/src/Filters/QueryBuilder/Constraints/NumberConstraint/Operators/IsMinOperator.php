<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class IsMinOperator extends Operator
{
    use Concerns\CanAggregateRelationships;

    public function getName(): string
    {
        return 'isMin';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.number.is_min.label.inverse' :
                'filament-tables::filters/query-builder.operators.number.is_min.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.number.is_min.summary.inverse' :
                'filament-tables::filters/query-builder.operators.number.is_min.summary.direct',
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
                ->label(__('filament-tables::filters/query-builder.operators.number.form.number.label'))
                ->numeric()
                ->integer($this->getConstraint()->isInteger())
                ->required(),
            $this->getAggregateSelect(),
        ];
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->where($this->replaceQualifiedColumnWithQualifiedAggregateColumn($qualifiedColumn), $this->isInverse() ? '<' : '>=', floatval($this->getSettings()['number']));
    }
}
