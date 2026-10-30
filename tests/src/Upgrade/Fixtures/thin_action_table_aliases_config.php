<?php

use Filament\Upgrade\Rector\RenameThinActionAndTableAliasesRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([RenameThinActionAndTableAliasesRector::class]);
