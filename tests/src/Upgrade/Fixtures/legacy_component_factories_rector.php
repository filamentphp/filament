<?php

use Filament\Upgrade\Rector\ReplaceLegacyComponentFactoriesRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withImportNames()
    ->withRules([ReplaceLegacyComponentFactoriesRector::class]);
