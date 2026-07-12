<?php

namespace Filament\QueryBuilder\Constraints\DateConstraint\Operators;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Schemas\Components\Component;
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
                'filament-query-builder::query-builder.operators.date.is_month.label.inverse' :
                'filament-query-builder::query-builder.operators.date.is_month.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.date.is_month.summary.inverse' :
                'filament-query-builder::query-builder.operators.date.is_month.summary.direct',
            [
                'attribute' => $this->getConstraint()->getAttributeLabel(),
                'month' => $this->getMonths()[$this->getMonthSetting()] ?? null,
            ],
        );
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getFormSchema(): array
    {
        return [
            Select::make('month')
                ->label(__('filament-query-builder::query-builder.operators.date.form.month.label'))
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
                $month => now()->setMonth($month)->setDay(1)->getTranslatedMonthName(),
            ])
            ->all();
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $month = $this->getMonthSetting();

        // Security: skip applying the constraint when the tampered setting is not a valid month.
        if ($month === null) {
            return $query;
        }

        return $query->whereMonth($qualifiedColumn, $this->isInverse() ? '!=' : '=', $month);
    }

    protected function getMonthSetting(): ?int
    {
        $month = $this->getSettings()['month'] ?? null;

        // Security: settings arrive from the request payload and can be tampered with, so a
        // non-scalar value (e.g. an array) must not reach `whereMonth()` or the `getMonths()`
        // offset lookup where it would throw. Fail closed by returning `null` for any value
        // that is not a real calendar month.
        if (! is_scalar($month)) {
            return null;
        }

        $month = (int) $month;

        if (($month < 1) || ($month > 12)) {
            return null;
        }

        return $month;
    }
}
