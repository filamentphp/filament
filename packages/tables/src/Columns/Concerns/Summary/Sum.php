<?php

namespace Filament\Tables\Columns\Concerns\Summary;

class Sum extends Strategy
{
    public function __invoke(): float
    {
        $additionComponents = collect($this->records)
            ->pluck($this->column->getName())
            ->filter(function ($record) {
                return is_numeric($record);
            });

        if ($additionComponents->count() === 0) {
            return null;
        }

        return number_format($additionComponents->sum(), 2, ',', '.');
    }
}
