<?php

namespace Filament\Tables\Columns\Concerns;

use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait CanBeSummarized
{
    /**
     * @var array<string | int, Summarizer>
     */
    protected array $summarizers = [];

    /**
     * @param array<Summarizer> | Summarizer $summarizers
     */
    public function summarize(array | Summarizer $summarizers): static
    {
        foreach (Arr::wrap($summarizers) as $summarizer) {
            $summarizer->column($this);

            if (filled($id = $summarizer->getId())) {
                $this->summarizers[$id] = $summarizer;
            } else {
                $this->summarizers[] = $summarizer;
            }
        }

        return $this;
    }

    /**
     * @return array<string | int, Summarizer>
     */
    public function getSummary(Builder $query): array
    {
        return array_map(function (Summarizer $summarizer) use ($query): Summarizer {
            $summarizer->query($query);

            return $summarizer;
        }, $this->getSummarizers());
    }

    public function getSummarizer(string $id): ?Summarizer
    {
        return $this->getSummarizers()[$id] ?? null;
    }

    /**
     * @return array<string | int, Summarizer>
     */
    public function getSummarizers(): array
    {
        return $this->summarizers;
    }

    public function hasSummary(): bool
    {
        return (bool) count($this->summarizers);
    }
}
