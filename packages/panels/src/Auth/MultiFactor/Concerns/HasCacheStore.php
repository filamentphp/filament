<?php

namespace Filament\Auth\MultiFactor\Concerns;

use Closure;
use Illuminate\Cache\Lock;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\Facades\Cache;
use LogicException;

trait HasCacheStore
{
    protected function getCacheRepository(): Repository
    {
        $cache = Cache::store();

        if (! ($cache instanceof Repository)) {
            throw new LogicException('The cache store must use Laravel\'s cache repository to use multi-factor authentication.');
        }

        return $cache;
    }

    protected function getCacheLock(Repository $cache, string $name, int $seconds): ?Lock
    {
        $store = $cache->getStore();

        if (! ($store instanceof LockProvider)) {
            return null;
        }

        $lock = $store->lock($name, $seconds);

        if (is_a($lock, 'Illuminate\Cache\NoLock') || (! ($lock instanceof Lock))) {
            return null;
        }

        return $lock;
    }

    protected function executeWithCacheLock(Repository $cache, string $name, Closure $callback): bool
    {
        $lock = $this->getCacheLock($cache, $name, 60);

        if (! $lock) {
            return $callback(null, $cache);
        }

        return $lock->block(10, fn (): bool => $callback($lock, $cache));
    }
}
