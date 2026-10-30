<?php

use Filament\Upgrade\Rector\RemoveExportActionCsvDelimiterRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([RemoveExportActionCsvDelimiterRector::class]);
