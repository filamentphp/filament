<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;

use function Filament\Support\format_number;

class IsMaxOperator extends Operator
{
    use Concerns\CanAggregateRelationships;

    public function getName(): string
    {
        return 'isMax';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.number.is_max.label.inverse' :
                'filament-tables::filters/query-builder.operators.number.is_max.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.number.is_max.summary.inverse' :
                'filament-tables::filters/query-builder.operators.number.is_max.summary.direct',
            [
                'attribute' => $this->getAttributeLabel(),
                'number' => format_number($this->getSettings()['number']),
            ],
        );
    }

    /**
     * @return array<Component>
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
        return $query->where($this->replaceQualifiedColumnWithQualifiedAggregateColumn($qualifiedColumn), $this->isInverse() ? '>' : '<=', floatval($this->getSettings()['number']));
    }
}
