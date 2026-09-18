<?php

use Filament\Tests\Rector\Rules\ScopePestTestHelpersRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([
        ScopePestTestHelpersRector::class,
    ]);
