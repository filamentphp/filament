<?php

use Filament\Tests\Rector\Rules\AddServerOnlyToLivewireMethodsRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withAutoloadPaths([__DIR__ . '/Source'])
    ->withConfiguredRule(AddServerOnlyToLivewireMethodsRector::class, [
        'paths' => [__DIR__],
    ]);
