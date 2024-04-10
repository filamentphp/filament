<?php

use Filament\Upgrade\Rector\FixGetSetClosureTypesRector;
use Filament\Upgrade\Rector\MoveImportedClassesRector;
use Filament\Upgrade\Rector\SecondaryToGrayColorRector;
use Filament\Upgrade\Rector\SimpleMethodChangesRector;
use Filament\Upgrade\Rector\SimplePropertyChangesRector;
use Rector\Config\RectorConfig;
use Rector\Removing\Rector\Class_\RemoveTraitUseRector;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\Rector\String_\RenameStringRector;
use Rector\Renaming\ValueObject\MethodCallRename;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rules([
        FixGetSetClosureTypesRector::class,
        MoveImportedClassesRector::class,
        SecondaryToGrayColorRector::class,
        SimpleMethodChangesRector::class,
        SimplePropertyChangesRector::class,
    ]);

    $rectorConfig->ruleWithConfiguration(
        RenameClassRector::class,
        [
            'Filament\\PluginServiceProvider' => 'Spatie\\LaravelPackageTools\\PackageServiceProvider',
            'Filament\\Resources\\RelationManagers\\BelongsToManyRelationManager' => 'Filament\\Resources\\RelationManagers\\RelationManager',
            'Filament\\Resources\\RelationManagers\\HasManyRelationManager' => 'Filament\\Resources\\RelationManagers\\RelationManager',
            'Filament\\Resources\\RelationManagers\\HasManyThroughRelationManager' => 'Filament\\Resources\\RelationManagers\\RelationManager',
            'Filament\\Resources\\RelationManagers\\MorphManyRelationManager' => 'Filament\\Resources\\RelationManagers\\RelationManager',
            'Filament\\Resources\\RelationManagers\\MorphToManyRelationManager' => 'Filament\\Resources\\RelationManagers\\RelationManager',
            'Filament\\Tables\\Actions\\Position' => 'Filament\\Tables\\Enums\\ActionsPosition',
            'Filament\\Tables\\Actions\\RecordCheckboxPosition' => 'Filament\\Tables\\Enums\\RecordCheckboxPosition',
            'Filament\\Tables\\Filters\\Layout' => 'Filament\\Tables\\Enums\\FiltersLayout',
        ],
    );

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
        RenameMethodRector::class,
        [
            new MethodCallRename('Filament\\Models\\Contracts\\FilamentUser', 'canAccessFilament', 'canAccessPanel'),
            new MethodCallRename('Filament\\Pages\\Page', 'getActions', 'getHeaderActions'),
            new MethodCallRename('Filament\\Tables\\Table', 'prependActions', 'actions'),
            new MethodCallRename('Filament\\Tables\\Table', 'pushActions', 'actions'),
            new MethodCallRename('Filament\\Tables\\Table', 'bulkActions', 'groupedBulkActions'),
            new MethodCallRename('Filament\\Tables\\Table', 'prependBulkActions', 'groupedBulkActions'),
            new MethodCallRename('Filament\\Tables\\Table', 'pushBulkActions', 'groupedBulkActions'),
        ],
    );

    $rectorConfig->ruleWithConfiguration(
        RenameStringRector::class,
        require 'heroicon-changes.php',
    );
};
