<?php

namespace Filament\Tables\Columns\Concerns\Summary;

use Illuminate\Support\Collection;
use Filament\Tables\Columns\Concerns\Summary\Strategy;

class Count extends Strategy
{
    public function __invoke() : string
    {
        $valueType = $this->column->getValueType();

        $mappedRecords = collect($this->records)
            ->pluck($this->column->getName())
            ->map(fn ($record) => $this->mapValue($record, $valueType));

        if ($mappedRecords->count() === 0) {
            return __('tables::table.summary.records_count', ['count' => 0]);
        }

        return match ($valueType) {
            'datetime' => $this->countDateTime($mappedRecords),
            'boolean' => $this->countBoolean($mappedRecords),
            default => $this->countDefault($mappedRecords),
        };
    }

    private function countDatetime(Collection $mappedRecords) : string
    {
        $pastCount = $mappedRecords
            ->filter(fn ($record) => $record->lt(now()))
            ->count();

        $futureCount = $mappedRecords
            ->filter(fn ($record) => $record->gte(now()))
            ->count();

        $summary = [];

        if ($pastCount) {
            $summary[] = $pastCount . ' ' . __('tables::table.summary.past_records');
        }

        if ($futureCount) {
            $summary[] = $futureCount . ' ' . __('tables::table.summary.incoming_records');
        }

        return implode(', ', $summary);
    }

    private function countBoolean(Collection $mappedRecords) : string
    {
        $trueCount = $mappedRecords
            ->reject(fn ($record) => $record !== true)
            ->count();

        $falseCount = $mappedRecords
            ->reject(fn ($record) => $record !== false)
            ->count();

        $nullsCount = $mappedRecords
            ->reject(fn ($record) => $record !== null)
            ->count();

        $summary = [];

        if ($trueCount) {
            $summary[] = $trueCount . '✓';
        }

        if ($falseCount) {
            $summary[] = $falseCount . '✗';
        }

        if ($nullsCount) {
            $summary[] = $nullsCount . '- ';
        }

        return implode(' ', $summary);
    }

    private function countDefault(Collection $mappedRecords) : string
    {
        return $mappedRecords->filter()->count();
    }
}
