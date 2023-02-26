<?php

use Filament\Upgrade\Rectors;
use Rector\Config\RectorConfig;
use Rector\Removing\Rector\Class_\RemoveTraitUseRector;
use Rector\Renaming\Rector\String_\RenameStringRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rules([
        Rectors\FixGetSetClosureTypesRector::class,
        Rectors\MoveImportedClassesRector::class,
        Rectors\ReplaceParentClassRector::class,
        Rectors\SecondaryToGrayColorRector::class,
        Rectors\SimpleMethodChangesRector::class,
        Rectors\SimplePropertyChangesRector::class,
    ]);

    $rectorConfig->ruleWithConfiguration(RemoveTraitUseRector::class, [
        'Filament\\Http\\Livewire\\Concerns\\CanNotify',
        'Filament\\Resources\\Pages\\ListRecords\\Concerns\\CanCreateRecords',
        'Filament\\Resources\\Pages\\ListRecords\\Concerns\\CanDeleteRecords',
        'Filament\\Resources\\Pages\\ListRecords\\Concerns\\CanEditRecords',
        'Filament\\Resources\\Pages\\ListRecords\\Concerns\\CanViewRecords',
        'Filament\\Resources\\RelationManagers\\Concerns\\CanAssociateRecords',
        'Filament\\Resources\\RelationManagers\\Concerns\\CanAttachRecords',
        'Filament\\Resources\\RelationManagers\\Concerns\\CanCreateRecords',
        'Filament\\Resources\\RelationManagers\\Concerns\\CanDeleteRecords',
        'Filament\\Resources\\RelationManagers\\Concerns\\CanDetachRecords',
        'Filament\\Resources\\RelationManagers\\Concerns\\CanDisassociateRecords',
        'Filament\\Resources\\RelationManagers\\Concerns\\CanEditRecords',
        'Filament\\Resources\\RelationManagers\\Concerns\\CanViewRecords',
    ]);

    $rectorConfig->ruleWithConfiguration(
        RenameStringRector::class,
        require 'heroicon-changes.php',
    );
};
