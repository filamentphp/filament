<?php

namespace Filament\Tables\Columns\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ColorColumn;

trait HasValueType
{
    protected string $valueType = 'string';

    public function valueType(string $valueType): static
    {
        $this->valueType = $valueType;

        return $this;
    }

    public function getValueType(): string
    {
        if (is_a($this, ColorColumn::class)) {
            return 'string';
        }

        if (is_a($this, IconColumn::class)) {
            if ($this->isBoolean()) {
                return 'boolean';
            }

            return 'string';
        }

        $records = collect(
            $this->getTable()
                ->getRecords()
                ->items()
        );

        $recordTypesInColumn = $records
            ->pluck($this->getName())
            ->flatten()
            ->map(fn ($record) => gettype($record))
            ->filter(fn ($record) => $record !== 'NULL')
            ->unique();

        return $this->getTypeFromCollection(
            $recordTypesInColumn,
            $records
        );
    }

    private function getTypeFromCollection(Collection $recordTypes, Collection $records): string
    {
        if ($recordTypes->count() === 1) {
            if ($recordTypes->contains('object')) {
                if (is_a($records->first()->{$this->getName()}, Carbon::class)) {
                    return 'datetime';
                }

                return 'string';
            }

            return $recordTypes->first();
        }

        return 'string';
    }
}
