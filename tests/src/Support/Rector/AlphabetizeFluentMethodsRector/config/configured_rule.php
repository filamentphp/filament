<?php

use Filament\Support\Rector\AlphabetizeFluentMethodsRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([
        AlphabetizeFluentMethodsRector::class,
    ]);
