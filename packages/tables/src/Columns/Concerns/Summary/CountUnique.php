<?php

namespace Filament\Tables\Columns\Concerns\Summary;

class CountUnique extends Strategy
{
    public function __invoke(): int
    {
        return collect($this->records)
            ->pluck($this->column->getName())
            ->unique()
            ->count() ?? 0;
    }
}
