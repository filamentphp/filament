<?php

use Composer\InstalledVersions;
use Filament\Upgrade\Rector\WidenAvatarProviderParameterTypeRector;
use Rector\Config\RectorConfig;
use Rector\Removing\Rector\Class_\RemoveTraitUseRector;
use Rector\Renaming\Rector\ClassConstFetch\RenameClassConstFetchRector;
use Rector\Renaming\ValueObject\RenameClassConstFetch;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->skip([
        dirname((new ReflectionClass(InstalledVersions::class))->getFileName(), 2),
    ]);

    $rectorConfig->ruleWithConfiguration(RemoveTraitUseRector::class, [
        'Filament\\Schemas\\Concerns\\RestrictsFileUploadsToSchemaComponents',
    ]);

    $rectorConfig->ruleWithConfiguration(RenameClassConstFetchRector::class, [
        new RenameClassConstFetch('Filament\\Tables\\View\\TablesRenderHook', 'TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER', 'TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER'),
        new RenameClassConstFetch('Filament\\Tables\\View\\TablesRenderHook', 'TOOLBAR_TOGGLE_COLUMN_TRIGGER_BEFORE', 'TOOLBAR_COLUMN_MANAGER_TRIGGER_BEFORE'),
    ]);

    $rectorConfig->rule(WidenAvatarProviderParameterTypeRector::class);
};
