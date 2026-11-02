<?php

use Filament\Tests\Rector\Rules\AddUnexposedToLivewireMethodsRector;
use Filament\Tests\Rector\Rules\MovePestTestClassesToEndRector;
use Filament\Tests\Rector\Rules\ScopePestTestHelpersRector;
use Rector\Caching\ValueObject\Storage\MemoryCacheStorage;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/docs-assets/app/app',
        __DIR__ . '/docs-assets/app/config',
        __DIR__ . '/docs-assets/app/database',
        __DIR__ . '/docs-assets/app/routes',
        __DIR__ . '/docs-assets/app/tests',
        __DIR__ . '/packages',
        __DIR__ . '/tests',
    ])
    ->withAutoloadPaths([
        __DIR__ . '/tests/src',
        __DIR__ . '/docs-assets/app/app',
    ])
    ->withCache(
        cacheDirectory: __DIR__ . '/.rector.cache',
        // Trait eligibility changes when another component starts using it.
        cacheClass: MemoryCacheStorage::class,
    )
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(0)
    ->withRules([
        ScopePestTestHelpersRector::class,
        MovePestTestClassesToEndRector::class,
    ])
    ->withConfiguredRule(AddUnexposedToLivewireMethodsRector::class, [
        'paths' => [__DIR__ . '/packages'],
        'componentPaths' => [
            __DIR__ . '/packages',
            __DIR__ . '/tests/src',
            __DIR__ . '/docs-assets/app/app',
        ],
    ]);
