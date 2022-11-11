<?php

namespace Filament\Tables\Columns\Summarizers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class Sum extends Summarizer
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->numeric();
    }

    public function summarize(Builder $query, string $attribute)
    {
        return $query->sum($attribute);
    }

    public function getDefaultLabel(): ?string
    {
        return __('filament-tables::table.summary.summarizers.sum.label');
    }
}
