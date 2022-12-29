<?php

namespace Filament\Tables\Columns\Summarizers;

use Carbon\CarbonImmutable;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;

class Range extends Summarizer
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.summaries.range';

    /**
     * @return array{0: mixed, 1: mixed}
     */
    public function summarize(Builder $query, string $attribute): array
    {
        $query->whereNotNull($attribute);

        return [$query->clone()->min($attribute), $query->clone()->max($attribute)];
    }

    public function minimalDateTimeDifference(): static
    {
        $this->formatStateUsing(static function (array $state): array {
            if (blank($state[1])) {
                unset($state[1]);
            }

            if (blank($state[0])) {
                unset($state[0]);
            }

            if (count($state) !== 2) {
                return $state;
            }

            $originalFrom = CarbonImmutable::make($state[0]);
            $originalTo = CarbonImmutable::make($state[1]);

            $fromDate = $originalFrom->format(Table::$defaultDateDisplayFormat);
            $toDate = $originalTo->format(Table::$defaultDateDisplayFormat);

            if ($fromDate !== $toDate) {
                return [$fromDate, $toDate];
            }

            $fromDateTime = $originalFrom->format(Table::$defaultDateTimeDisplayFormat);
            $toDateTime = $originalTo->format(Table::$defaultDateTimeDisplayFormat);

            if ($fromDateTime === $toDateTime) {
                return [$fromDateTime];
            }

            return [$fromDateTime, $toDateTime];
        });

        return $this;
    }

    public function minimalTextualDifference(): static
    {
        $this->formatStateUsing(static function (array $state): array {
            $originalFrom = trim(strval($state[0]));
            $originalTo = trim(strval($state[1]));

            if (($originalFrom === $originalTo) || blank($originalTo)) {
                unset($state[1]);
            }

            if (blank($originalFrom)) {
                unset($state[0]);
            }

            if (count($state) !== 2) {
                return $state;
            }

            $originalFromCharacters = str_split($originalFrom);
            $originalToCharacters = str_split($originalTo);

            $from = '';
            $to = '';

            $isFromLonger = (count($originalFromCharacters) > count($originalToCharacters));

            $characterIndex = 0;

            foreach (($isFromLonger ? $originalToCharacters : $originalFromCharacters) as $characterIndex => $character) {
                $from .= ($isFromLonger ? $originalFromCharacters[$characterIndex] : $character);
                $to .= ($isFromLonger ? $character : $originalToCharacters[$characterIndex]);

                if (strtolower($from) !== strtolower($to)) {
                    break;
                }
            }

            if ($from !== $to) {
                return [$from, $to];
            }

            $characterIndex++;

            if ($isFromLonger) {
                $from .= ($originalFromCharacters[$characterIndex] ?? '');
            } else {
                $to .= ($originalToCharacters[$characterIndex] ?? '');
            }

            return [$from, $to];
        });

        return $this;
    }
}
