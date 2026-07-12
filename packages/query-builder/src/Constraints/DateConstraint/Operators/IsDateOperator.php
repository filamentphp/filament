<?php

namespace Filament\QueryBuilder\Constraints\DateConstraint\Operators;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Schemas\Components\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class IsDateOperator extends Operator
{
    public function getName(): string
    {
        return 'isDate';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.date.is_date.label.inverse' :
                'filament-query-builder::query-builder.operators.date.is_date.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.date.is_date.summary.inverse' :
                'filament-query-builder::query-builder.operators.date.is_date.summary.direct',
            [
                'attribute' => $this->getConstraint()->getAttributeLabel(),
                'date' => ($date = $this->getDateSetting('date')) === null ? null : Carbon::parse($date)->toFormattedDateString(),
            ],
        );
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getFormSchema(): array
    {
        return [
            DatePicker::make('date')
                ->label(__('filament-query-builder::query-builder.operators.date.form.date.label'))
                ->required(),
        ];
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $date = $this->getDateSetting('date');

        // Security: skip applying the constraint when the tampered setting is not a scalar date.
        if ($date === null) {
            return $query;
        }

        return $query->whereDate($qualifiedColumn, $this->isInverse() ? '!=' : '=', $date);
    }
}
