<?php

namespace Filament\Tables\Columns\Summarizers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\HtmlString;

class Collect extends Summarizer
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function summarize(Builder $query, string $attribute): array
    {
        $column = $this->getColumn();
        $state = [];

        foreach ($query->clone()->distinct()->get() as $record)
        {
            $state[] = $record->{$attribute};
        }

        return array_unique($state);
    }

    public function getDefaultLabel(): ?string
    {
        return __('');
    }
}
