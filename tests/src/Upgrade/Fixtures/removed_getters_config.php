<?php

use Filament\Upgrade\Rector\RemoveObsoleteConcealmentRector;
use Filament\Upgrade\Rector\ReplaceImportColumnGetExampleRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withImportNames()
    ->withRules([
        ReplaceImportColumnGetExampleRector::class,
        RemoveObsoleteConcealmentRector::class,
    ]);
