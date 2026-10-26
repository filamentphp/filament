---
title: Upgrade guide
---

## Introduction

This guide covers upgrading from Filament 5 to 6.

## New requirements

- PHP 8.3+
- Laravel v12.0+

Upgrade Laravel before running the Filament upgrade script.

## Running the automated upgrade script

Check that your plugins support Filament 6 before upgrading. Commit your changes first: the script edits your files directly.

```bash
composer require filament/upgrade:"^6.0" -W --dev

vendor/bin/filament-v6

# Run the Composer commands printed by the script for your app, then:
composer update
```

<Aside variant="info">
    In Windows PowerShell, use `~6.0` instead of `^6.0` if the caret is ignored.
</Aside>

Review the script's changes and the breaking changes below, then test your app. You can remove the upgrade package afterwards with `composer remove filament/upgrade --dev`.

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

Place global configuration examples in a service provider's `boot()` method. Prefer overriding individual components when only some need the previous behavior.

### Medium-impact changes

<Disclosure open x-show="packages.includes('forms')">
<span slot="summary">`FileUpload` now prevents file path tampering by default</span>

`FileUpload` now rejects submitted file paths unless they were already stored on the record. New uploads and cleared values are unaffected.

If you set paths through defaults, shared libraries, or copying from another record, allow the permitted paths using `allowFilePathUsing`. To restore the previous default globally:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::configureUsing(static function (FileUpload $component): void {
    $component->preventFilePathTampering(false);
});
```

Disabling this protection requires you to authorize submitted paths yourself to prevent access to other users' or tenants' files.

See [authorizing existing file paths](../forms/file-upload#authorizing-existing-file-paths).
</Disclosure>

<Disclosure open x-show="packages.includes('forms')">
<span slot="summary">`RichEditor` now prevents file attachment path tampering by default</span>

`RichEditor` now rejects image attachment IDs that were not already stored in the record's original content. New uploads are unaffected, as are attachments using the Spatie Media Library provider.

If you insert existing images from a shared library or another record, allow those IDs using `allowFilePathUsing`. To restore the previous default globally:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::configureUsing(static function (RichEditor $component): void {
    $component->preventFileAttachmentPathTampering(false);
});
```

Disabling this protection requires you to authorize submitted attachment IDs yourself.

See [securing file attachment IDs](../forms/rich-editor#securing-file-attachment-ids).
</Disclosure>

<Disclosure open x-show="packages.includes('actions')">
<span slot="summary">Exports and import failure CSVs now prevent formula injection by default</span>

Exported strings and import failure CSV cells beginning with `=`, `+`, `-`, `@`, a tab, or a carriage return are now prefixed with a single quote to prevent spreadsheet formula execution. Numeric strings such as `-5` are unchanged. Values such as the phone number `+44 1234 567890` gain a quote, which may affect systems that consume these files.

For trusted data only, you can restore the previous defaults globally:

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

The previously unlimited bulk selection now defaults to `maxSelectableRecords(10000)`. If your app processes larger selections, raise the limit on the affected table. To restore unlimited selection globally:

```php
use Filament\Tables\Table;

Table::configureUsing(static function (Table $table): void {
    $table->maxSelectableRecords(null);
});
```

For database-backed tables, oversized bulk actions process only a subset of the selection, including inverse selections tracking deselected records. They do not reject the whole action. Custom-data tables throw a `LogicException` instead. The all-pages "select all" link is hidden when the selectable count exceeds the limit.

The limit does not guarantee bounded memory usage or execution time. See [selection limits](../tables/actions#limiting-the-number-of-selectable-records) and [bulk-action performance](../tables/actions#improving-the-performance-of-bulk-actions) before increasing it.
</Disclosure>

<Disclosure open x-show="packages.includes('forms') || packages.includes('infolists')">
<span slot="summary">Repeaters, builders, and repeatable entries now limit item processing by default</span>

`Repeater` and `Builder` now limit processing to 500 items per component; `RepeatableEntry` limits it to 1,000. Previously, processing was unlimited. Exceeding the limit aborts with HTTP 413 without truncating data, including when opening existing records.

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

This is separate from `maxItems()` validation and is not a request-size or execution-time guarantee. Query Builder retains its separate rule-tree limits.

See [limiting item processing](../forms/repeater#limiting-item-processing).
</Disclosure>

<Disclosure open x-show="packages.includes('tables')">
<span slot="summary">Tables now enforce pagination options by default</span>

Submitted page sizes must now match the table's configured pagination options. If you set page sizes directly in PHP, include them in those options, or opt the table out with `enforcePaginationPageOptions(false)`.

To restore the previous default globally:

```php
use Filament\Tables\Table;

Table::configureUsing(static function (Table $table): void {
    $table->enforcePaginationPageOptions(false);
});
```

Invalid sizes fall back to a valid saved selection or the default option. Stale saved selections fall back to the first option. Explicit `'all'` options and `paginated(false)` remain unrestricted.

See [enforcing pagination options](../tables/overview#enforcing-the-pagination-options), including fixed page sizes without a selector.
</Disclosure>

<Disclosure open x-show="packages.includes('tables')">
<span slot="summary">`QueryBuilder` filters now limit rule-tree complexity by default</span>

Rule trees now default to at most 100 conditions and a nesting depth of 5. Previously, both were unlimited. If existing or submitted state exceeds either limit, **the filter is ignored and no constraints are applied**.

For larger filters, raise `maxRules()` and `maxNestingDepth()` on the `QueryBuilder` filter. To restore unlimited rule trees globally, at the risk of excessive resource consumption:

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

App authentication now accepts codes from the current 30-second period and one period on either side, rather than eight. Synchronize server and user-device clocks if valid codes are rejected. To temporarily restore the previous tolerance, configure the provider in your panel's `multiFactorAuthentication()` configuration:

```php
use Filament\Auth\MultiFactor\App\AppAuthentication;

AppAuthentication::make()
    ->codeWindow(8)
```

See [changing the app code expiration time](../users/multi-factor-authentication#changing-the-app-code-expiration-time).
</Disclosure>

<Disclosure open x-show="packages.includes('schemas')">
<span slot="summary">Livewire file uploads are now restricted to schema components by default</span>

Components using `InteractsWithSchemas` now reject uploads with HTTP 403 unless the target property belongs to a schema component that supports uploads. Uploads through Filament fields are unaffected.

If your component uploads directly to a non-schema property, opt out on that Livewire component and secure that upload yourself:

```php
public function shouldRestrictFileUploadsToSchemaComponents(): bool
{
    return false;
}
```

The upgrade script removes the obsolete `RestrictsFileUploadsToSchemaComponents` trait automatically. See [restricting Livewire file uploads](../advanced/security#restricting-livewire-file-uploads-to-schema-components).
</Disclosure>

<Disclosure open x-show="packages.includes('actions')">
<span slot="summary">OpenSpout has been upgraded to v5</span>

If you customize XLSX exports through OpenSpout, update your code for v5's immutable objects. Use constructor arguments or `with*()` methods instead of setters, and apply cell styles through `Cell::fromValue()` or `Row::fromValuesWithStyles()` rather than a default row style.

See [customizing XLSX files](../actions/export#customizing-xlsx-files) and the [OpenSpout upgrade guide](https://github.com/openspout/openspout/blob/5.x/UPGRADE.md#upgrading-from-4x-to-50).
</Disclosure>

<Disclosure open x-show="packages.includes('panels')">
<span slot="summary">Custom avatar providers must accept non-Eloquent users</span>

`AvatarProvider::get()` now accepts `Model | Authenticatable` instead of only `Model`. The script updates declarations directly on providers and interfaces extending `AvatarProvider`; update methods supplied by traits or non-provider parent classes manually.

```php
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

public function get(Model | Authenticatable $record): string
{
    // Return the avatar URL for the record.
}
```

Review Eloquent-specific calls if your app uses non-Eloquent users. Already-compatible parameter types need no change. See [custom avatar providers](../users/overview#using-a-different-avatar-provider).
</Disclosure>

<Disclosure open x-show="packages.includes('actions')">
<span slot="summary">Import and export job tags are now namespaced</span>

Job tags change from `import{id}` and `export{id}` to `filament:import:{id}` and `filament:export:{id}`. Update filters or monitoring that use them. Existing tags and custom `getJobTags()` overrides are unchanged.
</Disclosure>

<Disclosure open x-show="packages.includes('schemas') || packages.includes('forms') || packages.includes('tables') || packages.includes('panels') || packages.includes('widgets')">
<span slot="summary">Deprecated lazy-loading and blur-binding APIs have been removed</span>

Use the current loading APIs:

| Affected classes | Removed methods | Replacements |
| --- | --- | --- |
| `Schema`, `Table` | `deferLoading()`, `isLoadingDeferred()` | `lazyLoaded()`, `isLazyLoaded()` |
| Schema `Livewire`, `LivewireField` | `lazy()`, `isLazy()` | `lazyLoaded()`, `isLazyLoaded()` |
| `Tab`, `RelationGroup` | `deferBadge()`, `isBadgeDeferred()` | `lazyLoadedBadge()`, `isBadgeLazyLoaded()` |
| `Widget`, `RelationManager` | `isLazy()` | `isLazyLoaded()` |
| `RelationManager` | `isBadgeDeferred()` | `isBadgeLazyLoaded()` |
| `Tabs` | `getDeferredTabBadges()`, `hasDeferredBadges()` | `getLazyLoadedTabBadges()`, `hasLazyLoadedBadges()` |

Rename overridden loading properties too: `$isLoadingDeferred` and `$isLazy` become `$isLazyLoaded`; `$isBadgeDeferred` becomes `$isBadgeLazyLoaded`. On widgets and relation managers, declare these static properties as `?bool`, preserving your configured value:

```php
protected static ?bool $isLazyLoaded = false;
```

When migrating direct property reads, use `static::$isLazyLoaded ?? true` or `static::$isBadgeLazyLoaded ?? false` to preserve the previous non-null defaults.

For state binding on `Schema` and schema components other than embedded Livewire components, replace `lazy()` with `live(onBlur: true)` and `isLazy()` with `isLiveOnBlur()`. This controls when input changes reach the server, not when a component loads.

The upgrade script migrates statically identified PHP calls and unambiguous overrides declared in the same file. Manually reconcile old/new member collisions, grouped property declarations, traits, cross-file custom overrides, dynamic names, callbacks, unknown receiver types, and non-PHP references. Static property writes, references, `isset()` / `unset()`, array access, and argument uses are left for manual migration, along with dependent getters. Review base-typed receivers that may hold custom subclasses and preserve custom dispatch when updating `parent::` calls.

Custom blur-binding `lazy()` overrides and first-class `lazy(...)` callables require manual migration; calls passing arguments to the old blur-binding method are also left unchanged. Custom frontend calls to `$getDeferredTabBadges()` must use `$getLazyLoadedTabBadges()` instead. Livewire's native `lazy` mount parameter is unchanged.
</Disclosure>

<Disclosure open x-show="packages.includes('actions')">
<span slot="summary">Deprecated action method aliases have been removed</span>

The script renames calls on `Filament\Actions\Action` and its subclasses:

| Removed method | Replacement |
| --- | --- |
| `emit()` | `dispatch()` |
| `emitSelf()` | `dispatchSelf()` |
| `emitTo()` | `dispatchTo()` |
| `disableForm()`, `disabledForm()` | `disabledSchema()` |
| `form()` | `schema()` |
| `getForm()` | `getSchema()` |
| `isFormDisabled()` | `isSchemaDisabled()` |
| `mutateFormDataUsing()` | `mutateDataUsing()` |
| `formData()` | `data()` |
| `resetFormData()` | `resetData()` |
| `getFormData()` | `getData()` |
| `getRawFormData()` | `getRawData()` |

It also renames `form:` arguments to `schema:`. Calls unpacking arguments into `form()` are left unchanged; rename the method and any `'form'` argument key manually. Update named arguments when invoking migrated first-class callables yourself.

Manually migrate custom overrides (including inherited ones), class-qualified calls such as `parent::disabledForm()`, dynamic names, callbacks, unknown receiver types, and non-PHP references. Calls on known overrides are skipped; review changes where a base-typed receiver may hold a custom subclass. Classes using action concerns without extending `Action` also need manual migration.

Preserve custom parameter names, defaults, and dispatch behavior: the old wrappers called replacements through `$this`, so simply renaming a `parent::` call may bypass a subclass override.
</Disclosure>

<Disclosure open x-show="packages.includes('tables')">
<span slot="summary">Deprecated fluent table method aliases have been removed</span>

The script renames calls on `Filament\Tables\Table` and its subclasses, preserving named and unpacked arguments:

| Removed method | Replacement |
| --- | --- |
| `actions()` | `recordActions()` |
| `pushActions()` | `pushRecordActions()` |
| `actionsColumnLabel()` | `recordActionsColumnLabel()` |
| `actionsAlignment()` | `recordActionsAlignment()` |
| `actionsPosition()` | `recordActionsPosition()` |
| `getActions()` | `getRecordActions()` |
| `getActionsPosition()` | `getRecordActionsPosition()` |
| `getActionsAlignment()` | `getRecordActionsAlignment()` |
| `getActionsColumnLabel()` | `getRecordActionsColumnLabel()` |
| `bulkActions()` | `toolbarActions()` |
| `pushBulkActions()` | `pushToolbarActions()` |
| `getBulkActions()` | `getToolbarActions()` |
| `toggleColumnsTriggerAction()` | `columnManagerTriggerAction()` |
| `columnToggleFormColumns()` | `columnManagerColumns()` |
| `columnToggleFormMaxHeight()` | `columnManagerMaxHeight()` |
| `columnToggleFormWidth()` | `columnManagerWidth()` |
| `getColumnToggleFormColumns()` | `getColumnManagerColumns()` |
| `getColumnToggleFormMaxHeight()` | `getColumnManagerMaxHeight()` |
| `getColumnToggleFormWidth()` | `getColumnManagerWidth()` |
| `getToggleColumnsTriggerAction()` | `getColumnManagerTriggerAction()` |

`getColumnManagerWidth()` returns `Filament\Support\Enums\Width | string | null`, not the old getter's `?string`. Review string-only consumers and your own return types. To convert an enum to a string, use `$width instanceof Width ? $width->value : $width`, importing `Filament\Support\Enums\Width`.

Manually migrate custom overrides (including inherited ones), class-qualified calls, dynamic names, callbacks, unknown receiver types, and non-PHP references. Calls on known overrides are skipped; review changes where a base-typed receiver may hold a custom subclass. Classes using table concerns without extending `Table` also need manual migration.

Preserve custom parameters and return behavior. The old wrappers dispatched through `$this`, so renaming `parent::` calls can bypass subclass overrides. Record-action and bulk-action setters also returned `$this` regardless of the replacement's return value.

`groupedBulkActions()` and legacy Livewire configuration hooks such as `getTableActions()` remain available.
</Disclosure>

<Disclosure open x-show="packages.includes('actions') || packages.includes('tables')">
<span slot="summary">Deprecated action and filter convenience classes have been removed</span>

The script replaces direct factories as follows, preserving subsequent chained configuration:

| Removed factory | Replacement |
| --- | --- |
| `Filament\Actions\ButtonAction::make()` | `Filament\Actions\Action::make()->button()` |
| `Filament\Actions\IconButtonAction::make()` | `Filament\Actions\Action::make()->iconButton()` |
| `Filament\Tables\Filters\MultiSelectFilter::make()` | `Filament\Tables\Filters\SelectFilter::make()->multiple()` |

It also renames PHP class references, including custom parent classes, types, and `::class`. Review these cases:

- **Global configuration, bindings, and type checks:** These now target the parent class and may affect unrelated actions or filters. Move configuration to individual components or a custom subclass if it should remain scoped. Explicit style and multiplicity methods now run after `make()` configuration callbacks, which may change configuration precedence or the values callbacks observe.
- **Custom subclasses:** Add `button()`, `iconButton()`, or `multiple()` after `parent::setUp()` in your subclass where needed; the script does not add this configuration.
- **Other construction paths:** Direct constructors, dynamic factories, and first-class factory callables receive class renames but not the explicit configuration methods. Add them manually. Update string and non-PHP references yourself, and remove unused imports.
</Disclosure>

<Disclosure open x-show="packages.includes('tables')">
<span slot="summary">Legacy query builder constraint namespaces have been removed</span>

The script replaces PHP references under `Filament\Tables\Filters\QueryBuilder\Constraints` with `Filament\QueryBuilder\Constraints`, including operators, traits, subclasses, and PHPDoc. Update string and non-PHP references manually and remove unused imports.

The filter itself remains `Filament\Tables\Filters\QueryBuilder`; its configuration is unchanged.
</Disclosure>

<Disclosure open x-show="packages.includes('tables')">
<span slot="summary">Deprecated column-toggle render hook constants have been removed</span>

The script replaces `TablesRenderHook::TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER` and `TOOLBAR_TOGGLE_COLUMN_TRIGGER_BEFORE` with `TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER` and `TOOLBAR_COLUMN_MANAGER_TRIGGER_BEFORE`.

Their string values are unchanged, so registrations using literal strings need no changes.
</Disclosure>

<Disclosure open>
<span slot="summary">The deprecated `IconSize::ExtraExtraLarge` alias has been removed</span>

The script replaces `Filament\Support\Enums\IconSize::ExtraExtraLarge` with `IconSize::TwoExtraLarge`. Its value remains `2xl`. Update string, dynamic, and non-PHP references manually.
</Disclosure>

<Disclosure open>
<span slot="summary">The deprecated badge delete button icon alias has been removed</span>

Remove registrations using `SupportIconAlias::BADGE_DELETE_BUTTON`. Customize `.fi-badge-delete-btn-icon` instead; there is no replacement icon alias or automated migration.
</Disclosure>

<Disclosure open>
<span slot="summary">Sticky modals have a new scroll wrapper</span>

Modals with `stickyHeader()` or `stickyFooter()` now wrap their header, content, and footer in `.fi-modal-window-scroll`, except for slide-overs and screen-width modals. Update custom CSS that targets those elements as direct children of `.fi-modal-window`.
</Disclosure>

<Disclosure open>
<span slot="summary">The Blade capture directive dependency has been removed</span>

Filament now provides `@capture` and `@endcapture` itself, so existing views need no changes. Remove `RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider` from plugin tests or other explicit provider registrations.

If you need these directives without Filament, require `ryangjchandler/blade-capture-directive` directly.
</Disclosure>

</div>
