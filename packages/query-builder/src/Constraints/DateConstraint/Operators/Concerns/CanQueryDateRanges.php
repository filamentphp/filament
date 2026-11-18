<?php

namespace Filament\QueryBuilder\Constraints\DateConstraint\Operators\Concerns;

use Carbon\Exceptions\InvalidFormatException;
use Filament\QueryBuilder\Constraints\DateConstraint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Support\Carbon;

trait CanQueryDateRanges
{
    /**
     * @param  '=' | '!=' | '<' | '<=' | '>' | '>='  $operator
     */
    protected function whereDateUsingRange(Builder $query, string $column, string $operator, string $date): Builder
    {
        $constraint = $this->getConstraint();

        if (($constraint instanceof DateConstraint) && (! $constraint->shouldQueryDatesUsingRanges())) {
            return $query->whereDate($column, $operator, $date);
        }

        // SQLite normalizes stored offsets to UTC; SQL Server preserves their local date.
        // Neither is equivalent to comparing every column value with the same midnight.
        $databaseConnection = $query->getConnection();

        if ((! ($databaseConnection instanceof MySqlConnection)) && (! ($databaseConnection instanceof PostgresConnection))) {
            return $query->whereDate($column, $operator, $date);
        }

        try {
            $day = Carbon::parse($date, 'UTC');
        } catch (InvalidFormatException) {
            return $query->whereDate($column, $operator, $date);
        }

        // Preserve the database's interpretation of nonstandard dates and avoid overflowing its date range.
        if (($day->toDateString() !== $date) || ($day->year < 1000) || ($day->year > 9998)) {
            return $query->whereDate($column, $operator, $date);
        }

        $start = $day->toDateString();
        $nextDay = $day->copy()->addDay()->toDateString();

        return match ($operator) {
            '>=' => $query->where($column, '>=', $start),
            '<' => $query->where($column, '<', $start),
            '<=' => $query->where($column, '<', $nextDay),
            '>' => $query->where($column, '>=', $nextDay),
            '=' => $this->whereBetweenDates($query, $column, $start, $nextDay),
            '!=' => $this->whereBetweenDates($query, $column, $start, $nextDay, isInverse: true),
        };
    }

    /**
     * Rows from the start of `$from` up to, but not including, the start of `$until` (or, inverted, the rest).
     */
    protected function whereBetweenDates(Builder $query, string $column, string $from, string $until, bool $isInverse = false): Builder
    {
        return $query->where(static fn (Builder $query): Builder => $isInverse
            ? $query->where($column, '<', $from)->orWhere($column, '>=', $until)
            : $query->where($column, '>=', $from)->where($column, '<', $until));
    }
}
