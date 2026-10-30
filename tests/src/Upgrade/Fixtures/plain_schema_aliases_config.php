<?php

use Filament\Upgrade\Rector\RenamePlainSchemaAliasesRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([RenamePlainSchemaAliasesRector::class]);
