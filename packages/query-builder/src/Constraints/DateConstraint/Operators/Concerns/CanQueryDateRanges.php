<?php

namespace Filament\QueryBuilder\Constraints\DateConstraint\Operators\Concerns;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Compares a column with whole days by comparing it with midnight, instead of
 * wrapping it in `whereDate()` or `whereYear()`. A wrapped column (`date(column)`,
 * `extract(year from column)`) can't use an index on it, so every query scanned the
 * table; a range can. The rows matched are the same.
 */
trait CanQueryDateRanges
{
    /**
     * The same rows as `whereDate($column, $operator, $date)`.
     *
     * @param  '=' | '!=' | '<' | '<=' | '>' | '>='  $operator
     */
    protected function whereDateUsingRange(Builder $query, string $column, string $operator, string $date): Builder
    {
        try {
            $day = Carbon::parse($date);
        } catch (InvalidFormatException) {
            // Security: a tampered setting that isn't a date skips the constraint, as other invalid settings do.
            return $query;
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
