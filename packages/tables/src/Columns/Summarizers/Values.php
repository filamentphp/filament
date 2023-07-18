<?php

namespace Filament\Tables\Columns\Summarizers;

use Closure;
use Illuminate\Database\Query\Builder;

class Values extends Summarizer
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.summaries.values';

    protected bool | Closure $isBulleted = true;

    /**
     * @return array<string, int>
     */
    public function summarize(Builder $query, string $attribute): array
    {
        return $query->clone()->distinct()->pluck($attribute)->all();
    }

    public function bulleted(bool | Closure $condition = true): static
    {
        $this->isBulleted = $condition;

        return $this;
    }

    public function isBulleted(): bool
    {
        return (bool) $this->evaluate($this->isBulleted);
    }
}
