<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Cache;

trait CachesQueries
{
    protected bool $shouldCacheQueries = false;

    protected int $keepCachedQueriesForSeconds = 600;

    public function cacheQueries(bool $cache = true): static
    {
        $this->shouldCacheQueries = $cache;

        return $this;
    }

    public function isCachingQueries(): bool
    {
        return $this->shouldCacheQueries;
    }

    public function keepCacheFor(int $seconds): static
    {
        $this->keepCachedQueriesForSeconds = $seconds;

        return $this;
    }

    public function isKeepingCacheFor(): int
    {
        return $this->keepCachedQueriesForSeconds;
    }

    protected function getCacheTag(): string
    {
        return $this instanceof ListRecords
            ? $this->getId()
            : $this->getLivewire()->getId();
    }

    protected function remember(string $key, Closure $callback): mixed
    {
        return $this->isCachingQueries()
            ? Cache::tags([$this->getCacheTag()])
                ->remember(
                    key: $key,
                    ttl: now()->addSeconds($this->keepCachedQueriesForSeconds),
                    callback: $callback
                )
            : $callback();
    }

    public function flushCache(): bool
    {
        return $this->isCachingQueries() && Cache::tags([$this->getCacheTag()])->flush();
    }
}
