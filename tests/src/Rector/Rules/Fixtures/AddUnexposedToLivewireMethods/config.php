<?php

use Filament\Tests\Rector\Rules\AddUnexposedToLivewireMethodsRector;
use Rector\Caching\ValueObject\Storage\MemoryCacheStorage;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withAutoloadPaths([__DIR__ . '/Source'])
    ->withCache(cacheClass: MemoryCacheStorage::class)
    ->withConfiguredRule(AddUnexposedToLivewireMethodsRector::class, [
        'paths' => [__DIR__],
    ]);
