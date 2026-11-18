---
title: Upgrade guide
---

## Introduction

Upgrade from Filament 5 to 6 by running the script, completing the applicable manual steps, and checking the new defaults. The migration tables are a reference for code the script leaves unchanged, not a checklist of renames to perform yourself.

## Upgrading your application

1. Upgrade to **PHP 8.3+ and Laravel 12+**, and check your plugins support Filament 6.
2. Review the manual steps below and migrate authorization overrides before updating Filament. Check existing records with large repeater, builder, or repeatable-entry item counts against the new processing limits before upgrading. Commit your changes; the script edits files directly.
3. Run the following commands, including your application source, shared classes, traits, and tests when prompted:

    ```bash
    composer require filament/upgrade:"^6.0" -W --dev
    vendor/bin/filament-v6

    # Run the Composer commands printed for your app, then:
    composer update
    ```

4. Review the diff and complete the remaining manual steps below.
5. Rebuild frontend bundles, run your tests and static analysis, and check affected workflows with existing records.

> [!NOTE]
> In Windows PowerShell, use `~6.0` instead of `^6.0` if the caret is ignored.

## Completing manual steps

### Moving resource authorization overrides

Resource create, edit, and view pages no longer consult `canCreate()`, `canEdit()`, or `canView()` overrides. **Migrate these before upgrading to avoid allowing access your overrides denied.** Prefer model policies, or replace the overrides with response methods:

| Old override             | Replacement returning `Illuminate\Auth\Access\Response` |
| ------------------------ | ------------------------------------------------------- |
| `canCreate()`            | `getCreateAuthorizationResponse()`                      |
| `canEdit(Model $record)` | `getEditAuthorizationResponse(Model $record)`           |
| `canView(Model $record)` | `getViewAuthorizationResponse(Model $record)`           |

```php
use Illuminate\Auth\Access\Response;

public static function getCreateAuthorizationResponse(): Response
{
    return Response::deny();
}
```

Check parent resources and traits. If calling the parent, use its matching response method and preserve denied responses, including their messages and HTTP statuses. Remove the old `can*()` overrides so boolean checks, pages, and actions share the same rules. The script does not migrate custom authorization rules.

### Preparing queues and integrations

- **Before deployment**, drain queued/retry jobs using `Actions\Exports\Jobs\ExportCompletion` and queued listeners carrying old `ImportChunkProcessed` events. Their class/property names change. Deploy to all workers and restart long-running workers before dispatching new jobs.
- Update monitoring filters: `import{id}` / `export{id}` become `filament:import:{id}` / `filament:export:{id}`. Existing tags and custom `getJobTags()` overrides are unchanged.
- Update custom OpenSpout code for **v5**: use constructors or immutable `with*()` methods instead of setters, and `Cell::fromValue()` / `Row::fromValuesWithStyles()` instead of default row styles. See [customizing XLSX files](../actions/export#customizing-xlsx-files) and [OpenSpout's guide](https://github.com/openspout/openspout/blob/5.x/UPGRADE.md#upgrading-from-4x-to-50).
- Custom avatar providers must implement `get(Model | Authenticatable $record): string`, using `Illuminate\Database\Eloquent\Model` and `Illuminate\Contracts\Auth\Authenticatable`. Review Eloquent-only operations if you support non-Eloquent users. See [avatar providers](../users/overview#using-a-different-avatar-provider).

### Updating assets and custom views

| Area                                         | Manual change                                                                                                                                                                                                                                                             |
| -------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Asset registration                           | Replace `registerScripts()` / `registerStyles()` with `Filament\Support\Facades\FilamentAsset::register()` and `Js::make()` / `Css::make()` from `Filament\Support\Assets`. See [assets](../support/assets).                                                              |
| JavaScript `FilamentNotificationAction`      | `emit()`, `emitSelf()`, `emitTo()` → `dispatch()`, `dispatchSelf()`, `dispatchTo()`                                                                                                                                                                                       |
| Type imports from `@filament/forms/js-field` | `JsFieldInitialProps<Value, Configuration>` → `JsFieldProps<Value, Configuration>`                                                                                                                                                                                        |
| Custom page layouts                          | Read `maxContentWidth`, not `maxWidth`.                                                                                                                                                                                                                                   |
| Sticky modal CSS                             | With `stickyHeader()` or `stickyFooter()`, account for `.fi-modal-window-scroll` between `.fi-modal-window` and its header/content/footer, except in slide-overs and screen-width modals.                                                                                 |
| Badge delete icons                           | Remove `Support\View\SupportIconAlias::BADGE_DELETE_BUTTON` registrations; style `.fi-badge-delete-btn-icon`. There is no replacement alias.                                                                                                                              |
| Blade capture                                | Remove explicit `RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider` registrations, including plugin tests. Filament provides `@capture` / `@endcapture`; require `ryangjchandler/blade-capture-directive` directly only if needed without Filament. |

Custom RTL icons no longer fall back to LTR registrations. Register RTL artwork for `Filament\Support\View\SupportIconAlias::PAGINATION_PREVIOUS_BUTTON_RTL` / `PAGINATION_NEXT_BUTTON_RTL` and `Filament\View\PanelsIconAlias::SIDEBAR_EXPAND_BUTTON_RTL` / `SIDEBAR_COLLAPSE_BUTTON_RTL` (also used in the topbar). Otherwise, built-in RTL icons are used.

### Reviewing custom extensions

The script handles common, statically identifiable PHP and Blade usage. Review anything left unchanged: overrides and `parent::` calls, shared traits, competing old/new configuration, dynamic calls, callable references, computed argument arrays, and published views. Update JavaScript/TypeScript manually. For nullsafe migrations, keep argument evaluation inside the null check.

- Move legacy panel registrations to the intended panel's configuration; legacy registrations targeted the **default panel**, not necessarily the current one.
- When replacing specialized component classes, review subclasses, type declarations, container bindings, and global `configureUsing()` callbacks. For example, `Select::configureUsing()` also affects single selects. Apply mode-dependent defaults after generated setters such as `multiple()` or `iconButton()`.
- Review specialized-component calls across branches, loops, variable aliases, properties, and callbacks manually, especially calls disabling modes the old classes forced. Automatic updates to later calls cover directly assigned local variables in the same straight-line block.
- Migrate separately constructed or dynamically registered menu items manually. Customize built-in user/tenant actions with closures to retain their names and defaults. Review generated `item-N` names for unnamed items with dynamic labels. See [user menus](../navigation/user-menu) and [tenant menus](../users/tenancy#customizing-the-tenant-menu).
- Reconcile inherited hooks and properties together. Canonical properties took precedence over legacy hooks in Filament 5; renaming a property alone can bypass a custom hook. Declare `$isLazyLoaded` and `$isBadgeLazyLoaded` as `protected static ?bool`.
- Include related parent classes and trait consumers in the upgrade paths. Manually review restricted overrides, trait adaptations, trait-defined or grouped properties, direct access to removed parent properties, and declarations outside those paths. When declarations are copied from traits, check differing `strict_types`, `declare(ticks=...)`, magic constants, conditional/nested consumers, and files with multiple namespaces.
- Preserve translated notification defaults for `null`; an empty string disables the notification. Resource label getters now return `string`: preserve model-derived defaults for `null` singular labels and blank plural labels.

## Checking changed defaults

Prefer configuring affected components individually. Put global `configureUsing()` examples in a service provider's `boot()` method.

### Authorizing existing files and attachments

| Component    | New protection                                                                                      | Authorizing additional existing values                                                                                                                        |
| ------------ | --------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `FileUpload` | Rejects paths not already stored on the record; new uploads and cleared values are unaffected.      | `preventFilePathTampering(allowFilePathUsing: ...)`; see [file paths](../forms/file-upload#authorizing-existing-file-paths).                                  |
| `RichEditor` | Rejects image attachment IDs absent from the record's original content; new uploads are unaffected. | `preventFileAttachmentPathTampering(allowFilePathUsing: ...)`; see [attachment IDs](../forms/rich-editor#allowing-additional-data-id-values-with-a-callback). |

Rich-editor validation includes Spatie Media Library attachments, even existing media in the record's collection. Resolving an attachment URL does not authorize its ID. Check defaults, shared libraries, and images copied from other records.

To restore the previous defaults globally:

```php
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;

FileUpload::configureUsing(static fn (FileUpload $component) => $component->preventFilePathTampering(false));
RichEditor::configureUsing(static fn (RichEditor $component) => $component->preventFileAttachmentPathTampering(false));
```

> [!WARNING]
> Disabling either protection makes you responsible for authorizing submitted paths/IDs, including cross-user and cross-tenant access. URL resolution alone is not authorization.

### Handling spreadsheet formula protection

Exports and import failure CSVs prefix strings starting with `=`, `+`, `-`, `@`, a tab, or a carriage return with a single quote. Numeric strings such as `-5` are unchanged; values such as `+44 1234 567890` change and may affect downstream consumers.

For **trusted data only**, restore the previous defaults globally:

```php
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Imports\Importer;

ExportColumn::configureUsing(static fn (ExportColumn $column) => $column->preventFormulaInjection(false));
Importer::preventFormulaInjection(false);
```

For one column, use `preventFormulaInjection(false)`; for one importer, redeclare `protected static bool $shouldPreventFormulaInjection = false`. Disabling protection allows spreadsheet formulas. See [exports](../actions/export#csv-formula-injection) and [import failure CSVs](../actions/import#csv-formula-injection).

### Checking processing and authentication limits

| Area                                         | New default and consequence                                                                                                                                                                                         | Configuration                                                                                                                                                                                                                                                      |
| -------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Bulk selections                              | 10,000 records. Database-backed actions process a subset of oversized selections, including inverse selections; custom-data tables throw `LogicException`. The all-pages select-all link is hidden above the limit. | `Table::maxSelectableRecords()`; `null` restores unlimited selection. [Details](../tables/actions#limiting-the-number-of-selectable-records).                                                                                                                      |
| `Repeater` / `Builder` / `RepeatableEntry`   | 500 / 500 / 1,000 items per component. Excess items abort with HTTP 413, without truncation, including when opening existing records.                                                                               | `maxItemsForProcessing()`; `null` restores unlimited processing. Separate from `maxItems()` validation. [Details](../forms/repeater#limiting-item-processing).                                                                                                     |
| Table pagination                             | Submitted sizes must match configured options; invalid sizes fall back to a valid saved selection or the default. Stale saved selections use the first option.                                                      | Include PHP-set sizes in the options, or use `enforcePaginationPageOptions(false)`. Explicit `'all'` and `paginated(false)` remain unrestricted. [Details](../tables/overview#enforcing-the-pagination-options).                                                   |
| Query Builder filters                        | 100 conditions, depth 5. **Oversized filters are ignored: no constraints are applied.**                                                                                                                             | `maxRules()` / `maxNestingDepth()`; `null` removes each limit. [Details](../tables/filters/query-builder#limiting-the-size-of-the-rule-tree).                                                                                                                      |
| App authentication                           | Current 30-second period and one period either side, previously eight.                                                                                                                                              | Synchronize clocks; `AppAuthentication::make()->codeWindow(8)` temporarily restores tolerance. [Details](../users/multi-factor-authentication#changing-the-app-code-expiration-time).                                                                              |
| Livewire uploads with `InteractsWithSchemas` | HTTP 403 unless the target belongs to an upload-supporting schema component. Filament fields are unaffected.                                                                                                        | For direct property uploads, return `false` from `shouldRestrictFileUploadsToSchemaComponents(): bool` and secure uploads yourself. Remove the obsolete restriction trait. [Details](../advanced/security#restricting-livewire-file-uploads-to-schema-components). |

Raise limits only where needed: they are not memory, request-size, or execution-time guarantees. Disabling Query Builder limits risks excessive resource consumption. Check [bulk-action performance](../tables/actions#improving-the-performance-of-bulk-actions) before increasing selection limits. To restore the previous processing defaults globally:

```php
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;

Table::configureUsing(static fn (Table $table) => $table
    ->maxSelectableRecords(null)
    ->enforcePaginationPageOptions(false));
Repeater::configureUsing(static fn (Repeater $component) => $component->maxItemsForProcessing(null));
Builder::configureUsing(static fn (Builder $component) => $component->maxItemsForProcessing(null));
RepeatableEntry::configureUsing(static fn (RepeatableEntry $component) => $component->maxItemsForProcessing(null));
QueryBuilder::configureUsing(static fn (QueryBuilder $filter) => $filter->maxRules(null)->maxNestingDepth(null));
```

## Migrating remaining API references

PHP names below are relative to `Filament` unless stated otherwise. Within each row, old and new names correspond in order.

### Migrating actions

| Owner                                                  | Old                                                              | New                                                                      |
| ------------------------------------------------------ | ---------------------------------------------------------------- | ------------------------------------------------------------------------ |
| `Actions\Action`                                       | `emit()`, `emitSelf()`, `emitTo()`                               | `dispatch()`, `dispatchSelf()`, `dispatchTo()`                           |
| `Actions\Action`                                       | `disableForm()`, `disabledForm()`                                | `disabledSchema()`                                                       |
| `Actions\Action`                                       | `form()`, `infolist()`; `getForm()`, `isFormDisabled()`          | `schema()`; `getSchema()`, `isSchemaDisabled()`                          |
| `Actions\Action`                                       | `mutateFormDataUsing()`, `formData()`                            | `mutateDataUsing()`, `data()`                                            |
| `Actions\Action`                                       | `resetFormData()`, `getFormData()`, `getRawFormData()`           | `resetData()`, `getData()`, `getRawData()`                               |
| `Actions\Action`                                       | `modalActions()`, `extraModalActions()`                          | `modalFooterActions()`, `extraModalFooterActions()`                      |
| `Actions\Action`                                       | `modalButton()`, `modalFooter()`, `modalSubheading()`            | `modalSubmitActionLabel()`, `modalContentFooter()`, `modalDescription()` |
| `Actions\Action`                                       | `makeExtraModalAction()`                                         | `makeModalSubmitAction()`                                                |
| `Actions\Action`                                       | `failureNotificationMessage()`, `successNotificationMessage()`   | `failureNotificationTitle()`, `successNotificationTitle()`               |
| `Actions\Action`                                       | `withAttributes()`, `hold()`                                     | `extraAttributes()`, `halt()`                                            |
| `Actions\Action`                                       | `hasCustomLivewireClickHandler()`, `getJavaScriptClickHandler()` | `hasLivewireClickHandlerEnabledConfiguration()`, `getJsClickHandler()`   |
| Actions, action groups, schema components using labels | `disableLabel()`                                                 | `hiddenLabel()`; do not negate the condition                             |
| Actions, action groups, schema tabs                    | `indicatorColor()`                                               | `badgeColor()`                                                           |
| `Actions\ReplicateAction`                              | `afterReplicaSaved()`                                            | `after()`                                                                |
| `Actions\ExportAction`, `ExportBulkAction`             | `getFileDisk()`, `isEnablingVisibleTableColumnsByDefault()`      | `getFileDiskName()`, `shouldEnableVisibleTableColumnsByDefault()`        |
| `Actions\Exports\Exporter`                             | `getFileDisk()`                                                  | `getFileDiskName()`                                                      |
| `Actions\Imports\ImportColumn`                         | `guess()`, `getExample()`                                        | `guesses()`, `Illuminate\Support\Arr::first($column->getExamples())`     |
| `Actions\Imports\Events\ImportChunkProcessed`          | `getProcessedRows()`, `getSuccessfulRows()`                      | `getProcessedRowsCount()`, `getSuccessfulRowsCount()`                    |
| `Actions\Concerns\InteractsWithActions`                | `mountedActionHasForm()`, `getMountedActionForm()`               | `mountedActionHasSchema()`, `getMountedActionSchema()`                   |

### Migrating forms, infolists, and schemas

| Owner                                      | Old                                                                                              | New                                                                                     |
| ------------------------------------------ | ------------------------------------------------------------------------------------------------ | --------------------------------------------------------------------------------------- |
| Form/infolist Livewire components          | `cacheForm()`, `cacheInfolist()`; `getForm()`, `getInfolist()`                                   | `cacheSchema()`; `getSchema()`                                                          |
| Form/infolist Livewire components          | `hasCachedForm()`, `hasCachedInfolist()`; `getCachedForms()`, `getCachedInfolists()`             | `hasCachedSchema()`; `getCachedSchemas()`                                               |
| Form Livewire components                   | `isCachingForms()`, `getActiveFormsLocale()`, `getOldFormState()`                                | `isCachingSchemas()`, `getActiveSchemaLocale()`, `getOldSchemaState()`                  |
| Form `BaseFileUpload`                      | `enableDownload()`, `enableOpen()`, `enableReordering()`                                         | `downloadable()`, `openable()`, `reorderable()`                                         |
| Form `BaseFileUpload`                      | `moveFile()`, `storeFile()`                                                                      | `moveFiles()`, `storeFiles()`                                                           |
| Form `Builder`, `Repeater`                 | `createItemButtonLabel()`, `getRawItemState()`                                                   | `addActionLabel()`, `getItemStateSnapshot()`                                            |
| Form `Builder`                             | `createItemBetweenButtonLabel()`; `showBlockLabels()`, `withBlockLabels()`, `withBlockNumbers()` | `addBetweenActionLabel()`; `blockLabels()`, `blockLabels()`, `blockNumbers()`           |
| Form `Repeater`                            | `orderable()`                                                                                    | `orderColumn()`                                                                         |
| Form `KeyValue`                            | `addButtonLabel()`, `deleteButtonLabel()`, `reorderButtonLabel()`                                | `addActionLabel()`, `deleteActionLabel()`, `reorderActionLabel()`                       |
| Form `FileUpload`                          | `idleLabel()`, `imageResizeMode()`                                                               | `placeholder()`, `automaticallyResizeImagesMode()`                                      |
| Form `FileUpload`                          | `imageResizeTargetHeight()`, `imageResizeTargetWidth()`                                          | `automaticallyResizeImagesToHeight()`, `automaticallyResizeImagesToWidth()`             |
| Form `FileUpload`                          | `imageResizeUpscale()`, `orientImageFromExif()`                                                  | `automaticallyUpscaleImagesWhenResizing()`, `orientImagesFromExif()`                    |
| Form `FileUpload`                          | `getImageCropAspectRatio()`, `getImageResizeMode()`                                              | `getAutomaticallyCropImagesAspectRatio()`, `getAutomaticallyResizeImagesMode()`         |
| Form `FileUpload`                          | `getImageResizeTargetHeight()`, `getImageResizeTargetWidth()`                                    | `getAutomaticallyResizeImagesHeight()`, `getAutomaticallyResizeImagesWidth()`           |
| Form `FileUpload`                          | `getImageResizeUpscale()`, `getParentTargetSizes()`                                              | `shouldAutomaticallyUpscaleImagesWhenResizing()`, `getImageEditorViewportScaleFactor()` |
| Form `FileUpload`                          | `imageEditorAspectRatios()`, `getImageEditorAspectRatiosForJs()`                                 | `imageEditorAspectRatioOptions()`, `getImageEditorAspectRatioOptionsForJs()`            |
| Form `MarkdownEditor`, `RichEditor`        | `getUploadedAttachmentUrlUsing()`, `saveUploadedFileAttachmentsUsing()`                          | `getFileAttachmentUrlUsing()`, `saveUploadedFileAttachmentUsing()`                      |
| Form `RichEditor\MentionProvider`          | `hasSearchResultsUsing()`                                                                        | `hasSearchResultsUsingCallback()`                                                       |
| Form `RichEditor\RichContentAttribute`     | `getCustomBlocksConfig()`                                                                        | `getCustomBlocksConfiguration()`                                                        |
| Form `SpatieMediaLibraryFileUpload`        | `getConversionsDisk()`                                                                           | `getConversionsDiskName()`                                                              |
| Form `MorphToSelect\Type`                  | `titleColumnName()`                                                                              | `titleAttribute()`                                                                      |
| Form `Field`, schema `FusedGroup`          | `getExtraFieldWrapperAttributesBag()`                                                            | `getExtraFieldWrapperAttributeBag()`                                                    |
| Infolist `Entry`                           | `getExtraEntryWrapperAttributesBag()`                                                            | `getExtraEntryWrapperAttributeBag()`                                                    |
| Infolist `RepeatableEntry`                 | `getItemState()`                                                                                 | `getItemStateWithExistence()`                                                           |
| Infolist `KeyValueEntry`                   | `emptyMessage()`                                                                                 | `placeholder()`                                                                         |
| Infolist `ImageEntry`, table `ImageColumn` | `height()`, `size()`, `getHeight()`                                                              | `imageHeight()`, `imageSize()`, `getImageHeight()`                                      |
| Infolist `ImageEntry`                      | `width()`, `getWidth()`                                                                          | `imageWidth()`, `getImageWidth()`                                                       |
| Schema and components                      | `reactive()`; `clearCachedDefaultChildSchemas()`                                                 | `live()`; `clearCachedChildSchemas()`                                                   |
| Form components using `HasAffixes`         | `postfix()`                                                                                      | `suffix()`                                                                              |
| `Schemas\Schema`, `Tables\Table`           | `deferLoading()`, `isLoadingDeferred()`                                                          | `lazyLoaded()`, `isLazyLoaded()`                                                        |
| Schema `Livewire`, form `LivewireField`    | `lazy()`, `isLazy()`                                                                             | `lazyLoaded()`, `isLazyLoaded()`                                                        |
| Other schema components and `Schema`       | `lazy()`, `isLazy()`                                                                             | `live(onBlur: true)`, `isLiveOnBlur()`                                                  |
| Schema `Tab`, resource `RelationGroup`     | `deferBadge()`, `isBadgeDeferred()`                                                              | `lazyLoadedBadge()`, `isBadgeLazyLoaded()`                                              |
| Schema `Tabs`                              | `getDeferredTabBadges()`, `hasDeferredBadges()`                                                  | `getLazyLoadedTabBadges()`, `hasLazyLoadedBadges()`                                     |
| `Schemas\Schema`                           | `name()`, `getName()`, `context()`                                                               | `key()`, `getKey()`, `operation()`                                                      |
| Schema components                          | `getGetCallback()`, `getSetCallback()`                                                           | `makeGetUtility()`, `makeSetUtility()`                                                  |
| Schema components                          | `getChildComponentContainer()`, `getChildComponentContainers()`                                  | `getChildSchema()`, `getChildSchemas()`                                                 |
| `Schemas\Components\Component`             | `isConcealed()`                                                                                  | `false` for the built-in implementation                                                 |
| Schema `Wizard\Step`                       | `afterValidated()`                                                                               | `afterValidation()`                                                                     |
| Schema `Section`                           | `formBefore()`, `isFormBefore()`                                                                 | `contentBefore()`, `isContentBefore()`                                                  |
| Schema `Utilities\Get`, `Utilities\Set`    | `skipComponentsChildContainersWhileSearching()`                                                  | `skipComponentsChildSchemasWhileSearching()`                                            |

### Migrating tables

| Owner                                          | Old                                                                                            | New                                                                                                 |
| ---------------------------------------------- | ---------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------- |
| `Tables\Table`                                 | `actions()`, `pushActions()`                                                                   | `recordActions()`, `pushRecordActions()`                                                            |
| `Tables\Table`                                 | `actionsColumnLabel()`, `actionsAlignment()`, `actionsPosition()`                              | `recordActionsColumnLabel()`, `recordActionsAlignment()`, `recordActionsPosition()`                 |
| `Tables\Table`                                 | `getActions()`, `getActionsPosition()`, `getActionsAlignment()`, `getActionsColumnLabel()`     | Corresponding `getRecordActions*()` methods                                                         |
| `Tables\Table`                                 | `bulkActions()`, `pushBulkActions()`, `getBulkActions()`                                       | `toolbarActions()`, `pushToolbarActions()`, `getToolbarActions()`                                   |
| `Tables\Table`                                 | `toggleColumnsTriggerAction()`, `getToggleColumnsTriggerAction()`                              | `columnManagerTriggerAction()`, `getColumnManagerTriggerAction()`                                   |
| `Tables\Table`                                 | `columnToggleFormColumns()`, `columnToggleFormMaxHeight()`, `columnToggleFormWidth()`          | `columnManagerColumns()`, `columnManagerMaxHeight()`, `columnManagerWidth()`                        |
| `Tables\Table`                                 | `getColumnToggleFormColumns()`, `getColumnToggleFormMaxHeight()`, `getColumnToggleFormWidth()` | `getColumnManagerColumns()`, `getColumnManagerMaxHeight()`, `getColumnManagerWidth()`               |
| `Tables\Table`                                 | `groupsInDropdownOnDesktop()`; `getInverseRelationship()`                                      | `groupingSettingsInDropdownOnDesktop()`; `getInverseRelationshipName()`                             |
| Table Livewire components                      | `updatedToggledTableColumns()`, `getToggledTableColumnsSessionKey()`                           | `applyTableColumnManager()` with no arguments, `getTableColumnsSessionKey()`                        |
| Table Livewire components                      | `updatedTableGroupColumn()`, `updatedTableSortDirection()`                                     | `updatedTableGrouping()`, `updatedTableSort()`                                                      |
| Table `ImageColumn`                            | `rounded()`, `isRounded()`                                                                     | `circular()`, `isCircular()`                                                                        |
| Table `IconColumn`                             | `options()`                                                                                    | `icons()`                                                                                           |
| Table `Column`                                 | `disableClick()`                                                                               | `disabledClick()`; do not negate the condition                                                      |
| Table `Column`, `Actions\Exports\ExportColumn` | `queriesRelationships()`, `getGetStateUsingCallback()`                                         | `hasRelationship()`, `getStateUsingCallback()`                                                      |
| Table `Summarizers\Summarizer`                 | `hasQueryModification()`                                                                       | `hasQueryModificationCallback()`                                                                    |
| Table `Filters\BaseFilter`                     | `schema()`, `getSchemaComponents()`, `hasSchema()`, `getSchema()`                              | `form()`, `getFormSchema()`, `hasForm()`, `getForm()`                                               |
| Table `Filters\SelectFilter`                   | `column()`, `getColumn()`; `queriesRelationships()`, `getRelationshipKey()`                    | `attribute()`, `getAttribute()`; `hasRelationship()`, `getQualifiedRelatedKeyNameForRelationship()` |
| Table `Grouping\Group`                         | `getDescriptionUsing()`                                                                        | `getDescriptionFromRecordUsing()`                                                                   |

### Migrating panels, pages, and widgets

| Owner                                                                            | Old                                                                                                                      | New                                                                                            |
| -------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------ | ---------------------------------------------------------------------------------------------- |
| `Navigation\NavigationItem`                                                      | `isActiveWhen()`, `isChildItemsActive()`                                                                                 | `activeWhen()`, `hasActiveChildItems()`                                                        |
| `Clusters\Cluster`                                                               | `unshiftClusterBreadcrumbs()`                                                                                            | `prependClusterBreadcrumbs()`                                                                  |
| `Panel`                                                                          | `passwordResetRequestRouteSlug()`, `passwordResetRouteSlug()`, `getResetPasswordRoutePrefix()`                           | `requestPasswordResetRouteSlug()`, `resetPasswordRouteSlug()`, `getPasswordResetRoutePrefix()` |
| `Auth\MultiFactor\Email\EmailAuthentication`                                     | `generateCodesUsing()`                                                                                                   | `generateCodeUsing()`                                                                          |
| `Panel`                                                                          | `databaseNotificationsPolling()`                                                                                         | `databaseNotificationsPollingInterval()`                                                       |
| `Panel`, `FilamentManager`, `Facades\Filament`                                   | `hasDarkModeForced()`                                                                                                    | `isDarkModeForced()`                                                                           |
| `FilamentManager`, `Facades\Filament`                                            | `currentDomain()`                                                                                                        | `setCurrentDomain()`                                                                           |
| `FilamentManager`, `Facades\Filament`                                            | `renderHook()`, `registerRenderHook()`                                                                                   | Same methods on `Support\Facades\FilamentView`                                                 |
| `FilamentManager`, `Facades\Filament`                                            | `registerScriptData()`                                                                                                   | `Support\Facades\FilamentAsset::registerScriptData()`                                          |
| `FilamentManager`, `Facades\Filament`                                            | `registerNavigationGroups()`, `registerNavigationItems()`, `registerPages()`, `registerResources()`, `registerWidgets()` | Panel `navigationGroups()`, `navigationItems()`, `pages()`, `resources()`, `widgets()`         |
| `FilamentManager`, `Facades\Filament`                                            | `registerTheme()`, `registerViteTheme()`, `registerUserMenuItems()`                                                      | Panel `theme()`, `viteTheme()`, `userMenuItems()`                                              |
| `Pages\BasePage`                                                                 | `alignFormActionsLeft()`, `alignFormActionsRight()`                                                                      | `alignFormActionsStart()`, `alignFormActionsEnd()`                                             |
| `Pages\Page`                                                                     | `getActions()`                                                                                                           | `getHeaderActions()`                                                                           |
| Pages                                                                            | `$maxWidth`, `getMaxWidth()`; `Pages\Concerns\HasMaxWidth`                                                               | `$maxContentWidth`, `getMaxContentWidth()`; remove trait                                       |
| `Auth\Pages\EditProfile`                                                         | `backAction()`                                                                                                           | `getCancelFormAction()`                                                                        |
| Resource `CreateRecord`                                                          | `getCreatedNotificationMessage()`                                                                                        | `getCreatedNotificationTitle()`                                                                |
| Resource `EditRecord`, settings pages                                            | `getSavedNotificationMessage()`                                                                                          | `getSavedNotificationTitle()`                                                                  |
| `Resources\Resource`                                                             | `getLabel()`, `getPluralLabel()`; `$label`, `$pluralLabel`                                                               | `getModelLabel()`, `getPluralModelLabel()`; `$modelLabel`, `$pluralModelLabel`                 |
| Dashboard `HasFilters`                                                           | `normalizeTableFilterValuesFromQueryString()`                                                                            | `normalizeFilterValuesFromQueryString()`                                                       |
| Resource/table/relation-manager/related-record page generators                   | `getTableActions()`, `outputTableActions()`, `outputTableMethodBulkActions()`                                            | `getTableRecordActions()`, `outputTableRecordActions()`, `outputTableBulkActions()`            |
| `Notifications\Livewire\Notifications`                                           | `pullNotificationsFromSession()`                                                                                         | `pullNotifications()`                                                                          |
| `Notifications\Livewire\DatabaseNotifications`, `Livewire\DatabaseNotifications` | `getTrigger()`                                                                                                           | `renderTrigger()`; remains internal, not for new extensions                                    |
| Widgets, relation managers                                                       | `isLazy()`, `$isLazy`                                                                                                    | `isLazyLoaded()`, `$isLazyLoaded`                                                              |
| Relation managers                                                                | `isBadgeDeferred()`, `$isBadgeDeferred`                                                                                  | `isBadgeLazyLoaded()`, `$isBadgeLazyLoaded`                                                    |
| Rich-content custom-block generators                                             | `configureToPreviewMethod()`                                                                                             | `configureToPreviewHtmlMethod()`                                                               |
| Chart widgets using `HasFiltersForm`                                             | `filtersSchema()`, `getFiltersSchema()`, `resetFiltersForm()`                                                            | `filtersForm()`, `getFiltersForm()`, `resetFilters()`                                          |
| `Auth\MultiFactor\MultiFactorChallenge`                                          | `getSchemaComponents()`, `getProviderPickerSchemaComponent()`, `getChallengeSchemaComponents()`                          | `getFormComponents()`, `getProviderPickerFormComponent()`, `getChallengeFormComponents()`      |
| `Widgets\StatsOverviewWidget`                                                    | `getCards()`                                                                                                             | `getStats()`                                                                                   |
| `Support` functions                                                              | `format_number()`, `format_money()`                                                                                      | `Illuminate\Support\Number::format()`, `Number::currency()`; adapt arguments where needed      |

### Replacing configuration setters

For opposite-condition replacements, preserve closure evaluation rather than negating a closure object. Migrated callbacks use the canonical component's evaluation context; review code relying on captured originals when cloning components. Enum names below refer to `Filament\Support\Enums`.

| Owner                                                     | Old                                                                                              | New                                                                                                                                      |
| --------------------------------------------------------- | ------------------------------------------------------------------------------------------------ | ---------------------------------------------------------------------------------------------------------------------------------------- |
| Form `BaseFileUpload`                                     | `disablePreview()`                                                                               | `previewable()` with opposite condition                                                                                                  |
| Form `Builder`, `Repeater`                                | `disableItemCreation()`, `disableItemDeletion()`, `disableItemMovement()`                        | `addable()`, `deletable()`, `reorderable()` with opposite condition                                                                      |
| Form `KeyValue`                                           | `disableAddingRows()`, `disableDeletingRows()`, `disableEditingKeys()`, `disableEditingValues()` | `addable()`, `deletable()`, `editableKeys()`, `editableValues()` with opposite condition                                                 |
| Form `TextInput`, `Textarea`                              | `disableAutocomplete()`                                                                          | `autocomplete(false)` when disabled; `autocomplete(null)` otherwise                                                                      |
| Form `TextInput`                                          | `disableAutocapitalize()`                                                                        | `autocapitalize(false)` when disabled; `autocapitalize(null)` otherwise                                                                  |
| Form `Select`                                             | `disablePlaceholderSelection()`                                                                  | `selectablePlaceholder()` with opposite condition                                                                                        |
| Form `DateTimePicker`, `DatePicker`, `TimePicker`         | `withoutDate()`, `withoutSeconds()`, `withoutTime()`                                             | `date()`, `seconds()`, `time()` with opposite condition                                                                                  |
| Form `DateTimePicker`, `DatePicker`, `TimePicker`         | `icon()`                                                                                         | `suffixIcon($icon ?? Heroicon::Calendar, isInline: true)`; `false` keeps existing icon. `Heroicon` is `Filament\Support\Icons\Heroicon`. |
| Form `FileUpload`                                         | `imageCropAspectRatio()`                                                                         | Both `imageAspectRatio()` and `automaticallyCropImagesToAspectRatio()`                                                                   |
| `Actions\Action` and built-in subclasses                  | `centerModal()`                                                                                  | Apply `modalAlignment(Alignment::Center)` only if the eagerly evaluated condition is truthy                                              |
| `Actions\CreateAction`, `AttachAction`, `AssociateAction` | `disableCreateAnother()`, `disableAttachAnother()`, `disableAssociateAnother()`                  | `createAnother()`, `attachAnother()`, `associateAnother()` with opposite condition                                                       |
| Actions, action groups, schema tabs                       | `indicator()`                                                                                    | `badge()` with a value; stringify numeric action/action-group values                                                                     |

Custom consumers of support `HasBadge` need their own `badge()` API.

### Updating named arguments and extension contracts

Update matching overrides, properties, and named argument/container keys, not just callers. Resolve competing old/new implementations first.

| Owner                                                                                                                                                         | Old                                                                         | New                                                                         |
| ------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------- | --------------------------------------------------------------------------- |
| `Auth\MultiFactor\Email\Contracts\HasEmailAuthentication`                                                                                                     | `toggleEmailAuthentication()`                                               | `saveEmailAuthentication()`                                                 |
| `Support\Contracts\TranslatableContentDriver`                                                                                                                 | `getRecordAttributesToArray()`                                              | `recordAttributesToArray()`                                                 |
| `Support\Exceptions\Halt`, `Cancel`                                                                                                                           | `shouldRollbackDatabaseTransaction()`, `$shouldRollbackDatabaseTransaction` | `shouldRollBackDatabaseTransaction()`, `$shouldRollBackDatabaseTransaction` |
| `Actions\Contracts\HasActions::getAction()`                                                                                                                   | `$name`                                                                     | `$actions`                                                                  |
| `Actions\Imports\Events\ImportChunkProcessed` constructor/properties                                                                                          | `$processedRows`, `$successfulRows`                                         | `$processedRowsCount`, `$successfulRowsCount`                               |
| Form `RichEditor::plugins()`                                                                                                                                  | `$extensions`                                                               | `$plugins`                                                                  |
| Form `Slider::minValue()`, `maxValue()`; `minDifference()`                                                                                                    | `$minValue`, `$maxValue`; `$minDifference`                                  | `$value`; `$difference`                                                     |
| Form `Builder\Block::maxItems()`                                                                                                                              | `$maxItems`                                                                 | `$count`                                                                    |
| Form fields/table `TextInputColumn`: `inlinePrefix()`, `inlineSuffix()`                                                                                       | `$isInline`                                                                 | `$condition`                                                                |
| Form `BaseFileUpload::disk()`                                                                                                                                 | `$name`                                                                     | `$disk`                                                                     |
| `RichContentCustomBlock::toHtml()`, `toPreviewHtml()`, `getPreviewLabel()`, `shouldApplyProseStylingToPreview()`; `RichContentRenderer::getCustomBlockHtml()` | `$config`                                                                   | `$configuration`                                                            |
| `Navigation\NavigationItem::parentItem()`                                                                                                                     | `$group`                                                                    | `$parentItem`                                                               |
| `Resources\ParentResourceRegistration::getInverseRelationship()`                                                                                              | `$parentRecord`                                                             | `$childRecord`                                                              |
| `Schemas\Schema::hidden()`; schema `Actions::fullWidth()`                                                                                                     | `$hidden`; `$isFullWidth`                                                   | `$condition`                                                                |
| Schema `Get`: `string()`, `integer()`, `float()`, `boolean()`, `array()`, `date()`, `enum()`, `filled()`, `blank()`                                           | `$key`                                                                      | `$path`                                                                     |
| Infolist entries/table columns: `iconPosition()`                                                                                                              | `$iconPosition`                                                             | `$position`                                                                 |
| `Schemas\Components\StateCasts\BooleanStateCast` constructor/property                                                                                         | `$isStoredAsInt`                                                            | `$isStoredAsInteger`                                                        |
| Command `askForSchema()`                                                                                                                                      | `$intialQuestion`                                                           | `$initialQuestion`                                                          |
| `reorderAnimationDuration()`                                                                                                                                  | `$animation`                                                                | `$duration`                                                                 |
| `Notifications\Notification::sendToDatabase()`                                                                                                                | `$isEventDispatched`                                                        | `$shouldDispatchEvent`                                                      |
| Table Livewire `updateTableColumnState()`                                                                                                                     | `$column`, `$record`, `$input`                                              | `$name`, `$recordKey`, `$state`                                             |
| `GlobalSearch\Providers\Contracts\GlobalSearchProvider::getResults()` and implementations                                                                     | `$query`                                                                    | `$search`                                                                   |
| `Panel::databaseNotifications()`                                                                                                                              | `$isLazy`                                                                   | `$isLazyLoaded`                                                             |
| `Support\View\Components\ColorMaps\ButtonComponentColorMap::lightBackground()`, `darkBackground()`                                                            | `$bg`                                                                       | `$background`                                                               |
| Same class: `probeTextLightness()`                                                                                                                            | `$textForBg`                                                                | `$textForBackground`                                                        |
| `Pages\BasePage::halt()`                                                                                                                                      | `$shouldRollbackDatabaseTransaction`                                        | `$shouldRollBackDatabaseTransaction`                                        |
| Page configuration                                                                                                                                            | `$formActionsAreSticky`                                                     | `$areFormActionsSticky`                                                     |

### Migrating testing assertions

| Old                                                                                                                                                                      | New                                                                                               |
| ------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------------- |
| `assertFormFieldIsDisabled()`, `assertFormFieldIsEnabled()`, `assertFormFieldIsReadOnly()`                                                                               | `assertFormFieldDisabled()`, `assertFormFieldEnabled()`, `assertFormFieldReadOnly()`              |
| `assertFormFieldIsHidden()`, `assertFormFieldIsVisible()`                                                                                                                | `assertFormFieldHidden()`, `assertFormFieldVisible()`                                             |
| `assertActionHeld()`                                                                                                                                                     | `assertActionHalted()`                                                                            |
| `assertTableActionsExistInOrder()`                                                                                                                                       | `assertTableRecordActionsExistInOrder()`                                                          |
| `setActionData()`, `setFormComponentActionData()`, `setInfolistActionData()`, `setTableActionData()`, `setTableBulkActionData()`                                         | `fillForm()`                                                                                      |
| `assertActionDataSet()`, `assertFormComponentActionDataSet()`, `assertInfolistActionDataSet()`, `assertTableActionDataSet()`, `assertTableBulkActionDataSet()`           | `assertSchemaStateSet()`; named `data:` → `state:`                                                |
| `assertHasActionErrors()`, `assertHasFormComponentActionErrors()`, `assertHasInfolistActionErrors()`, `assertHasTableActionErrors()`, `assertHasTableBulkActionErrors()` | `assertHasFormErrors()`; corresponding `assertHasNo*Errors()` → `assertHasNoFormErrors()`         |
| `assertFormSet()`, `assertFormExists()`                                                                                                                                  | `assertSchemaStateSet()`, `assertSchemaExists()`; preserve explicit schema name, default `'form'` |
| `assertFormComponentExists()`, `assertFormComponentDoesNotExist()`                                                                                                       | `assertSchemaComponentExists()`, `assertSchemaComponentDoesNotExist()`                            |
| `assertFormComponentAction*()`, `assertInfolistAction*()`                                                                                                                | Corresponding `assertAction*()` with schema-component context                                     |
| `assertTableAction*()`, `assertTableBulkAction*()`                                                                                                                       | Corresponding `assertAction*()` with table/bulk/record context                                    |
| Context-specific `*Halted()`, `assertTableActionHeld()`, `assertTableBulkActionHeld()`                                                                                   | `assertActionMounted()` with original context; checks mounted state, not execution halting        |
| `assertTableBulkActionsExistInOrder()`                                                                                                                                   | `assertActionListInOrder()` with toolbar actions, `'table bulk'`, and `Actions\BulkAction::class` |

- Data/error assertions still use the mounted-action or default schema. Legacy form assertions explicitly target `'form'`. Move a second-argument check closure in `assertFormComponentExists()` to the canonical third argument.
- Contextual assertions use `Actions\Testing\TestAction::make()` with `table()`, `bulk()`, or `schemaComponent()`. The script reuses public `parseNested*Actions()` helpers where needed for dynamic/nested lists. Preserve names, record keys, arguments, and the legacy `'form'` / `'infolist'` defaults.
- Review contextual first-class callables and unknown unpacked argument lists manually. Data callable aliases are renamed directly; change named `data:` to `state:` when invoking them.
- Action mounting, unmounting, calling, and nondeprecated table action-order assertions remain supported.

### Replacing classes, traits, and enum names

| Old                                                                                                        | New                                                                                                    |
| ---------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------ |
| `Actions\ButtonAction`, `Actions\IconButtonAction`                                                         | `Actions\Action::make()->button()`, `->iconButton()`                                                   |
| `Forms\Components\MultiSelect`                                                                             | `Forms\Components\Select::make()->multiple()`                                                          |
| `Forms\Components\RelationshipRepeater`                                                                    | `Forms\Components\Repeater::make()->relationship()`                                                    |
| `Forms\Components\Placeholder`                                                                             | `Infolists\Components\TextEntry`; `content()` / `getContent()` → `state()` / `getState()`              |
| `Tables\Columns\BadgeColumn`, `TagsColumn`                                                                 | `Tables\Columns\TextColumn::make()->badge()`; tags `limit()` → `limitList()`                           |
| `Tables\Columns\BooleanColumn`                                                                             | `Tables\Columns\IconColumn::make()->boolean()`                                                         |
| `Tables\Filters\MultiSelectFilter`                                                                         | `Tables\Filters\SelectFilter::make()->multiple()`                                                      |
| `Navigation\MenuItem`                                                                                      | `Actions\Action` in user/tenant menus                                                                  |
| `Schemas\Components\StateCasts\OptionsArrayStateCast`                                                      | `Schemas\Components\StateCasts\OptionArrayStateCast`                                                   |
| `Support\View\Components\Contracts\HasColor`                                                               | `Support\View\Components\Contracts\HasColorMap`                                                        |
| `Schemas\View\SchemaIconAlias`                                                                             | `Schemas\View\SchemasIconAlias`                                                                        |
| `Widgets\View\Components\StatsOverviewWidgetComponent\StatComponent\StatsOverviewWidgetStatChartComponent` | `Widgets\View\Components\StatsOverviewWidgetComponent\StatComponent\ChartComponent`                    |
| `Forms\Components\Concerns\CanLimitItemsLength`                                                            | `Forms\Components\Concerns\CanLimitItemsCount`                                                         |
| `Tables\Columns\Concerns\HasRowLoopObject`                                                                 | `Tables\Columns\Concerns\HasRowLoop`                                                                   |
| `Support\Concerns\CanBeLazy`                                                                               | `Support\Concerns\CanBeLazyLoaded`                                                                     |
| `Tables\Table\Concerns\CanDeferLoading`, `Tables\Concerns\CanDeferLoading`                                 | `CanBeLazyLoaded` in the same namespace                                                                |
| `Tables\Columns\Concerns\CanBeCopied`                                                                      | `Support\Concerns\CanBeCopied`                                                                         |
| `Tables\Actions\HeaderActionsPosition`                                                                     | `Tables\Enums\HeaderActionsPosition`                                                                   |
| `Tables\Filters\QueryBuilder\Constraints\*` (operators/traits included)                                    | `QueryBuilder\Constraints\*`                                                                           |
| `Actions\Concerns\HasInfolist`                                                                             | `Actions\Concerns\HasSchema`, or remove redundant use                                                  |
| `Tables\Filters\Concerns\HasSchema`                                                                        | `Tables\Filters\Concerns\HasForm`                                                                      |
| `Widgets\ChartWidget\Concerns\HasFiltersSchema`                                                            | `Widgets\ChartWidget\Concerns\HasFiltersForm`                                                          |
| `Schemas\Concerns\RestrictsFileUploadsToSchemaComponents`                                                  | Remove; restriction is built into `InteractsWithSchemas`                                               |
| `Support\Components\Attributes\ExposedLivewireMethod`                                                      | `Support\Components\Attributes\Exposed` on methods                                                     |
| `Actions\Exports\Jobs\ExportCompletion`                                                                    | `Actions\Exports\Jobs\CompleteExport`; drain old jobs first                                            |
| `Widgets\BarChartWidget`, `BubbleChartWidget`, `DoughnutChartWidget`, `LineChartWidget`                    | `Widgets\ChartWidget`; `getType()` returns `'bar'`, `'bubble'`, `'doughnut'`, `'line'` respectively    |
| `Widgets\PieChartWidget`, `PolarAreaChartWidget`, `RadarChartWidget`, `ScatterChartWidget`                 | `Widgets\ChartWidget`; `getType()` returns `'pie'`, `'polarArea'`, `'radar'`, `'scatter'` respectively |

### Updating icon aliases, render hooks, and Blade components

| Old                                                                                             | New                                                                                                                        |
| ----------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------- |
| `Tables\View\TablesIconAlias::FILTERS_QUERY_BUILDER_CONSTRAINTS_*`                              | `QueryBuilder\View\QueryBuilderIconAlias::CONSTRAINTS_*` for `BOOLEAN`, `DATE`, `NUMBER`, `RELATIONSHIP`, `SELECT`, `TEXT` |
| `View\PanelsIconAlias::PAGES_PASSWORD_RESET_REQUEST_PASSWORD_RESET_ACTIONS_LOGIN` (also `_RTL`) | `AUTH_PASSWORD_RESET_REQUEST_ACTIONS_LOGIN` (also `_RTL`) on the same class                                                |
| `Support\Enums\IconSize::ExtraExtraLarge`                                                       | `IconSize::TwoExtraLarge`; value remains `2xl`                                                                             |
| `forms:components.rich-editor.toolbar.*`                                                        | `forms::components.rich-editor.toolbar.*`; hyphenate tool names instead of underscores                                     |
| Built-in `schema::components.*` keys                                                            | `schemas::components.*`                                                                                                    |
| `tables::filters.query-builder.constraints.*`                                                   | `query-builder::constraints.*`                                                                                             |
| `Tables\View\TablesRenderHook::TOOLBAR_TOGGLE_COLUMN_TRIGGER_BEFORE` / `_AFTER`                 | `TOOLBAR_COLUMN_MANAGER_TRIGGER_BEFORE` / `_AFTER`; string values unchanged                                                |
| `panels::page.sub-navigation.module-menu.before` / `.after`                                     | `panels::page.sub-navigation.mobile-menu.before` / `.after`                                                                |
| `<x-filament::card>`                                                                            | `<x-filament::section>`                                                                                                    |
| `<x-filament::page>` / `<x-filament::widget>`                                                   | `<x-filament-panels::page>` / `<x-filament-widgets::widget>`                                                               |

The Blade scanner updates tags and literal dynamic-component names, including `filament::page` / `filament::widget`. Update computed names and direct old card-view references manually. To scan additional directories:

```bash
php vendor/filament/upgrade/src/upgrade-blade-templates.php resources/views app-modules
```

Add `--dry-run` to check without edits.

## Checking deprecated APIs during development

Keep `filament/upgrade` as a development dependency and add `vendor/filament/upgrade/src/rector.php` to `withSets()` in your Rector configuration. Include application source and tests, not `vendor` or third-party code.

Run `vendor/bin/rector process` to apply changes, or add `--dry-run` for CI. A dry run exits nonzero for required changes or errors. When related declarations span files, run with `--clear-cache` and without file filters/change limits.

Use Rector for ongoing checks, not the one-time `vendor/bin/filament-v6` script. Neither replaces `php artisan filament:upgrade` for publishing deployment assets.
