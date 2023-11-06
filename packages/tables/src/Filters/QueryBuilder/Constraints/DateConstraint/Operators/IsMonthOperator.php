<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;

class IsMonthOperator extends Operator
{
    public function getName(): string
    {
        return 'isMonth';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.date.is_month.label.inverse' :
                'filament-tables::filters/query-builder.operators.date.is_month.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.date.is_month.summary.inverse' :
                'filament-tables::filters/query-builder.operators.date.is_month.summary.direct',
            [
                'attribute' => $this->getConstraint()->getAttributeLabel(),
                'month' => $this->getMonths()[$this->getSettings()['month']] ?? null,
            ],
        );
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        return [
            Select::make('month')
                ->label(__('filament-tables::filters/query-builder.operators.date.form.month.label'))
                ->options($this->getMonths())
                ->required(),
        ];
    }

    /**
     * @return array<string>
     */
    protected function getMonths(): array
    {
        return collect(range(1, 12))
            ->mapWithKeys(fn (int $month): array => [
                $month => now()->setMonth($month)->getTranslatedMonthName(),
            ])
            ->all();
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->whereMonth($qualifiedColumn, $this->isInverse() ? '!=' : '=', $this->getSettings()['month']);
    }
}
