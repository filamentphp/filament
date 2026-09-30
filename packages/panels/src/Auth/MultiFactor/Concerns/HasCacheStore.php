<?php

namespace Filament\Auth\MultiFactor\Concerns;

use Closure;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\DynamoDbStore;
use Illuminate\Cache\Lock;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\Facades\Cache;
use LogicException;

trait HasCacheStore
{
    protected ?string $cacheStore = null;

    public function cacheStore(?string $store): static
    {
        $this->cacheStore = $store;

        return $this;
    }

    public function getCacheStore(): ?string
    {
        return $this->cacheStore;
    }

    protected function getCacheRepository(): Repository
    {
        $cache = Cache::store($this->getCacheStore());

        if (! ($cache instanceof Repository)) {
            throw new LogicException('The cache store must use Laravel\'s cache repository to use multi-factor authentication.');
        }

        $store = $cache->getStore();

        if ($store instanceof DynamoDbStore) {
            throw new LogicException('The DynamoDB cache store does not provide the consistent reads required to use multi-factor authentication.');
        }

        if (($store instanceof ArrayStore) && (! app()->runningUnitTests())) {
            throw new LogicException('The array cache store is not shared between processes and cannot be used for multi-factor authentication.');
        }

        if (is_a($store, 'Illuminate\Cache\FailoverStore')) {
            throw new LogicException('The failover cache store cannot provide one authoritative store for multi-factor authentication.');
        }

        if (is_a($store, 'Illuminate\Cache\MemoizedStore')) {
            throw new LogicException('The memoized cache store cannot provide authoritative reads for multi-factor authentication.');
        }

        return $cache;
    }

    protected function getCacheLock(Repository $cache, string $name, int $seconds): Lock
    {
        $store = $cache->getStore();

        if (! ($store instanceof LockProvider)) {
            $storeName = $this->getCacheStore() ?? config('cache.default');

            throw new LogicException("The [{$storeName}] cache store must support atomic locks to use multi-factor authentication.");
        }

        $lock = $store->lock($name, $seconds);

        if (! ($lock instanceof Lock)) {
            throw new LogicException('The cache store must use Laravel\'s cache lock implementation to use multi-factor authentication.');
        }

        return $lock;
    }

    protected function executeWithCacheLock(Repository $cache, string $name, Closure $callback): bool
    {
        $lock = $this->getCacheLock($cache, $name, 60);

        return $lock->block(10, function () use ($callback, $lock): bool {
            if (! $callback()) {
                return false;
            }

            return $lock->isOwnedByCurrentProcess();
        });
    }
}
