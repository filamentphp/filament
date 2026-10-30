<?php

use Filament\Upgrade\Rector\RenameReconciledContractMethodsRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([RenameReconciledContractMethodsRector::class]);
