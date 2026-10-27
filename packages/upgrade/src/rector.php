<?php

use Composer\InstalledVersions;
use Filament\Upgrade\Rector\RemoveObsoleteUploadRestrictionTraitRector;
use Filament\Upgrade\Rector\RenameDeprecatedConstantsRector;
use Filament\Upgrade\Rector\RenameDeprecatedForwardingMethodsRector;
use Filament\Upgrade\Rector\RenameDeprecatedLazyLoadingApisRector;
use Filament\Upgrade\Rector\RenameDeprecatedTestingAssertionsRector;
use Filament\Upgrade\Rector\RenameSchemaComponentHooksRector;
use Filament\Upgrade\Rector\ReplaceConvenienceClassesRector;
use Filament\Upgrade\Rector\ReplaceDeprecatedBehavioralConfigurationRector;
use Filament\Upgrade\Rector\ReplaceDeprecatedNumberHelpersRector;
use Filament\Upgrade\Rector\ReplaceExposedLivewireMethodAttributeRector;
use Filament\Upgrade\Rector\ReplaceLegacyComponentFactoriesRector;
use Filament\Upgrade\Rector\ReplaceTypedChartWidgetsRector;
use Filament\Upgrade\Rector\WidenAvatarProviderParameterTypeRector;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->skip([
        dirname((new ReflectionClass(InstalledVersions::class))->getFileName(), 2),
    ]);

    $rectorConfig->importNames();

    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Filament\\Tables\\Columns\\Concerns\\CanBeCopied' => 'Filament\\Support\\Concerns\\CanBeCopied',
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

    $rectorConfig->rule(RemoveObsoleteUploadRestrictionTraitRector::class);
    $rectorConfig->rule(RenameDeprecatedConstantsRector::class);
    $rectorConfig->rule(RenameDeprecatedForwardingMethodsRector::class);
    $rectorConfig->rule(RenameDeprecatedLazyLoadingApisRector::class);
    $rectorConfig->rule(RenameDeprecatedTestingAssertionsRector::class);
    $rectorConfig->rule(RenameSchemaComponentHooksRector::class);
    $rectorConfig->rule(ReplaceConvenienceClassesRector::class);
    $rectorConfig->rule(ReplaceDeprecatedBehavioralConfigurationRector::class);
    $rectorConfig->rule(ReplaceDeprecatedNumberHelpersRector::class);
    $rectorConfig->rule(ReplaceExposedLivewireMethodAttributeRector::class);
    $rectorConfig->rule(ReplaceLegacyComponentFactoriesRector::class);
    $rectorConfig->rule(ReplaceTypedChartWidgetsRector::class);
    $rectorConfig->rule(WidenAvatarProviderParameterTypeRector::class);
};
