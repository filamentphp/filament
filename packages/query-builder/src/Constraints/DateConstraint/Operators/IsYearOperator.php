<?php

namespace Filament\QueryBuilder\Constraints\DateConstraint\Operators;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\QueryBuilder\Constraints\DateConstraint;
use Filament\QueryBuilder\Constraints\DateConstraint\Operators\Concerns\CanQueryDateRanges;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Schemas\Components\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;

class IsYearOperator extends Operator
{
    use CanQueryDateRanges;

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

        $constraint = $this->getConstraint();

        if (($constraint instanceof DateConstraint) && (! $constraint->shouldQueryDatesUsingRanges())) {
            return $query->whereYear($qualifiedColumn, $this->isInverse() ? '!=' : '=', $year);
        }

        // Keep extraction for offset-sensitive drivers and years outside the portable date range.
        $databaseConnection = $query->getConnection();

        if (((! ($databaseConnection instanceof MySqlConnection)) && (! ($databaseConnection instanceof PostgresConnection))) || ($year < 1000) || ($year > 9998)) {
            return $query->whereYear($qualifiedColumn, $this->isInverse() ? '!=' : '=', $year);
        }

        return $this->whereBetweenDates($query, $qualifiedColumn, sprintf('%04d-01-01', $year), sprintf('%04d-01-01', $year + 1), isInverse: (bool) $this->isInverse());
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
