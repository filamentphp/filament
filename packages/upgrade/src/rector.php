<?php

use Rector\Config\RectorConfig;
use Rector\Removing\Rector\Class_\RemoveTraitUseRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(RemoveTraitUseRector::class, [
        'Filament\\Schemas\\Concerns\\RestrictsFileUploadsToSchemaComponents',
    ]);
};
