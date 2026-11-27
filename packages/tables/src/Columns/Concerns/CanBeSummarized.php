<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
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
     * @param  array<Summarizer> | Summarizer  $summarizers
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

    public function getSummarizer(string $id): ?Summarizer
    {
        return $this->getSummarizers()[$id] ?? null;
    }

    /**
     * @return array<string | int, Summarizer>
     */
    public function getSummarizers(Builder | Closure | null $query = null): array
    {
        return $query ? $this->getVisibleSummarizers($query) : $this->summarizers;
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @return array<string | int, Summarizer>
     */
    public function getVisibleSummarizers(Builder | Closure | null $query): array
    {
        return array_filter(
            $this->summarizers,
            fn (Summarizer $summarizer): bool => ($query ? $summarizer->query($query) : (clone $summarizer)->query(null))->isVisible(),
        );
    }

    public function hasSummary(Builder | Closure | null $query = null): bool
    {
        return (bool) count($this->getSummarizers($query));
    }
}
