---
title: Upgrade guide
---

## Introduction

This guide covers upgrading from Filament 5 to 6. Run the upgrade script first, then review the behavior changes and manual steps that apply to your application. The removed-API tables are a reference; you do not need to work through each rename yourself.

## New requirements

- PHP 8.3+
- Laravel v12.0+

Upgrade Laravel before running the Filament upgrade script, and check that your plugins support Filament 6.

## Running the automated upgrade script

Commit your changes first: the script edits your files directly.

```bash
composer require filament/upgrade:"^6.0" -W --dev

vendor/bin/filament-v6

# Run the Composer commands printed by the script for your app, then:
composer update
```

<Aside variant="info">
    In Windows PowerShell, use `~6.0` instead of `^6.0` if the caret is ignored.
</Aside>

Include your application source directories and tests when prompted. The script handles most repetitive PHP and Blade changes. Review its changes, follow the applicable manual steps below, and test your application.

### Checking for deprecated APIs during development

Keep `filament/upgrade` as a development dependency to migrate deprecated PHP copied into an already-upgraded application. Add its set to your existing `rector.php`, or create one:

```php
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withSets([
        __DIR__ . '/vendor/filament/upgrade/src/rector.php',
    ]);
```

Include your own source directories and omit paths that do not exist. Do not include `vendor` or third-party source. Use the upgrade package for your target major and keep Rector up to date.

```bash
# Apply migrations locally, then review and test:
vendor/bin/rector process

# Check in CI without changing source files:
vendor/bin/rector process --dry-run
```

A dry run exits nonzero when changes are needed or an error occurs. Review and apply changes locally rather than automatically applying them in CI.

Run Rector routinely, not `vendor/bin/filament-v6`, which is the one-time interactive workflow. Neither replaces the deployment command `php artisan filament:upgrade` for publishing assets.

## Breaking changes

<div x-data="{ packages: ['panels', 'schemas', 'forms', 'infolists', 'tables', 'actions', 'notifications', 'widgets'] }">

Select the packages your app uses, including dependencies such as Schemas:

<Checkboxes>
    <Checkbox value="panels">Panels</Checkbox>
    <Checkbox value="schemas">Schemas</Checkbox>
    <Checkbox value="forms">Forms</Checkbox>
    <Checkbox value="infolists">Infolists</Checkbox>
    <Checkbox value="tables">Tables</Checkbox>
    <Checkbox value="actions">Actions</Checkbox>
    <Checkbox value="notifications">Notifications</Checkbox>
    <Checkbox value="widgets">Widgets</Checkbox>
</Checkboxes>

Place global configuration examples in a service provider's `boot()` method. Prefer configuring individual components when only some need the previous behavior.

### Medium-impact changes

<Disclosure open x-show="packages.includes('forms')">
<span slot="summary">`FileUpload` now prevents file path tampering by default</span>

`FileUpload` rejects submitted file paths unless they were already stored on the record. New uploads and cleared values are unaffected.

If you set paths through defaults, shared libraries, or copying from another record, authorize them using the `allowFilePathUsing:` argument of `preventFilePathTampering()`. To restore the previous default globally:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::configureUsing(static function (FileUpload $component): void {
    $component->preventFilePathTampering(false);
});
```

Disabling this protection requires you to authorize submitted paths yourself to prevent access to other users' or tenants' files. See [authorizing existing file paths](../forms/file-upload#authorizing-existing-file-paths).
</Disclosure>

<Disclosure open x-show="packages.includes('forms')">
<span slot="summary">`RichEditor` now prevents file attachment path tampering by default</span>

`RichEditor` rejects image attachment IDs that were not already stored in the record's original content. New uploads and attachments using the Spatie Media Library provider are unaffected.

If you insert existing images from a shared library or another record, authorize those IDs using the `allowFilePathUsing:` argument of `preventFileAttachmentPathTampering()`. To restore the previous default globally:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::configureUsing(static function (RichEditor $component): void {
    $component->preventFileAttachmentPathTampering(false);
});
```

Disabling this protection requires you to authorize submitted attachment IDs yourself. See [securing file attachment IDs](../forms/rich-editor#securing-file-attachment-ids).
</Disclosure>

<Disclosure open x-show="packages.includes('actions')">
<span slot="summary">Exports and import failure CSVs now prevent formula injection by default</span>

Exported strings and import failure CSV cells beginning with `=`, `+`, `-`, `@`, a tab, or a carriage return are prefixed with a single quote. Numeric strings such as `-5` are unchanged. Values such as `+44 1234 567890` gain a quote, which may affect systems consuming these files.

For trusted data only, restore the previous defaults globally:

```php
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Imports\Importer;

ExportColumn::configureUsing(static function (ExportColumn $column): void {
    $column->preventFormulaInjection(false);
});

Importer::preventFormulaInjection(false);
```

For individual components, use `preventFormulaInjection(false)` on an export column or redeclare `protected static bool $shouldPreventFormulaInjection = false` on an importer. Disabling either protection allows spreadsheet formulas in the affected output.

See [export formula injection](../actions/export#csv-formula-injection) and [import failure CSV formula injection](../actions/import#csv-formula-injection).
</Disclosure>

### Low-impact changes

<Disclosure open x-show="packages.includes('tables')">
<span slot="summary">Tables now limit bulk selections to 10,000 records by default</span>

The previously unlimited bulk selection defaults to `maxSelectableRecords(10000)`. Raise the limit on affected tables, or restore unlimited selection globally:

```php
use Filament\Tables\Table;

Table::configureUsing(static function (Table $table): void {
    $table->maxSelectableRecords(null);
});
```

Database-backed bulk actions process only a subset of oversized selections, including inverse selections; they do not reject the whole action. Custom-data tables throw a `LogicException`. The all-pages "select all" link is hidden when the selectable count exceeds the limit.

This is not a memory or execution-time guarantee. See [selection limits](../tables/actions#limiting-the-number-of-selectable-records) and [bulk-action performance](../tables/actions#improving-the-performance-of-bulk-actions) before increasing it.
</Disclosure>

<Disclosure open x-show="packages.includes('forms') || packages.includes('infolists')">
<span slot="summary">Repeaters, builders, and repeatable entries now limit item processing by default</span>

`Repeater` and `Builder` now process at most 500 items per component; `RepeatableEntry` processes at most 1,000. Exceeding the limit aborts with HTTP 413 without truncating data, including when opening existing records.

Check records with large item counts before upgrading. Raise `maxItemsForProcessing()` on affected components, or restore unlimited processing globally:

```php
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\RepeatableEntry;

Repeater::configureUsing(static function (Repeater $component): void {
    $component->maxItemsForProcessing(null);
});

Builder::configureUsing(static function (Builder $component): void {
    $component->maxItemsForProcessing(null);
});

RepeatableEntry::configureUsing(static function (RepeatableEntry $component): void {
    $component->maxItemsForProcessing(null);
});
```

This is separate from `maxItems()` validation and is not a request-size or execution-time guarantee. Query Builder has separate rule-tree limits. See [limiting item processing](../forms/repeater#limiting-item-processing).
</Disclosure>

<Disclosure open x-show="packages.includes('tables')">
<span slot="summary">Tables now enforce pagination options by default</span>

Submitted page sizes must match the configured pagination options. Include sizes you set directly in PHP, or opt out with `enforcePaginationPageOptions(false)`. To restore the previous default globally:

```php
use Filament\Tables\Table;

Table::configureUsing(static function (Table $table): void {
    $table->enforcePaginationPageOptions(false);
});
```

Invalid sizes fall back to a valid saved selection or the default option; stale saved selections fall back to the first option. Explicit `'all'` options and `paginated(false)` remain unrestricted. See [enforcing pagination options](../tables/overview#enforcing-the-pagination-options).
</Disclosure>

<Disclosure open x-show="packages.includes('tables')">
<span slot="summary">`QueryBuilder` filters now limit rule-tree complexity by default</span>

Rule trees now allow at most 100 conditions and a nesting depth of 5. If existing or submitted state exceeds either limit, **the filter is ignored and no constraints are applied**.

Raise `maxRules()` and `maxNestingDepth()` on affected filters, or restore unlimited rule trees globally at the risk of excessive resource consumption:

```php
use Filament\Tables\Filters\QueryBuilder;

QueryBuilder::configureUsing(static function (QueryBuilder $queryBuilder): void {
    $queryBuilder
        ->maxRules(null)
        ->maxNestingDepth(null);
});
```

See [limiting the size of the rule tree](../tables/filters/query-builder#limiting-the-size-of-the-rule-tree).
</Disclosure>

<Disclosure open x-show="packages.includes('panels')">
<span slot="summary">App authentication now accepts a smaller TOTP window</span>

App authentication accepts codes from the current 30-second period and one period on either side, rather than eight. Synchronize server and user-device clocks if valid codes are rejected. To temporarily restore the previous tolerance, configure your panel's app authentication provider:

```php
use Filament\Auth\MultiFactor\App\AppAuthentication;

AppAuthentication::make()
    ->codeWindow(8)
```

See [changing the app code expiration time](../users/multi-factor-authentication#changing-the-app-code-expiration-time).
</Disclosure>

<Disclosure open x-show="packages.includes('schemas')">
<span slot="summary">Livewire file uploads are now restricted to schema components by default</span>

Components using `InteractsWithSchemas` reject uploads with HTTP 403 unless the target property belongs to a schema component supporting uploads. Filament fields are unaffected. To upload directly to another property, opt out on that Livewire component and secure the upload yourself:

```php
public function shouldRestrictFileUploadsToSchemaComponents(): bool
{
    return false;
}
```

Remove uses of the obsolete `RestrictsFileUploadsToSchemaComponents` trait; the restriction is now built into `InteractsWithSchemas`. See [restricting Livewire file uploads](../advanced/security#restricting-livewire-file-uploads-to-schema-components).
</Disclosure>

<Disclosure open x-show="packages.includes('actions')">
<span slot="summary">OpenSpout has been upgraded to v5</span>

If you customize XLSX exports, update your OpenSpout code for v5's immutable objects. Use constructor arguments or `with*()` methods instead of setters, and apply styles through `Cell::fromValue()` or `Row::fromValuesWithStyles()` rather than a default row style.

See [customizing XLSX files](../actions/export#customizing-xlsx-files) and the [OpenSpout upgrade guide](https://github.com/openspout/openspout/blob/5.x/UPGRADE.md#upgrading-from-4x-to-50).
</Disclosure>

<Disclosure open x-show="packages.includes('panels')">
<span slot="summary">Custom avatar providers must accept non-Eloquent users</span>

`AvatarProvider::get()` now accepts `Model | Authenticatable`. Update your provider's signature if the script has not already done so:

```php
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

public function get(Model | Authenticatable $record): string
{
    // Return the avatar URL for the record.
}
```

Review Eloquent-specific calls if your app uses non-Eloquent users. See [custom avatar providers](../users/overview#using-a-different-avatar-provider).
</Disclosure>

<Disclosure open x-show="packages.includes('actions')">
<span slot="summary">Import and export job tags are now namespaced</span>

Tags change from `import{id}` and `export{id}` to `filament:import:{id}` and `filament:export:{id}`. Update monitoring filters. Existing tags and custom `getJobTags()` overrides are unchanged.
</Disclosure>

<Disclosure open>
<span slot="summary">RTL controls require their own icon registrations</span>

RTL pagination and sidebar controls, including those in the topbar, no longer fall back to LTR icon registrations. If you customize these icons, register suitable RTL artwork explicitly:

- `Filament\Support\View\SupportIconAlias::PAGINATION_PREVIOUS_BUTTON_RTL` and `PAGINATION_NEXT_BUTTON_RTL`.
- `Filament\View\PanelsIconAlias::SIDEBAR_EXPAND_BUTTON_RTL` and `SIDEBAR_COLLAPSE_BUTTON_RTL`.

Without custom registrations, Filament uses its built-in RTL icons.
</Disclosure>

<Disclosure open>
<span slot="summary">The badge delete button icon alias has been removed</span>

Remove registrations for `Filament\Support\View\SupportIconAlias::BADGE_DELETE_BUTTON`. Customize `.fi-badge-delete-btn-icon` instead; there is no replacement icon alias or automated migration.
</Disclosure>

<Disclosure open x-show="packages.includes('tables')">
<span slot="summary">Column manager widths can now be enums</span>

`getColumnToggleFormWidth()` is replaced by `getColumnManagerWidth()`, which returns `Filament\Support\Enums\Width | string | null` instead of `?string`. Update string-only consumers and custom return types. Convert an enum using `$width instanceof Width ? $width->value : $width` after importing `Filament\Support\Enums\Width`.
</Disclosure>

<Disclosure open>
<span slot="summary">Sticky modals have a new scroll wrapper</span>

Modals using `stickyHeader()` or `stickyFooter()` wrap their header, content, and footer in `.fi-modal-window-scroll`, except for slide-overs and screen-width modals. Update CSS targeting those elements as direct children of `.fi-modal-window`.
</Disclosure>

<Disclosure open>
<span slot="summary">The Blade capture directive dependency has been removed</span>

Filament provides `@capture` and `@endcapture` itself; existing views need no changes. Remove explicit `RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider` registrations, including in plugin tests. If you need the directives without Filament, require `ryangjchandler/blade-capture-directive` directly.
</Disclosure>

### Updating custom code

The script focuses on documented configuration and extension points. If you use custom components or plugins, update any remaining references to the removed APIs listed below, including overrides and `parent::` calls. JavaScript and TypeScript changes need to be made manually. Run your tests and static analysis after upgrading.

For widget and relation manager lazy-loading configuration supplied by traits, manually rename `$isLazy` to `$isLazyLoaded` and `$isBadgeDeferred` to `$isBadgeLazyLoaded`. Declare both as `protected static ?bool`, keeping their configured values. The script updates class declarations but leaves shared traits unchanged.

<Disclosure x-show="packages.includes('notifications') || packages.includes('forms')">
<span slot="summary">Updating JavaScript and TypeScript aliases manually</span>

Update these references in your JavaScript and TypeScript:

| Owner                                        | Removed API                                 | Replacement                                    |
| -------------------------------------------- | ------------------------------------------- | ---------------------------------------------- |
| JavaScript `FilamentNotificationAction`      | `emit()`, `emitSelf()`, `emitTo()`          | `dispatch()`, `dispatchSelf()`, `dispatchTo()` |
| Type imports from `@filament/forms/js-field` | `JsFieldInitialProps<Value, Configuration>` | `JsFieldProps<Value, Configuration>`           |

Rebuild your bundles afterwards.
</Disclosure>

<Disclosure x-show="packages.includes('panels')">
<span slot="summary">Replacing obsolete asset registration calls manually</span>

Replace `registerScripts()` / `registerStyles()` with `Filament\Support\Facades\FilamentAsset::register()`, using `Js::make()` / `Css::make()` from `Filament\Support\Assets`. See [asset registration](../support/assets).
</Disclosure>

### Referencing removed APIs

The upgrade script handles the common uses of the APIs below. Use these tables to update any references that remain. PHP names are relative to the `Filament` namespace unless stated otherwise.

<Disclosure>
<span slot="summary">Removed methods and functions</span>

| Owner                                                                | Removed method                                                                                 | Replacement                                                                             |
| -------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------- |
| `Actions\Action`                                                     | `emit()`, `emitSelf()`, `emitTo()`                                                             | `dispatch()`, `dispatchSelf()`, `dispatchTo()`                                          |
| `Actions\Action`                                                     | `disableForm()`, `disabledForm()`                                                              | `disabledSchema()`                                                                      |
| `Actions\Action`                                                     | `form()`, `infolist()`                                                                         | `schema()`                                                                              |
| `Actions\Action`                                                     | `getForm()`, `isFormDisabled()`                                                                | `getSchema()`, `isSchemaDisabled()`                                                     |
| `Actions\Action`                                                     | `mutateFormDataUsing()`, `formData()`                                                          | `mutateDataUsing()`, `data()`                                                           |
| `Actions\Action`                                                     | `resetFormData()`, `getFormData()`, `getRawFormData()`                                         | `resetData()`, `getData()`, `getRawData()`                                              |
| `Actions\Action`                                                     | `fillForm()`                                                                                   | `fillSchema()`                                                                          |
| `Actions\Action`                                                     | `beforeFormFilled()`, `afterFormFilled()`                                                      | `beforeSchemaFilled()`, `afterSchemaFilled()`                                           |
| `Actions\Action`                                                     | `beforeFormValidated()`, `afterFormValidated()`                                                | `beforeSchemaValidated()`, `afterSchemaValidated()`                                     |
| `Actions\Action`                                                     | `callBeforeFormFilled()`, `callAfterFormFilled()`                                              | `callBeforeSchemaFilled()`, `callAfterSchemaFilled()`                                   |
| `Actions\Action`                                                     | `callBeforeFormValidated()`, `callAfterFormValidated()`                                        | `callBeforeSchemaValidated()`, `callAfterSchemaValidated()`                             |
| `Actions\Action`                                                     | `modalActions()`, `extraModalActions()`                                                        | `modalFooterActions()`, `extraModalFooterActions()`                                     |
| `Actions\Action`                                                     | `modalButton()`, `modalFooter()`, `modalSubheading()`                                          | `modalSubmitActionLabel()`, `modalContentFooter()`, `modalDescription()`                |
| `Actions\Action`                                                     | `makeExtraModalAction()`                                                                       | `makeModalSubmitAction()`                                                               |
| `Actions\Action`                                                     | `failureNotificationMessage()`, `successNotificationMessage()`                                 | `failureNotificationTitle()`, `successNotificationTitle()`                              |
| `Actions\Action`                                                     | `withAttributes()`, `hold()`                                                                   | `extraAttributes()`, `halt()`                                                           |
| `Actions\Action`                                                     | `hasCustomLivewireClickHandler()`, `getJavaScriptClickHandler()`                               | `hasLivewireClickHandlerEnabledConfiguration()`, `getJsClickHandler()`                  |
| Actions, action groups, schema components using labels               | `disableLabel()`                                                                               | `hiddenLabel()` (do not negate the condition)                                           |
| Actions, action groups, schema tabs                                  | `indicatorColor()`                                                                             | `badgeColor()`                                                                          |
| `Actions\ReplicateAction`                                            | `afterReplicaSaved()`                                                                          | `after()`                                                                               |
| `Actions\CreateAction`                                               | `preserveFormDataWhenCreatingAnother()`                                                        | `preserveDataWhenCreatingAnother()`                                                     |
| `Actions\ExportAction`, `ExportBulkAction`                           | `getFileDisk()`, `isEnablingVisibleTableColumnsByDefault()`                                    | `getFileDiskName()`, `shouldEnableVisibleTableColumnsByDefault()`                       |
| `Actions\Imports\ImportColumn`                                       | `guess()`, `getExample()`                                                                      | `guesses()`, `Illuminate\Support\Arr::first($column->getExamples())`                    |
| `Actions\Imports\Events\ImportChunkProcessed`                        | `getProcessedRows()`, `getSuccessfulRows()`                                                    | `getProcessedRowsCount()`, `getSuccessfulRowsCount()`                                   |
| Importers, exporters                                                 | `getOptionsFormComponents()`                                                                   | `getOptionsSchemaComponents()`                                                          |
| Query builder operators                                              | `getFormSchema()`                                                                              | `getSchemaComponents()`                                                                 |
| `Actions\Concerns\InteractsWithActions`                              | `mountedActionHasForm()`, `getMountedActionForm()`                                             | `mountedActionHasSchema()`, `getMountedActionSchema()`                                  |
| Form/infolist Livewire components                                    | `cacheForm()`, `cacheInfolist()`                                                               | `cacheSchema()`                                                                         |
| Form/infolist Livewire components                                    | `getForm()`, `getInfolist()`                                                                   | `getSchema()`                                                                           |
| Form/infolist Livewire components                                    | `hasCachedForm()`, `hasCachedInfolist()`                                                       | `hasCachedSchema()`                                                                     |
| Form/infolist Livewire components                                    | `getCachedForms()`, `getCachedInfolists()`                                                     | `getCachedSchemas()`                                                                    |
| Form Livewire components                                             | `isCachingForms()`, `getActiveFormsLocale()`, `getOldFormState()`                              | `isCachingSchemas()`, `getActiveSchemaLocale()`, `getOldSchemaState()`                  |
| `Forms\Components\BaseFileUpload`                                    | `enableDownload()`, `enableOpen()`, `enableReordering()`                                       | `downloadable()`, `openable()`, `reorderable()`                                         |
| `Forms\Components\BaseFileUpload`                                    | `moveFile()`, `storeFile()`                                                                    | `moveFiles()`, `storeFiles()`                                                           |
| Form `Builder`, `Repeater`                                           | `createItemButtonLabel()`, `getRawItemState()`                                                 | `addActionLabel()`, `getItemStateSnapshot()`                                            |
| Form `Builder`                                                       | `createItemBetweenButtonLabel()`                                                               | `addBetweenActionLabel()`                                                               |
| Form `Builder`                                                       | `showBlockLabels()`, `withBlockLabels()`, `withBlockNumbers()`                                 | `blockLabels()`, `blockLabels()`, `blockNumbers()`                                      |
| Form `Repeater`                                                      | `orderable()`                                                                                  | `orderColumn()`                                                                         |
| Form `KeyValue`                                                      | `addButtonLabel()`, `deleteButtonLabel()`, `reorderButtonLabel()`                              | `addActionLabel()`, `deleteActionLabel()`, `reorderActionLabel()`                       |
| Form `FileUpload`                                                    | `idleLabel()`, `imageResizeMode()`                                                             | `placeholder()`, `automaticallyResizeImagesMode()`                                      |
| Form `FileUpload`                                                    | `imageResizeTargetHeight()`, `imageResizeTargetWidth()`                                        | `automaticallyResizeImagesToHeight()`, `automaticallyResizeImagesToWidth()`             |
| Form `FileUpload`                                                    | `imageResizeUpscale()`, `orientImageFromExif()`                                                | `automaticallyUpscaleImagesWhenResizing()`, `orientImagesFromExif()`                    |
| Form `FileUpload`                                                    | `getImageCropAspectRatio()`, `getImageResizeMode()`                                            | `getAutomaticallyCropImagesAspectRatio()`, `getAutomaticallyResizeImagesMode()`         |
| Form `FileUpload`                                                    | `getImageResizeTargetHeight()`, `getImageResizeTargetWidth()`                                  | `getAutomaticallyResizeImagesHeight()`, `getAutomaticallyResizeImagesWidth()`           |
| Form `FileUpload`                                                    | `getImageResizeUpscale()`, `getParentTargetSizes()`                                            | `shouldAutomaticallyUpscaleImagesWhenResizing()`, `getImageEditorViewportScaleFactor()` |
| Form `FileUpload`                                                    | `imageEditorAspectRatios()`, `getImageEditorAspectRatiosForJs()`                               | `imageEditorAspectRatioOptions()`, `getImageEditorAspectRatioOptionsForJs()`            |
| Form `MarkdownEditor`, `RichEditor`                                  | `getUploadedAttachmentUrlUsing()`, `saveUploadedFileAttachmentsUsing()`                        | `getFileAttachmentUrlUsing()`, `saveUploadedFileAttachmentUsing()`                      |
| Form `RichEditor\MentionProvider`                                    | `hasSearchResultsUsing()`                                                                      | `hasSearchResultsUsingCallback()`                                                       |
| Form `RichEditor\RichContentAttribute`                               | `getCustomBlocksConfig()`                                                                      | `getCustomBlocksConfiguration()`                                                        |
| Form `SpatieMediaLibraryFileUpload`                                  | `getConversionsDisk()`                                                                         | `getConversionsDiskName()`                                                              |
| Form `MorphToSelect\Type`                                            | `titleColumnName()`                                                                            | `titleAttribute()`                                                                      |
| Form `Select`                                                        | `createOptionForm()`, `editOptionForm()`, `manageOptionForm()`                                 | `createOptionSchema()`, `editOptionSchema()`, `manageOptionSchema()`                    |
| Form `Select`                                                        | `getCreateOptionActionForm()`, `getEditOptionActionForm()`                                     | `getCreateOptionActionSchema()`, `getEditOptionActionSchema()`                          |
| Form `Select`                                                        | `hasCreateOptionActionFormSchema()`, `hasEditOptionActionFormSchema()`                         | `hasCreateOptionActionSchema()`, `hasEditOptionActionSchema()`                          |
| Form `Field`, schema `FusedGroup`                                    | `getExtraFieldWrapperAttributesBag()`                                                          | `getExtraFieldWrapperAttributeBag()`                                                    |
| Infolist `Entry`                                                     | `getExtraEntryWrapperAttributesBag()`                                                          | `getExtraEntryWrapperAttributeBag()`                                                    |
| Infolist `RepeatableEntry`                                           | `getItemState()`                                                                               | `getItemStateWithExistence()`                                                           |
| Infolist `KeyValueEntry`                                             | `emptyMessage()`                                                                               | `placeholder()`                                                                         |
| Infolist `ImageEntry`, table `ImageColumn`                           | `height()`, `size()`, `getHeight()`                                                            | `imageHeight()`, `imageSize()`, `getImageHeight()`                                      |
| Infolist `ImageEntry`                                                | `width()`, `getWidth()`                                                                        | `imageWidth()`, `getImageWidth()`                                                       |
| Table `ImageColumn`                                                  | `rounded()`, `isRounded()`                                                                     | `circular()`, `isCircular()`                                                            |
| Schema and schema components                                         | `reactive()`                                                                                   | `live()`                                                                                |
| Form components using `HasAffixes`                                   | `postfix()`                                                                                    | `suffix()`                                                                              |
| `Schemas\Schema`, `Tables\Table`                                     | `deferLoading()`, `isLoadingDeferred()`                                                        | `lazyLoaded()`, `isLazyLoaded()`                                                        |
| Schema `Livewire`, form `LivewireField`                              | `lazy()`, `isLazy()`                                                                           | `lazyLoaded()`, `isLazyLoaded()`                                                        |
| Other schema components and `Schema` (blur binding)                  | `lazy()`, `isLazy()`                                                                           | `live(onBlur: true)`, `isLiveOnBlur()`                                                  |
| Schema `Tab`, resource `RelationGroup`                               | `deferBadge()`, `isBadgeDeferred()`                                                            | `lazyLoadedBadge()`, `isBadgeLazyLoaded()`                                              |
| Widgets, relation managers                                           | `isLazy()`                                                                                     | `isLazyLoaded()`                                                                        |
| Relation managers                                                    | `isBadgeDeferred()`                                                                            | `isBadgeLazyLoaded()`                                                                   |
| Schema `Tabs`                                                        | `getDeferredTabBadges()`, `hasDeferredBadges()`                                                | `getLazyLoadedTabBadges()`, `hasLazyLoadedBadges()`                                     |
| `Schemas\Schema`                                                     | `name()`, `getName()`, `context()`                                                             | `key()`, `getKey()`, `operation()`                                                      |
| Schema and schema components                                         | `clearCachedDefaultChildSchemas()`                                                             | `clearCachedChildSchemas()`                                                             |
| Schema components                                                    | `getGetCallback()`, `getSetCallback()`                                                         | `makeGetUtility()`, `makeSetUtility()`                                                  |
| Schema components                                                    | `getChildComponentContainer()`, `getChildComponentContainers()`                                | `getChildSchema()`, `getChildSchemas()`                                                 |
| `Schemas\Components\Component`                                       | `isConcealed()`                                                                                | `false` for the built-in implementation                                                 |
| Schema `Wizard\Step`                                                 | `afterValidated()`                                                                             | `afterValidation()`                                                                     |
| Schema `Section`                                                     | `formBefore()`, `isFormBefore()`                                                               | `contentBefore()`, `isContentBefore()`                                                  |
| Schema `Utilities\Get`, `Utilities\Set`                              | `skipComponentsChildContainersWhileSearching()`                                                | `skipComponentsChildSchemasWhileSearching()`                                            |
| `Tables\Table`                                                       | `actions()`, `pushActions()`                                                                   | `recordActions()`, `pushRecordActions()`                                                |
| `Tables\Table`                                                       | `actionsColumnLabel()`, `actionsAlignment()`, `actionsPosition()`                              | `recordActionsColumnLabel()`, `recordActionsAlignment()`, `recordActionsPosition()`     |
| `Tables\Table`                                                       | `getActions()`, `getActionsPosition()`, `getActionsAlignment()`, `getActionsColumnLabel()`     | Corresponding `getRecordActions*()` methods                                             |
| `Tables\Table`                                                       | `bulkActions()`, `pushBulkActions()`, `getBulkActions()`                                       | `toolbarActions()`, `pushToolbarActions()`, `getToolbarActions()`                       |
| `Tables\Table`                                                       | `toggleColumnsTriggerAction()`, `getToggleColumnsTriggerAction()`                              | `columnManagerTriggerAction()`, `getColumnManagerTriggerAction()`                       |
| `Tables\Table`                                                       | `columnToggleFormColumns()`, `columnToggleFormMaxHeight()`, `columnToggleFormWidth()`          | `columnManagerColumns()`, `columnManagerMaxHeight()`, `columnManagerWidth()`            |
| `Tables\Table`                                                       | `getColumnToggleFormColumns()`, `getColumnToggleFormMaxHeight()`, `getColumnToggleFormWidth()` | `getColumnManagerColumns()`, `getColumnManagerMaxHeight()`, `getColumnManagerWidth()`   |
| `Tables\Table`                                                       | `groupsInDropdownOnDesktop()`                                                                  | `groupingSettingsInDropdownOnDesktop()`                                                 |
| Table Livewire components                                            | `updatedToggledTableColumns()`, `getToggledTableColumnsSessionKey()`                           | `applyTableColumnManager()` with no arguments, `getTableColumnsSessionKey()`            |
| Table Livewire components                                            | `updatedTableGroupColumn()`, `updatedTableSortDirection()`                                     | `updatedTableGrouping()`, `updatedTableSort()`                                          |
| Table `IconColumn`                                                   | `options()`                                                                                    | `icons()`                                                                               |
| Table `Column`                                                       | `disableClick()`                                                                               | `disabledClick()` (do not negate the condition)                                         |
| Table `Column`, `Actions\Exports\ExportColumn`                       | `queriesRelationships()`, `getGetStateUsingCallback()`                                         | `hasRelationship()`, `getStateUsingCallback()`                                          |
| Table `Summarizers\Summarizer`                                       | `hasQueryModification()`                                                                       | `hasQueryModificationCallback()`                                                        |
| `Tables\Table`                                                       | `getInverseRelationship()`                                                                     | `getInverseRelationshipName()`                                                          |
| Table `Filters\BaseFilter`                                           | `form()`, `getFormSchema()`                                                                    | `schema()`, `getSchemaComponents()`                                                     |
| Table `Filters\SelectFilter`                                         | `column()`, `getColumn()`                                                                      | `attribute()`, `getAttribute()`                                                         |
| Table `Filters\SelectFilter`                                         | `queriesRelationships()`, `getRelationshipKey()`                                               | `hasRelationship()`, `getQualifiedRelatedKeyNameForRelationship()`                      |
| Table `Grouping\Group`                                               | `getDescriptionUsing()`                                                                        | `getDescriptionFromRecordUsing()`                                                       |
| `Navigation\NavigationItem`                                          | `isActiveWhen()`, `isChildItemsActive()`                                                       | `activeWhen()`, `hasActiveChildItems()`                                                 |
| `Clusters\Cluster`                                                   | `unshiftClusterBreadcrumbs()`                                                                  | `prependClusterBreadcrumbs()`                                                           |
| `Panel`                                                              | `passwordResetRequestRouteSlug()`, `passwordResetRouteSlug()`                                  | `requestPasswordResetRouteSlug()`, `resetPasswordRouteSlug()`                           |
| `Panel`                                                              | `databaseNotificationsPolling()`                                                               | `databaseNotificationsPollingInterval()`                                                |
| `Panel`, `FilamentManager`, `Facades\Filament`                       | `hasDarkModeForced()`                                                                          | `isDarkModeForced()`                                                                    |
| `FilamentManager`, `Facades\Filament`                                | `currentDomain()`                                                                              | `setCurrentDomain()`                                                                    |
| `Pages\BasePage`                                                     | `alignFormActionsLeft()`, `alignFormActionsRight()`                                            | `alignFormActionsStart()`, `alignFormActionsEnd()`                                      |
| Dashboard `HasFilters` concern                                       | `normalizeTableFilterValuesFromQueryString()`                                                  | `normalizeFilterValuesFromQueryString()`                                                |
| `Forms\Commands\FileGenerators\RichContentCustomBlockClassGenerator` | `configureToPreviewMethod()`                                                                   | `configureToPreviewHtmlMethod()`                                                        |
| `Notifications\Livewire\Notifications`                               | `pullNotificationsFromSession()`                                                               | `pullNotifications()`                                                                   |
| Chart widgets using `HasFiltersSchema`                               | `resetFiltersForm()`                                                                           | `resetFilters()`                                                                        |
| `Support` functions                                                  | `format_number()`, `format_money()`                                                            | `Illuminate\Support\Number::format()`, `Number::currency()` with adapters where needed  |
| Livewire test assertions                                             | `assertFormFieldIsDisabled()`, `assertFormFieldIsEnabled()`, `assertFormFieldIsReadOnly()`     | `assertFormFieldDisabled()`, `assertFormFieldEnabled()`, `assertFormFieldReadOnly()`    |
| Livewire test assertions                                             | `assertFormFieldIsHidden()`, `assertFormFieldIsVisible()`                                      | `assertFormFieldHidden()`, `assertFormFieldVisible()`                                   |
| Livewire test assertions                                             | `assertActionHeld()`, `assertTableActionHeld()`, `assertTableBulkActionHeld()`                 | `assertActionHalted()`, `assertTableActionHalted()`, `assertTableBulkActionHalted()`    |
| Livewire test assertions                                             | `assertTableActionsExistInOrder()`                                                             | `assertTableRecordActionsExistInOrder()`                                                |
| Livewire test assertions                                             | `setActionData()`, `assertActionDataSet()`                                                     | `fillForm()`, `assertSchemaStateSet()` (`data:` → `state:`)                             |
| Livewire test assertions                                             | `assertHasActionErrors()`, `assertHasNoActionErrors()`                                         | `assertHasFormErrors()`, `assertHasNoFormErrors()`                                      |

Resource/page `form()` / `infolist()` hooks and `InteractsWithForms` remain supported; you do not need to replace your form components with `InteractsWithSchemas`.
</Disclosure>

<Disclosure>
<span slot="summary">Removed classes, traits, and enum names</span>

| Removed name                                                                                               | Replacement                                                                         |
| ---------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------- |
| `Schemas\View\SchemaIconAlias`                                                                             | `Schemas\View\SchemasIconAlias`                                                     |
| `Widgets\View\Components\StatsOverviewWidgetComponent\StatComponent\StatsOverviewWidgetStatChartComponent` | `Widgets\View\Components\StatsOverviewWidgetComponent\StatComponent\ChartComponent` |
| `Forms\Components\Concerns\CanLimitItemsLength`                                                            | `Forms\Components\Concerns\CanLimitItemsCount`                                      |
| `Tables\Columns\Concerns\HasRowLoopObject`                                                                 | `Tables\Columns\Concerns\HasRowLoop`                                                |
| `Tables\Table\Concerns\CanDeferLoading`                                                                    | `Tables\Table\Concerns\CanBeLazyLoaded`                                             |
| `Tables\Concerns\CanDeferLoading`                                                                          | `Tables\Concerns\CanBeLazyLoaded`                                                   |
| `Tables\Columns\Concerns\CanBeCopied`                                                                      | `Support\Concerns\CanBeCopied`                                                      |
| `Tables\Actions\HeaderActionsPosition`                                                                     | `Tables\Enums\HeaderActionsPosition`                                                |
| `Tables\Filters\QueryBuilder\Constraints\*`, including operators and traits                                | `QueryBuilder\Constraints\*`                                                        |
| `Actions\Concerns\HasInfolist`                                                                             | `Actions\Concerns\HasSchema`, or remove a redundant trait use                       |
| `Schemas\Concerns\RestrictsFileUploadsToSchemaComponents`                                                  | Remove redundant uses; see the upload restriction change above                      |
| `Support\Components\Attributes\ExposedLivewireMethod`                                                      | `Support\Components\Attributes\Exposed` for method attributes                       |
| `Widgets\BarChartWidget`                                                                                   | `Widgets\ChartWidget` with `getType()` returning `'bar'`                            |
| `Widgets\BubbleChartWidget`                                                                                | `Widgets\ChartWidget` with `getType()` returning `'bubble'`                         |
| `Widgets\DoughnutChartWidget`                                                                              | `Widgets\ChartWidget` with `getType()` returning `'doughnut'`                       |
| `Widgets\LineChartWidget`                                                                                  | `Widgets\ChartWidget` with `getType()` returning `'line'`                           |
| `Widgets\PieChartWidget`                                                                                   | `Widgets\ChartWidget` with `getType()` returning `'pie'`                            |
| `Widgets\PolarAreaChartWidget`                                                                             | `Widgets\ChartWidget` with `getType()` returning `'polarArea'`                      |
| `Widgets\RadarChartWidget`                                                                                 | `Widgets\ChartWidget` with `getType()` returning `'radar'`                          |
| `Widgets\ScatterChartWidget`                                                                               | `Widgets\ChartWidget` with `getType()` returning `'scatter'`                        |

The query builder filter itself remains `Tables\Filters\QueryBuilder`. Update any remaining type hints for removed chart classes to `Widgets\ChartWidget`.
</Disclosure>

<Disclosure>
<span slot="summary">Removed icon aliases and icon-size constants</span>

| Removed name or string                                                            | Replacement                                                                                                                |
| --------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------- |
| `Tables\View\TablesIconAlias::FILTERS_QUERY_BUILDER_CONSTRAINTS_*`                | `QueryBuilder\View\QueryBuilderIconAlias::CONSTRAINTS_*` for `BOOLEAN`, `DATE`, `NUMBER`, `RELATIONSHIP`, `SELECT`, `TEXT` |
| `View\PanelsIconAlias::PAGES_PASSWORD_RESET_REQUEST_PASSWORD_RESET_ACTIONS_LOGIN` | `AUTH_PASSWORD_RESET_REQUEST_ACTIONS_LOGIN` on the same class                                                              |
| The preceding password-reset constant with `_RTL`                                 | `AUTH_PASSWORD_RESET_REQUEST_ACTIONS_LOGIN_RTL`                                                                            |
| `Support\Enums\IconSize::ExtraExtraLarge`                                         | `IconSize::TwoExtraLarge` (value remains `2xl`)                                                                            |
| `forms:components.rich-editor.toolbar.*`                                          | `forms::components.rich-editor.toolbar.*`, with hyphens instead of underscores in tool names                               |
| `schema::components.*` built-in keys                                              | `schemas::components.*`                                                                                                    |
| `tables::filters.query-builder.constraints.*`                                     | `query-builder::constraints.*`                                                                                             |

The script updates PHP constants and string literals. Apply the badge delete icon and RTL registration changes described above manually.
</Disclosure>

<Disclosure>
<span slot="summary">Removed render hook aliases</span>

| Removed name or string                                               | Replacement                                                 |
| -------------------------------------------------------------------- | ----------------------------------------------------------- |
| `Tables\View\TablesRenderHook::TOOLBAR_TOGGLE_COLUMN_TRIGGER_BEFORE` | `TOOLBAR_COLUMN_MANAGER_TRIGGER_BEFORE` on the same class   |
| `Tables\View\TablesRenderHook::TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER`  | `TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER` on the same class    |
| `panels::page.sub-navigation.module-menu.before` / `.after`          | `panels::page.sub-navigation.mobile-menu.before` / `.after` |

The table-hook string values are unchanged.
</Disclosure>

<Disclosure>
<span slot="summary">Removed Blade components</span>

| Removed component    | Replacement             |
| -------------------- | ----------------------- |
| `<x-filament::card>` | `<x-filament::section>` |

The script updates component tags and literal dynamic-component names. Update computed names and direct references to the old card view manually. To scan additional view directories:

```bash
php vendor/filament/upgrade/src/upgrade-blade-templates.php resources/views app-modules
```

Add `--dry-run` to check without changing files. The scanner also updates `filament::page` / `filament::widget` tags to `filament-panels::page` / `filament-widgets::widget`.
</Disclosure>

</div>
