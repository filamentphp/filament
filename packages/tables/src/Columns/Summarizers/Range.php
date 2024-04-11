<?php

namespace Filament\Tables\Columns\Summarizers;

use Carbon\CarbonImmutable;
use Closure;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

class Range extends Summarizer
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.summaries.range';

    protected bool | Closure $shouldExcludeNull = true;

    /**
     * @return array{0: mixed, 1: mixed}
     */
    public function summarize(Builder $query, string $attribute): array
    {
        if ($this->shouldExcludeNull()) {
            $query->whereNotNull($attribute);
        }

        $minSelectAlias = Str::random();
        $maxSelectAlias = Str::random();

        $state = $query->selectRaw("min({$attribute}) as \"{$minSelectAlias}\", max({$attribute}) as \"{$maxSelectAlias}\"")->get()[0];

        return [$state->{$minSelectAlias}, $state->{$maxSelectAlias}];
    }

    public function excludeNull(bool | Closure $condition = true): static
    {
        $this->shouldExcludeNull = $condition;

        return $this;
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

            $fromDate = $originalFrom->translatedFormat(Table::$defaultDateDisplayFormat);
            $toDate = $originalTo->translatedFormat(Table::$defaultDateDisplayFormat);

            if ($fromDate !== $toDate) {
                return [$fromDate, $toDate];
            }

            $fromDateTime = $originalFrom->translatedFormat(Table::$defaultDateTimeDisplayFormat);
            $toDateTime = $originalTo->translatedFormat(Table::$defaultDateTimeDisplayFormat);

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

                if (Str::lower($from) !== Str::lower($to)) {
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

    public function shouldExcludeNull(): bool
    {
        return (bool) $this->evaluate($this->shouldExcludeNull);
    }
}
