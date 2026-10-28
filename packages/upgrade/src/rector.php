<?php

use Composer\InstalledVersions;
use Filament\Upgrade\Rector\MigrateLegacyConfigurationHooksRector;
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
use Filament\Upgrade\Rector\ReplaceLegacyMenuItemsRector;
use Filament\Upgrade\Rector\ReplaceLegacyPanelIntegrationRector;
use Filament\Upgrade\Rector\ReplaceTypedChartWidgetsRector;
use Filament\Upgrade\Rector\WidenAvatarProviderParameterTypeRector;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\Rector\String_\RenameStringRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->skip([
        __FILE__,
        dirname((new ReflectionClass(InstalledVersions::class))->getFileName(), 2),
    ]);

    $rectorConfig->importNames();

    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Filament\\Schemas\\View\\SchemaIconAlias' => 'Filament\\Schemas\\View\\SchemasIconAlias',
        'Filament\\Widgets\\View\\Components\\StatsOverviewWidgetComponent\\StatComponent\\StatsOverviewWidgetStatChartComponent' => 'Filament\\Widgets\\View\\Components\\StatsOverviewWidgetComponent\\StatComponent\\ChartComponent',
        'Filament\\Forms\\Components\\Concerns\\CanLimitItemsLength' => 'Filament\\Forms\\Components\\Concerns\\CanLimitItemsCount',
        'Filament\\Tables\\Columns\\Concerns\\HasRowLoopObject' => 'Filament\\Tables\\Columns\\Concerns\\HasRowLoop',
        'Filament\\Tables\\Table\\Concerns\\CanDeferLoading' => 'Filament\\Tables\\Table\\Concerns\\CanBeLazyLoaded',
        'Filament\\Tables\\Concerns\\CanDeferLoading' => 'Filament\\Tables\\Concerns\\CanBeLazyLoaded',
        'Filament\\Tables\\Actions\\HeaderActionsPosition' => 'Filament\\Tables\\Enums\\HeaderActionsPosition',
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

    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        'forms:components.rich-editor.toolbar.align-center' => 'forms::components.rich-editor.toolbar.align-center',
        'forms:components.rich-editor.toolbar.align-end' => 'forms::components.rich-editor.toolbar.align-end',
        'forms:components.rich-editor.toolbar.align-justify' => 'forms::components.rich-editor.toolbar.align-justify',
        'forms:components.rich-editor.toolbar.align-start' => 'forms::components.rich-editor.toolbar.align-start',
        'forms:components.rich-editor.toolbar.attach-files' => 'forms::components.rich-editor.toolbar.attach-files',
        'forms:components.rich-editor.toolbar.blockquote' => 'forms::components.rich-editor.toolbar.blockquote',
        'forms:components.rich-editor.toolbar.bold' => 'forms::components.rich-editor.toolbar.bold',
        'forms:components.rich-editor.toolbar.bullet-list' => 'forms::components.rich-editor.toolbar.bullet-list',
        'forms:components.rich-editor.toolbar.clear_formatting' => 'forms::components.rich-editor.toolbar.clear-formatting',
        'forms:components.rich-editor.toolbar.code' => 'forms::components.rich-editor.toolbar.code',
        'forms:components.rich-editor.toolbar.code-block' => 'forms::components.rich-editor.toolbar.code-block',
        'forms:components.rich-editor.toolbar.custom-blocks' => 'forms::components.rich-editor.toolbar.custom-blocks',
        'forms:components.rich-editor.toolbar.details' => 'forms::components.rich-editor.toolbar.details',
        'forms:components.rich-editor.toolbar.grid' => 'forms::components.rich-editor.toolbar.grid',
        'forms:components.rich-editor.toolbar.grid_add_column_after' => 'forms::components.rich-editor.toolbar.grid-add-column-after',
        'forms:components.rich-editor.toolbar.grid_add_column_before' => 'forms::components.rich-editor.toolbar.grid-add-column-before',
        'forms:components.rich-editor.toolbar.grid_delete' => 'forms::components.rich-editor.toolbar.grid-delete',
        'forms:components.rich-editor.toolbar.grid_delete_column' => 'forms::components.rich-editor.toolbar.grid-delete-column',
        'forms:components.rich-editor.toolbar.h1' => 'forms::components.rich-editor.toolbar.h1',
        'forms:components.rich-editor.toolbar.h2' => 'forms::components.rich-editor.toolbar.h2',
        'forms:components.rich-editor.toolbar.h3' => 'forms::components.rich-editor.toolbar.h3',
        'forms:components.rich-editor.toolbar.h4' => 'forms::components.rich-editor.toolbar.h4',
        'forms:components.rich-editor.toolbar.h5' => 'forms::components.rich-editor.toolbar.h5',
        'forms:components.rich-editor.toolbar.h6' => 'forms::components.rich-editor.toolbar.h6',
        'forms:components.rich-editor.toolbar.highlight' => 'forms::components.rich-editor.toolbar.highlight',
        'forms:components.rich-editor.toolbar.horizontal-rule' => 'forms::components.rich-editor.toolbar.horizontal-rule',
        'forms:components.rich-editor.toolbar.italic' => 'forms::components.rich-editor.toolbar.italic',
        'forms:components.rich-editor.toolbar.lead' => 'forms::components.rich-editor.toolbar.lead',
        'forms:components.rich-editor.toolbar.link' => 'forms::components.rich-editor.toolbar.link',
        'forms:components.rich-editor.toolbar.merge-tags' => 'forms::components.rich-editor.toolbar.merge-tags',
        'forms:components.rich-editor.toolbar.ordered-list' => 'forms::components.rich-editor.toolbar.ordered-list',
        'forms:components.rich-editor.toolbar.paragraph' => 'forms::components.rich-editor.toolbar.paragraph',
        'forms:components.rich-editor.toolbar.redo' => 'forms::components.rich-editor.toolbar.redo',
        'forms:components.rich-editor.toolbar.small' => 'forms::components.rich-editor.toolbar.small',
        'forms:components.rich-editor.toolbar.strike' => 'forms::components.rich-editor.toolbar.strike',
        'forms:components.rich-editor.toolbar.subscript' => 'forms::components.rich-editor.toolbar.subscript',
        'forms:components.rich-editor.toolbar.superscript' => 'forms::components.rich-editor.toolbar.superscript',
        'forms:components.rich-editor.toolbar.table' => 'forms::components.rich-editor.toolbar.table',
        'forms:components.rich-editor.toolbar.table_add_column_after' => 'forms::components.rich-editor.toolbar.table-add-column-after',
        'forms:components.rich-editor.toolbar.table_add_column_before' => 'forms::components.rich-editor.toolbar.table-add-column-before',
        'forms:components.rich-editor.toolbar.table_add_row_after' => 'forms::components.rich-editor.toolbar.table-add-row-after',
        'forms:components.rich-editor.toolbar.table_add_row_before' => 'forms::components.rich-editor.toolbar.table-add-row-before',
        'forms:components.rich-editor.toolbar.table_delete' => 'forms::components.rich-editor.toolbar.table-delete',
        'forms:components.rich-editor.toolbar.table_delete_column' => 'forms::components.rich-editor.toolbar.table-delete-column',
        'forms:components.rich-editor.toolbar.table_delete_row' => 'forms::components.rich-editor.toolbar.table-delete-row',
        'forms:components.rich-editor.toolbar.table_merge_cells' => 'forms::components.rich-editor.toolbar.table-merge-cells',
        'forms:components.rich-editor.toolbar.table_split_cell' => 'forms::components.rich-editor.toolbar.table-split-cell',
        'forms:components.rich-editor.toolbar.table_toggle_header_cell' => 'forms::components.rich-editor.toolbar.table-toggle-header-cell',
        'forms:components.rich-editor.toolbar.table_toggle_header_row' => 'forms::components.rich-editor.toolbar.table-toggle-header-row',
        'forms:components.rich-editor.toolbar.text-color' => 'forms::components.rich-editor.toolbar.text-color',
        'forms:components.rich-editor.toolbar.underline' => 'forms::components.rich-editor.toolbar.underline',
        'forms:components.rich-editor.toolbar.undo' => 'forms::components.rich-editor.toolbar.undo',
        'schema::components.callout.danger' => 'schemas::components.callout.danger',
        'schema::components.callout.info' => 'schemas::components.callout.info',
        'schema::components.callout.success' => 'schemas::components.callout.success',
        'schema::components.callout.warning' => 'schemas::components.callout.warning',
        'schema::components.tabs.dropdown-trigger-button' => 'schemas::components.tabs.dropdown-trigger-button',
        'schema::components.tabs.more-tabs-button' => 'schemas::components.tabs.more-tabs-button',
        'schema::components.wizard.completed-step' => 'schemas::components.wizard.completed-step',
        'panels::page.sub-navigation.module-menu.before' => 'panels::page.sub-navigation.mobile-menu.before',
        'panels::page.sub-navigation.module-menu.after' => 'panels::page.sub-navigation.mobile-menu.after',
    ]);

    $rectorConfig->rule(MigrateLegacyConfigurationHooksRector::class);
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
    $rectorConfig->rule(ReplaceLegacyMenuItemsRector::class);
    $rectorConfig->rule(ReplaceLegacyPanelIntegrationRector::class);
    $rectorConfig->rule(ReplaceTypedChartWidgetsRector::class);
    $rectorConfig->rule(WidenAvatarProviderParameterTypeRector::class);
};
