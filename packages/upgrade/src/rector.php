<?php

// use Filament\Upgrade\Rector;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\Rector\String_\RenameStringRector;

return static function (RectorConfig $rectorConfig): void {
    // $rectorConfig->rules([
    //     Rector\FixGetSetClosureTypesRector::class,
    //     Rector\MoveImportedClassesRector::class,
    //     Rector\SecondaryToGrayColorRector::class,
    //     Rector\SimpleMethodChangesRector::class,
    //     Rector\SimplePropertyChangesRector::class,
    // ]);

    $rectorConfig->ruleWithConfiguration(
        RenameClassRector::class,
        [
            'Filament\\Forms\\Commands\\Aliases\\MakeLayoutComponentCommand' => 'Filament\\Commands\\Aliases\\MakeLayoutComponentCommand',
            'Filament\\Forms\\Commands\\MakeLayoutComponentCommand' => 'Filament\\Commands\\MakeLayoutComponentCommand',
            'Filament\\Forms\\Testing\\TestsComponentActions' => 'Filament\\Testing\\TestsComponentActions',
        ],
    );

    $rectorConfig->ruleWithConfiguration(
        RenameStringRector::class,
        [
            'filament-forms::component-container' => 'filament::component-container',
        ],
    );
};
