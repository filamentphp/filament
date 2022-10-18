<?php

namespace Filament\Tables\Columns\Concerns\Summary;

use Filament\Tables\Columns\Concerns\Summary\Strategy;

class CountUnique extends Strategy
{
    public function __invoke() : int
    {
        return collect($this->records)
            ->pluck($this->column->getName())
            ->unique()
            ->count() ?? 0;
    }
}
