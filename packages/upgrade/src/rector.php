<?php

use Composer\InstalledVersions;
use Filament\Upgrade\Rector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->skip([
        dirname((new ReflectionClass(InstalledVersions::class))->getFileName(), 2),
    ]);

    $rectorConfig->rules([
        Rector\SimpleMethodChangesRector::class,
    ]);
};
