<?php

use Composer\InstalledVersions;
use Filament\Upgrade\Rector;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->skip([
        dirname((new ReflectionClass(InstalledVersions::class))->getFileName(), 2),
    ]);

    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Filament\\Tables\\Filters\\Concerns\\HasSchema' => 'Filament\\Tables\\Filters\\Concerns\\HasForm',
        'Filament\\Widgets\\ChartWidget\\Concerns\\HasFiltersSchema' => 'Filament\\Widgets\\ChartWidget\\Concerns\\HasFiltersForm',
    ]);

    $rectorConfig->rules([
        Rector\SimpleMethodChangesRector::class,
    ]);
};
