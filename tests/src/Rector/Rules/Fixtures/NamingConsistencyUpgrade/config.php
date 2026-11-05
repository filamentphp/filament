<?php

use Rector\Caching\ValueObject\Storage\MemoryCacheStorage;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withSets([dirname(__DIR__, 6) . '/packages/upgrade/src/rector.php'])
    ->withCache(cacheClass: MemoryCacheStorage::class);
