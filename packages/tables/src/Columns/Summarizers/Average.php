<?php

namespace Filament\Tables\Columns\Summarizers;

use Illuminate\Database\Query\Builder;

class Average extends Summarizer
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->numeric();
    }

    public function summarize(Builder $query, string $attribute)
    {
        return $query->avg($attribute);
    }

    public function getDefaultLabel(): ?string
    {
        return __('filament-tables::table.summary.summarizers.average.label');
    }
}
