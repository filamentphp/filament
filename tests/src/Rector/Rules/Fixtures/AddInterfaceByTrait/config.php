<?php

use Filament\Tests\Rector\Fixtures\AddInterfaceByTrait\FirstTrait;
use Filament\Tests\Rector\Fixtures\AddInterfaceByTrait\RequiredInterface;
use Filament\Tests\Rector\Fixtures\AddInterfaceByTrait\SecondTrait;
use Filament\Upgrade\Rector\AddInterfaceByTraitRector;
use Rector\Caching\ValueObject\Storage\MemoryCacheStorage;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withAutoloadPaths([__DIR__ . '/Source'])
    ->withCache(cacheClass: MemoryCacheStorage::class)
    ->withConfiguredRule(AddInterfaceByTraitRector::class, [
        FirstTrait::class => RequiredInterface::class,
        SecondTrait::class => RequiredInterface::class,
    ]);
