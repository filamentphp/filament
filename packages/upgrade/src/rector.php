<?php

use Composer\InstalledVersions;
use Rector\Config\RectorConfig;
use Rector\Removing\Rector\Class_\RemoveTraitUseRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->skip([
        dirname((new ReflectionClass(InstalledVersions::class))->getFileName(), 2),
    ]);

    $rectorConfig->ruleWithConfiguration(RemoveTraitUseRector::class, [
        'Filament\\Schemas\\Concerns\\RestrictsFileUploadsToSchemaComponents',
    ]);
};
