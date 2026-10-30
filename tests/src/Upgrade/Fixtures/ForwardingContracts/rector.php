<?php

use Filament\Upgrade\Rector\RenameDeprecatedForwardingMethodsRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([RenameDeprecatedForwardingMethodsRector::class]);
