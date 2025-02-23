---
title: Upgrade Guide
contents: false
---
import Checkbox from "@components/Checkbox.astro"
import Checkboxes from "@components/Checkboxes.astro"
import Disclosure from "@components/Disclosure.astro"

> If you see anything missing from this guide, please do not hesitate to [make a pull request](https://github.com/filamentphp/filament/edit/4.x/docs/09-upgrade-guide.md) to our repository! Any help is appreciated!

## New requirements

- PHP 8.2+
- Laravel v11.15+
- Tailwind CSS v4.0+, if you are currently using Tailwind CSS v3.0 with Filament. This does not apply if you are just using a Filament panel without a custom theme CSS file.
- Filament no longer requires `doctrine/dbal`, but if your application still does, and you do not have it installed directly, you should add it to your `composer.json` file.

## Upgrading automatically

The easiest way to upgrade your app is to run the automated upgrade script. This script will automatically upgrade your application to the latest version of Filament and make changes to your code, which handles most breaking changes.

```bash
composer require filament/upgrade:"^4.0" -W --dev

vendor/bin/filament-v4
```

Make sure to carefully follow the instructions, and review the changes made by the script. You may need to make some manual changes to your code afterwards, but the script should handle most of the repetitive work for you.

Finally, you must run `php artisan filament:install` to finalize the Filament v4 installation. This command must be run for all new Filament projects.

You can now `composer remove filament/upgrade` as you don't need it anymore.

> Some plugins you're using may not be available in v4 just yet. You could temporarily remove them from your `composer.json` file until they've been upgraded, replace them with a similar plugins that are v4-compatible, wait for the plugins to be upgraded before upgrading your app, or even write PRs to help the authors upgrade them.

## Upgrading manually

After upgrading the dependencies via Composer, you should execute `php artisan filament:upgrade` in order to clear any Laravel caches and publish the new frontend assets.

<div x-data="{ packages: ['panels', 'forms', 'infolists', 'tables', 'actions', 'notifications', 'widgets', 'support'] }">

To begin, filter the upgrade guide for your specific needs by selecting only the packages that you use in your project:

<Checkboxes>
    <Checkbox value="panels" model="packages">
        Panels
    </Checkbox>

    <Checkbox value="forms" model="packages">
        Forms

        <span slot="description">
            This package is also often used in a panel, or using the tables or actions package.
        </span>
    </Checkbox>

    <Checkbox value="infolists" model="packages">
        Infolists

        <span slot="description">
            This package is also often used in a panel, or using the tables or actions package.
        </span>
    </Checkbox>

    <Checkbox value="tables" model="packages">
        Tables

        <span slot="description">
            This package is also often used in a panel.
        </span>
    </Checkbox>

    <Checkbox value="actions" model="packages">
        Actions

        <span slot="description">
            This package is also often used in a panel.
        </span>
    </Checkbox>

    <Checkbox value="notifications" model="packages">
        Notifications

        <span slot="description">
            This package is also often used in a panel.
        </span>
    </Checkbox>

    <Checkbox value="widgets" model="packages">
        Widgets

        <span slot="description">
            This package is also often used in a panel.
        </span>
    </Checkbox>

    <Checkbox value="support" model="packages">
        Blade UI components
    </Checkbox>
</Checkboxes>

### High-impact changes

<Disclosure open>
<span slot="summary">The `FILAMENT_FILESYSTEM_DISK` environment variable</span>

If you hadn't published the Filament configuration file to `config/filament.php`, Filament will now reference the `FILESYSTEM_DISK` environment variable instead of `FILAMENT_FILESYSTEM_DISK` when uploading files. Laravel also uses this environment variable to represent the default filesystem disk, so this change aims to bring Filament in line to avoid potential confusion.

If you have published the Filament configuration file, and you have a `default_filesystem_disk` key set, you can ignore this change as your app will continue to use the old configuration value.

If you have not published the Filament configuration file, or you have removed the `default_filesystem_disk` key from it:

- If you do not have a `FILAMENT_FILESYSTEM_DISK` environment variable set:
    - If the `FILESYSTEM_DISK` environment variable is set to `public`, you can ignore this change, since `public` was the default value for `FILAMENT_FILESYSTEM_DISK` before.
    - If the `FILESYSTEM_DISK` environment variable is set to something else, you can ignore this change if you are happy with the `FILESYSTEM_DISK` value being used to upload files in Filament. Please be aware that not using a `public` or `s3` disk may lead to unexpected behaviour, as the `local` disk stores private files but is unable to generate public URLs for previews or downloads.
- If you do have a `FILAMENT_FILESYSTEM_DISK` environment variable set:
    - If it is the same as `FILESYSTEM_DISK`, you can remove it completely.
    - If it is different from `FILESYSTEM_DISK`, and it should be, you can [publish the Filament configuration file](installation#publishing-configuration) and set the `default_filesystem_disk` to reference the old `FILAMENT_FILESYSTEM_DISK` environment variable like it was before.
</Disclosure>

<Disclosure open x-show="packages.includes('tables')">
<span slot="summary">The `all` pagination page option is not available for tables by default</span>

The `all` pagination page method is now not available for tables by default. If you want to use it on a table, you can add it to the configuration:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->paginated([5, 10, 25, 50, 'all']);
}
```

Be aware when using `all` as it will cause performance issues when dealing with a large number of records.

Alternatively, you can do it for all tables at once using a global setting in the `boot()` method of a service provider:

```php
use Filament\Tables\Table;

Table::configureUsing(function (Table $table): void {
    $table->paginationPageOptions([5, 10, 25, 50, 'all']);
});
```
</Disclosure>

### Medium-impact changes

<Disclosure x-show="packages.includes('panels')">
<span slot="summary">Automatic tenancy global scoping and association</span>

When using tenancy in v3, Filament only scoped resource queries to the current tenant: to render the resource table, resolve URL parameters, and fetch global search results. There were many situations where other queries in the panel were not scoped by default, and the developer had to manually scope them. While this was a documented feature, it created a lot of additional work for developers.

In v4, Filament automatically scopes all queries in a panel to the current tenant, and automatically associates new records with the current tenant using model events. This means that you no longer need to manually scope queries or associate new Eloquent records in most cases. There are still some important points to consider, so the [documentation](panels/tenancy#tenancy-security) has been updated to reflect this.
</Disclosure>

<Disclosure x-show="packages.includes('panels')">
<span slot="summary">The `$maxContentWidth` property on page classes</span>

The `$maxContentWidth` property on page classes has a new type. It is now able to accept `MaxWidth` enum values, as well as strings and null:

```php
use Filament\Support\Enums\Width;

protected MaxWidth | string | null $maxContentWidth = null;
```
</Disclosure>

<Disclosure x-show="packages.includes('forms')">
<span slot="summary">The `Radio` `inline()` method behavior</span>

In v3, the `inline()` method put the radio buttons inline with each other, and also inline with the label at the same time. This is inconsistent with other components.

In v4, the `inline()` method now only puts the radio buttons inline with each other, and not with the label. If you want the radio buttons to be inline with the label, you can use the `inlineLabel()` method as well.

If you were previously using `inline()->inlineLabel(false)` to achieve the v4 behaviour, you can now simply use `inline()`.
</Disclosure>

<Disclosure x-show="packages.includes('actions')">
<span slot="summary">Import and export job retries</span>

In Filament v3, import and export jobs were retries continuously for 24 hours if they failed, with no backoff between tries by default.

In v4, they are retried 3 times with a 60 second backoff between each retry.

This behaviour can be customized in the [importer](prebuilt-actions/import#customizing-the-import-job-retries) and [exporter](prebuilt-actions/export#customizing-the-export-job-retries) classes.
</Disclosure>

<Disclosure x-show="packages.includes('widgets')">
<span slot="summary">The `InteractsWithPageFilters` `$filters` property is now `$pageFilters`</span>

In v3, the `$this->filters` property from the `InteractsWithPageFilters` trait was used to access the raw data from the filters form. In v4, this property has been renamed to `$this->pageFilters`, to avoid conflicts with the new `$filters` property that can be used to access filter values from a chart widget's filter form.

```diff
use InteractsWithPageFilters;

public function getStats(): array
{
-    $startDate = $this->filters['startDate'] ?? null;
-    $endDate = $this->filters['endDate'] ?? null;
+    $startDate = $this->pageFilters['startDate'] ?? null;
+    $endDate = $this->pageFilters['endDate'] ?? null;

    return [
        // ...
    ];
}
```
</Disclosure>

### Low-impact changes

<Disclosure x-show="packages.includes('tables') || packages.includes('infolists')">
<span slot="summary">The `isSeparate` parameter of `ImageColumn::limitedRemainingText()` and `ImageEntry::limitedRemainingText()` has been removed</span>

Previously, users were able to display the number of limited images separately to an image stack using the `isSeparate` parameter. Now the parameter has been removed, and if a stack exists, the text will always be stacked on top and not separate. If the images are not stacked, the text will be separate.
</Disclosure>

<Disclosure x-show="packages.includes('forms')">
<span slot="summary">Overriding the `Field::make()`, `MorphToSelect::make()`, `Placeholder::make()`, or `Builder\Block::make()` methods</span>

The signature for the `Field::make()`, `MorphToSelect::make()`, `Placeholder::make()`, and `Builder\Block::make()` methods has changed. Any classes that extend the `Field`, `MorphToSelect`, `Placeholder`, or `Builder\Block` class and override the `make()` method must update the method signature to match the new signature. The new signature is as follows:

```php
public static function make(?string $name = null): static
```

This is due to the introduction of the `getDefaultName()` method, that can be overridden to provide a default `$name` value if one is not specified (`null`). If you were previously overriding the `make()` method in order to provide a default `$name` value, it is advised that you now override the `getDefaultName()` method instead, to avoid further maintenance burden in the future:

```php
public static function getDefaultName(): ?string
{
    return 'default';
}
```

If you are overriding the `make()` method to pass default configuration to the object once it is instantiated, please note that it is recommended to instead override the `setUp()` method, which is called immediately after the object is instantiated:

```php
protected function setUp(): void
{
    parent::setUp();

    $this->label('Default label');
}
```

Ideally, you should avoid overriding the `make()` method altogether as there are alternatives like `setUp()`, and doing so causes your code to be brittle if Filament decides to introduce new constructor parameters in the future.
</Disclosure>

<Disclosure x-show="packages.includes('infolists')">
<span slot="summary">Overriding the `Entry::make()` method</span>

The signature for the `Entry::make()` method has changed. Any classes that extend the `Entry` class and override the `make()` method must update the method signature to match the new signature. The new signature is as follows:

```php
public static function make(?string $name = null): static
```

This is due to the introduction of the `getDefaultName()` method, that can be overridden to provide a default `$name` value if one is not specified (`null`). If you were previously overriding the `make()` method in order to provide a default `$name` value, it is advised that you now override the `getDefaultName()` method instead, to avoid further maintenance burden in the future:

```php
public static function getDefaultName(): ?string
{
    return 'default';
}
```

If you are overriding the `make()` method to pass default configuration to the object once it is instantiated, please note that it is recommended to instead override the `setUp()` method, which is called immediately after the object is instantiated:

```php
protected function setUp(): void
{
    parent::setUp();

    $this->label('Default label');
}
```

Ideally, you should avoid overriding the `make()` method altogether as there are alternatives like `setUp()`, and doing so causes your code to be brittle if Filament decides to introduce new constructor parameters in the future.
</Disclosure>

<Disclosure x-show="packages.includes('tables')">
<span slot="summary">Overriding the `Column::make()` or `Constraint::make()` methods</span>

The signature for the `Column::make()` and `Constraint::make()` methods has changed. Any classes that extend the `Column` or `Constraint` class and override the `make()` method must update the method signature to match the new signature. The new signature is as follows:

```php
public static function make(?string $name = null): static
```

This is due to the introduction of the `getDefaultName()` method, that can be overridden to provide a default `$name` value if one is not specified (`null`). If you were previously overriding the `make()` method in order to provide a default `$name` value, it is advised that you now override the `getDefaultName()` method instead, to avoid further maintenance burden in the future:

```php
public static function getDefaultName(): ?string
{
    return 'default';
}
```

If you are overriding the `make()` method to pass default configuration to the object once it is instantiated, please note that it is recommended to instead override the `setUp()` method, which is called immediately after the object is instantiated:

```php
protected function setUp(): void
{
    parent::setUp();

    $this->label('Default label');
}
```

Ideally, you should avoid overriding the `make()` method altogether as there are alternatives like `setUp()`, and doing so causes your code to be brittle if Filament decides to introduce new constructor parameters in the future.
</Disclosure>

<Disclosure x-show="packages.includes('actions')">
<span slot="summary">Overriding the `ExportColumn::make()` or `ImportColumn::make()` methods</span>

The signature for the `ExportColumn::make()` and `ImportColumn::make()` methods has changed. Any classes that extend the `ExportColumn` or `ImportColumn` class and override the `make()` method must update the method signature to match the new signature. The new signature is as follows:

```php
public static function make(?string $name = null): static
```

This is due to the introduction of the `getDefaultName()` method, that can be overridden to provide a default `$name` value if one is not specified (`null`). If you were previously overriding the `make()` method in order to provide a default `$name` value, it is advised that you now override the `getDefaultName()` method instead, to avoid further maintenance burden in the future:

```php
public static function getDefaultName(): ?string
{
    return 'default';
}
```

If you are overriding the `make()` method to pass default configuration to the object once it is instantiated, please note that it is recommended to instead override the `setUp()` method, which is called immediately after the object is instantiated:

```php
protected function setUp(): void
{
    parent::setUp();

    $this->label('Default label');
}
```

Ideally, you should avoid overriding the `make()` method altogether as there are alternatives like `setUp()`, and doing so causes your code to be brittle if Filament decides to introduce new constructor parameters in the future.
</Disclosure>

<Disclosure x-show="packages.includes('actions')">
<span slot="summary">Authenticating the user inside the import and export jobs</span>

In v3, the `Illuminate\Auth\Events\Login` event was fired from the import and export jobs, to set the current user. This is no longer the case in v4: the user is authenticated, but that event is not fired, to avoid running any listeners that should only run for actual user logins.
</Disclosure>

<Disclosure>
<span slot="summary">European Portuguese translations</span>

The European Portuguese translations have been moved from `pt_PT` to `pt`, which appears to be the more commonly used language code for the language within the Laravel community.
</Disclosure>

<Disclosure>
<span slot="summary">Nepalese translations</span>

The Nepalese translations have been moved from `np` to `ne`, which appears to be the more commonly used language code for the language within the Laravel community.
</Disclosure>

<Disclosure>
<span slot="summary">Norwegian translations</span>

The Norwegian translations have been moved from `no` to `nb`, which appears to be the more commonly used language code for the language within the Laravel community.
</Disclosure>

<Disclosure x-show="packages.includes('panels')">
<span slot="summary">`getCurrentPanel()` no longer returns the default panel as a fallback</span>

In v4, the `getCurrentPanel()` method returned the default panel if no panel was set. While this was useful behaviour internally in Filament core, it was unexpected for developers. In v4, `getCurrentPanel()` will return `null` if no panel is set, and you should handle this case in your code.

```php
use Filament\Facades\Filament;

Filament::getCurrentPanel();
filament()->getCurrentPanel();
```

If you are a plugin author and would like to get the default panel if no panel is set, you can use the `getCurrentOrDefaultPanel()` method instead:

```php
use Filament\Facades\Filament;

Filament::getCurrentOrDefaultPanel();
filament()->getCurrentOrDefaultPanel();
```
</Disclosure>

</div>
