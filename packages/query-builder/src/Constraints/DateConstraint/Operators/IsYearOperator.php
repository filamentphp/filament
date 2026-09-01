<?php

namespace Filament\QueryBuilder\Constraints\DateConstraint\Operators;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Schemas\Components\Component;
use Illuminate\Database\Eloquent\Builder;

class IsYearOperator extends Operator
{
    public function getName(): string
    {
        return 'isYear';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.date.is_year.label.inverse' :
                'filament-query-builder::query-builder.operators.date.is_year.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.date.is_year.summary.inverse' :
                'filament-query-builder::query-builder.operators.date.is_year.summary.direct',
            [
                'attribute' => $this->getConstraint()->getAttributeLabel(),
                'year' => $this->getYearSetting(),
            ],
        );
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getFormSchema(): array
    {
        return [
            TextInput::make('year')
                ->label(__('filament-query-builder::query-builder.operators.date.form.year.label'))
                ->integer()
                ->required(),
        ];
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $year = $this->getYearSetting();

        // Security: skip applying the constraint when the tampered setting is not a valid year.
        if ($year === null) {
            return $query;
        }

        return $query->whereYear($qualifiedColumn, $this->isInverse() ? '!=' : '=', $year);
    }

    protected function getYearSetting(): ?int
    {
        $year = $this->getSettings()['year'] ?? null;

        // Security: settings arrive from the request payload and can be tampered with, so a
        // non-numeric value (e.g. an array) must not reach `whereYear()` where it would throw.
        // Fail closed by returning `null` instead.
        if (! is_numeric($year)) {
            return null;
        }

        return (int) $year;
    }
}
