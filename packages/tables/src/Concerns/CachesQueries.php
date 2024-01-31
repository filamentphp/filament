<?php

namespace Filament\Tables\Concerns;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
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
        return $this->getId();
    }

    protected function remember(string $key, \Closure $callback): mixed
    {
        /** @var \Closure(): Paginator|CursorPaginator|Collection  $callback */
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
