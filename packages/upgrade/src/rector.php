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
            'Filament\\Pages\\Actions\\Action' => 'Filament\\Actions\\Action',
            'Filament\\Forms\\Components\\BelongsToManyCheckboxList' => 'Filament\\Forms\\Components\\CheckboxList',
            'Filament\\Forms\\Components\\BelongsToManyMultiSelect' => 'Filament\\Forms\\Components\\MultiSelect',
            'Filament\\Forms\\Components\\BelongsToSelect' => 'Filament\\Forms\\Components\\Select',
            'Filament\\Forms\\Components\\Card' => 'Filament\\Components\\Section',
            'Filament\\Forms\\Components\\HasManyRepeater' => 'Filament\\Forms\\Components\\RelationshipRepeater',
            'Filament\\Forms\\Components\\MorphManyRepeater' => 'Filament\\Forms\\Components\\RelationshipRepeater',
            'Filament\\Actions\\Exceptions\\Hold' => 'Filament\\Support\\Exceptions\\Halt',
            'Filament\\Actions\\Modal\\Actions' => 'Filament\\Actions\\StaticAction',
            'Filament\\Forms\\Components\\Concerns\\HasExtraAlpineAttributes' => 'Filament\\Support\\Concerns\\HasExtraAlpineAttributes',
            'Filament\\Forms\\Components\\Concerns\\HasExtraAttributes' => 'Filament\\Support\\Concerns\\HasExtraAttributes',
            'Filament\\Infolists\\Components\\Card' => 'Filament\\Components\\Section',
            'Filament\\Http\\Livewire\\Auth\\Login' => 'Filament\\Pages\\Auth\\Login',
            'Filament\\\Navigation\\UserMenuItem' => 'Filament\\Navigation\\MenuItem',
            'Filament\\Pages\\Actions\\Modal\\Actions\\Action' => 'Filament\\Actions\\StaticAction',
            'Filament\\Pages\\Actions\\Modal\\Actions\\ButtonAction' => 'Filament\\Actions\\StaticAction',
            'Filament\\Pages\\Actions\\ActionGroup' => 'Filament\\Actions\\ActionGroup',
            'Filament\\Pages\\Actions\\ButtonAction' => 'Filament\\Actions\\Action',
            'Filament\\Pages\\Actions\\CreateAction' => 'Filament\\Actions\\CreateAction',
            'Filament\\Pages\\Actions\\DeleteAction' => 'Filament\\Actions\\DeleteAction',
            'Filament\\Pages\\Actions\\EditAction' => 'Filament\\Actions\\EditAction',
            'Filament\\Pages\\Actions\\ForceDeleteAction' => 'Filament\\Actions\\ForceDeleteAction',
            'Filament\\Pages\\Actions\\ReplicateAction' => 'Filament\\Actions\\ReplicateAction',
            'Filament\\Pages\\Actions\\RestoreAction' => 'Filament\\Actions\\RestoreAction',
            'Filament\\Pages\\Actions\\SelectAction' => 'Filament\\Actions\\SelectAction',
            'Filament\\Pages\\Actions\\ViewAction' => 'Filament\\Actions\\ViewAction',
            'Filament\\\Resources\\Pages\\ListRecords\\Tab' => 'Filament\\Resources\\Components\\Tab',
            'Filament\\Tables\\Actions\\Modal\\Actions\\Action' => 'Filament\\Actions\\StaticAction',
            'Filament\\Tables\\Actions\\Modal\\Actions\\ButtonAction' => 'Filament\\Actions\\StaticAction',
            'Filament\\Tables\\Actions\\LinkAction' => 'Filament\\Actions\\Action',
            'Filament\\Tables\\Columns\\Concerns\\HasExtraAttributes' => 'Filament\\Support\\Concerns\\HasExtraAttributes',
            'Filament\\Widgets\\StatsOverviewWidget\\Card' => 'Filament\\Widgets\\StatsOverviewWidget\\Stat',
        ],
    );

    $rectorConfig->ruleWithConfiguration(
        RenameStringRector::class,
        [
            'filament-forms::component-container' => 'filament::component-container',
        ],
    );
};
