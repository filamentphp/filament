<?php

use Filament\Tests\Rector\Rules\AddUnexposedToLivewireMethodsRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withAutoloadPaths([__DIR__ . '/Source'])
    ->withConfiguredRule(AddUnexposedToLivewireMethodsRector::class, [
        'paths' => [__DIR__],
    ]);
