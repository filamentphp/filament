<?php

use Filament\Upgrade\Rector\ReplaceDeprecatedBehavioralConfigurationRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withImportNames()
    ->withRules([ReplaceDeprecatedBehavioralConfigurationRector::class]);
