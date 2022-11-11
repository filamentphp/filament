<?php

namespace Filament\Tables\Columns\Concerns;

use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait CanBeSummarized
{
    protected array $summarizers = [];

    public function summarize(array | Summarizer $summarizers): static
    {
        $this->summarizers = array_merge(
            $this->summarizers,
            Arr::wrap($summarizers),
        );

        return $this;
    }

    public function getSummary(Builder $query): array
    {
        return array_map(function (Summarizer $summarizer) use ($query): Summarizer {
            $summarizer->column($this);
            $summarizer->query($query);

            return $summarizer;
        }, $this->getSummarizers());
    }

    public function getSummarizers(): array
    {
        return $this->summarizers;
    }

    public function hasSummary(): bool
    {
        return count($this->summarizers);
    }
}
