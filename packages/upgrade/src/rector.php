<?php

use Filament\Upgrade\App\MoveImportedClassesRector;
use Filament\Upgrade\App\SimpleMethodChangesRector;
use Filament\Upgrade\App\SimplePropertyChangesRector;
use Filament\Upgrade\Forms\FixGetSetClosureTypesRector;
use Filament\Upgrade\SecondaryToGrayColorRector;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\String_\RenameStringRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rules([
        FixGetSetClosureTypesRector::class,
        MoveImportedClassesRector::class,
        SecondaryToGrayColorRector::class,
        SimpleMethodChangesRector::class,
        SimplePropertyChangesRector::class,
    ]);

    $rectorConfig->ruleWithConfiguration(
        RenameStringRector::class,
        require 'heroicon-changes.php',
    );
};
