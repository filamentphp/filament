<?php

use Composer\InstalledVersions;
use Filament\Upgrade\Rector\WidenAvatarProviderParameterTypeRector;
use Rector\Config\RectorConfig;
use Rector\Removing\Rector\Class_\RemoveTraitUseRector;
use Rector\Renaming\Rector\ClassConstFetch\RenameClassConstFetchRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\ValueObject\RenameClassConstFetch;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->skip([
        dirname((new ReflectionClass(InstalledVersions::class))->getFileName(), 2),
    ]);

    $rectorConfig->importNames();

    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\Constraint' => 'Filament\\QueryBuilder\\Constraints\\Constraint',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\BooleanConstraint' => 'Filament\\QueryBuilder\\Constraints\\BooleanConstraint',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\BooleanConstraint\\Operators\\IsTrueOperator' => 'Filament\\QueryBuilder\\Constraints\\BooleanConstraint\\Operators\\IsTrueOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\Concerns\\CanBeNullable' => 'Filament\\QueryBuilder\\Constraints\\Concerns\\CanBeNullable',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\DateConstraint' => 'Filament\\QueryBuilder\\Constraints\\DateConstraint',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\DateConstraint\\Operators\\IsAfterOperator' => 'Filament\\QueryBuilder\\Constraints\\DateConstraint\\Operators\\IsAfterOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\DateConstraint\\Operators\\IsBeforeOperator' => 'Filament\\QueryBuilder\\Constraints\\DateConstraint\\Operators\\IsBeforeOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\DateConstraint\\Operators\\IsDateOperator' => 'Filament\\QueryBuilder\\Constraints\\DateConstraint\\Operators\\IsDateOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\DateConstraint\\Operators\\IsMonthOperator' => 'Filament\\QueryBuilder\\Constraints\\DateConstraint\\Operators\\IsMonthOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\DateConstraint\\Operators\\IsYearOperator' => 'Filament\\QueryBuilder\\Constraints\\DateConstraint\\Operators\\IsYearOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\NumberConstraint' => 'Filament\\QueryBuilder\\Constraints\\NumberConstraint',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\NumberConstraint\\Operators\\Concerns\\CanAggregateRelationships' => 'Filament\\QueryBuilder\\Constraints\\NumberConstraint\\Operators\\Concerns\\CanAggregateRelationships',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\NumberConstraint\\Operators\\EqualsOperator' => 'Filament\\QueryBuilder\\Constraints\\NumberConstraint\\Operators\\EqualsOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\NumberConstraint\\Operators\\IsMaxOperator' => 'Filament\\QueryBuilder\\Constraints\\NumberConstraint\\Operators\\IsMaxOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\NumberConstraint\\Operators\\IsMinOperator' => 'Filament\\QueryBuilder\\Constraints\\NumberConstraint\\Operators\\IsMinOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\Operators\\IsFilledOperator' => 'Filament\\QueryBuilder\\Constraints\\Operators\\IsFilledOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\Operators\\Operator' => 'Filament\\QueryBuilder\\Constraints\\Operators\\Operator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\RelationshipConstraint' => 'Filament\\QueryBuilder\\Constraints\\RelationshipConstraint',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\RelationshipConstraint\\Operators\\EqualsOperator' => 'Filament\\QueryBuilder\\Constraints\\RelationshipConstraint\\Operators\\EqualsOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\RelationshipConstraint\\Operators\\HasMaxOperator' => 'Filament\\QueryBuilder\\Constraints\\RelationshipConstraint\\Operators\\HasMaxOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\RelationshipConstraint\\Operators\\HasMinOperator' => 'Filament\\QueryBuilder\\Constraints\\RelationshipConstraint\\Operators\\HasMinOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\RelationshipConstraint\\Operators\\IsEmptyOperator' => 'Filament\\QueryBuilder\\Constraints\\RelationshipConstraint\\Operators\\IsEmptyOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\RelationshipConstraint\\Operators\\IsRelatedToOperator' => 'Filament\\QueryBuilder\\Constraints\\RelationshipConstraint\\Operators\\IsRelatedToOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\SelectConstraint' => 'Filament\\QueryBuilder\\Constraints\\SelectConstraint',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\SelectConstraint\\Operators\\IsOperator' => 'Filament\\QueryBuilder\\Constraints\\SelectConstraint\\Operators\\IsOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\TextConstraint' => 'Filament\\QueryBuilder\\Constraints\\TextConstraint',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\TextConstraint\\Operators\\ContainsOperator' => 'Filament\\QueryBuilder\\Constraints\\TextConstraint\\Operators\\ContainsOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\TextConstraint\\Operators\\EndsWithOperator' => 'Filament\\QueryBuilder\\Constraints\\TextConstraint\\Operators\\EndsWithOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\TextConstraint\\Operators\\EqualsOperator' => 'Filament\\QueryBuilder\\Constraints\\TextConstraint\\Operators\\EqualsOperator',
        'Filament\\Tables\\Filters\\QueryBuilder\\Constraints\\TextConstraint\\Operators\\StartsWithOperator' => 'Filament\\QueryBuilder\\Constraints\\TextConstraint\\Operators\\StartsWithOperator',
    ]);

    $rectorConfig->ruleWithConfiguration(RemoveTraitUseRector::class, [
        'Filament\\Schemas\\Concerns\\RestrictsFileUploadsToSchemaComponents',
    ]);

    $rectorConfig->ruleWithConfiguration(RenameClassConstFetchRector::class, [
        new RenameClassConstFetch('Filament\\Support\\Enums\\IconSize', 'ExtraExtraLarge', 'TwoExtraLarge'),
        new RenameClassConstFetch('Filament\\Tables\\View\\TablesRenderHook', 'TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER', 'TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER'),
        new RenameClassConstFetch('Filament\\Tables\\View\\TablesRenderHook', 'TOOLBAR_TOGGLE_COLUMN_TRIGGER_BEFORE', 'TOOLBAR_COLUMN_MANAGER_TRIGGER_BEFORE'),
    ]);

    $rectorConfig->rule(WidenAvatarProviderParameterTypeRector::class);
};
