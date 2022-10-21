<?php

namespace Filament\Tables\Columns\Concerns\Summary;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class FromTo extends Strategy
{
    public function __invoke(): ?string
    {
        $valueType = $this->column->getValueType();

        $sortedRecords = collect($this->records)
            ->pluck($this->column->getName())
            ->reject(fn ($record) => $record === '' || $record === null) //but keep "false" for columns with boolean values
            ->map(fn ($record) => $this->mapValue($record, $valueType))
            ->unique()
            ->sort();

        if ($sortedRecords->count() === 0) {
            return null;
        }

        return match ($valueType) {
            'double', 'float', 'integer' => $this->fromToFloat($sortedRecords),
            'boolean' => $this->fromToBoolean($sortedRecords),
            'datetime' => $this->fromToDateTime($sortedRecords),
            default => $this->fromToString($sortedRecords),
        };
    }

    private function fromToFloat(Collection $sortedRecords): string
    {
        return implode(' - ', [$sortedRecords->min(), $sortedRecords->max()]);
    }

    private function fromToBoolean(Collection $sortedRecords): string
    {
        $fromTo = [];

        if ($sortedRecords->contains(false)) {
            $fromTo[] = __('tables::table.summary.false_value');
        }

        if ($sortedRecords->contains(true)) {
            $fromTo[] = __('tables::table.summary.true_value');
        }

        return implode(' - ', $fromTo);
    }

    private function formatDate(Carbon $date): string
    {
        return $this->column->evaluate(
            invade($this->column)?->formatStateUsing,
            [
                'state' => $date,
            ]
        ) ?? $date->format(config('tables.date_format'));
    }

    private function fromToDateTime(Collection $sortedRecords): string
    {
        $from = $sortedRecords->min();
        $to = $sortedRecords->max();

        if ($from->eq($to)) {
            return $this->formatDate($from);
        }

        return __('tables::table.summary.from') . ' ' . $this->formatDate($from) . '<br/>' .
            __('tables::table.summary.to') . ' ' . $this->formatDate($to);
    }

    private function fromToString(Collection $sortedRecords): string
    {
        if ($sortedRecords->count() === 1) {
            return strtoupper($sortedRecords->first()[0]);
        }

        $firstRecord = $sortedRecords->first();
        $lastRecord = $sortedRecords->last();

        $from = null;
        $to = null;
        $maxLetters = 3;

        $shortestRecordLength = min([
            strlen($firstRecord),
            strlen($lastRecord),
            $maxLetters,
        ]);

        for ($letter = 0; $letter < $shortestRecordLength; $letter++) {
            $firstLetter = mb_substr($firstRecord, $letter, 1);
            $lastLetter = mb_substr($lastRecord, $letter, 1);

            $from .= $firstLetter;
            $to .= $lastLetter;

            if ($firstLetter !== $lastLetter) {
                break;
            }
        }

        return strtoupper($from . ' - ' . $to);
    }
}
