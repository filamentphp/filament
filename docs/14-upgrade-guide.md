---
title: Upgrade guide
---
import Aside from "@components/Aside.astro"
import Checkbox from "@components/Checkbox.astro"
import Checkboxes from "@components/Checkboxes.astro"
import Disclosure from "@components/Disclosure.astro"

<Aside variant="info">
    If you see anything missing from this guide, please don’t hesitate to [make a pull request](https://github.com/filamentphp/filament/edit/4.x/docs/14-upgrade-guide.md) to our repository! Any help is appreciated!
</Aside>

## New requirements

- PHP 8.2+
- Laravel v11.28+
- Tailwind CSS v4.0+, if you’re currently using Tailwind CSS v3.0 with Filament. This doesn’t apply if you’re just using a Filament panel without a custom theme CSS file.
- Filament no longer requires `doctrine/dbal`, but if your application still does, and you don’t have it installed directly, you should add it to your `composer.json` file.

## Running the automated upgrade script

<Aside variant="info">
    The upgrade script is not a replacement for the upgrade guide. It handles many small changes that aren’t mentioned in the upgrade guide, but it doesn’t handle all breaking changes. You should still read the [manual upgrade steps](#breaking-changes-that-must-be-handled-manually) to see what changes you need to make to your code.
</Aside>

<Aside variant="info">
    Some plugins you're using may not be available in v4 just yet. You could temporarily remove them from your `composer.json` file until they've been upgraded, replace them with a similar plugins that are v4-compatible, wait for the plugins to be upgraded before upgrading your app, or even write PRs to help the authors upgrade them.
</Aside>

The first step to upgrade your Filament app is to run the automated upgrade script. This script will automatically upgrade your application to the latest version of Filament and make changes to your code, which handles most breaking changes:

```bash
composer require filament/upgrade:"^4.0" -W --dev

vendor/bin/filament-v4

# Run the commands output by the upgrade script, they are unique to your app
composer require filament/filament:"^4.0" -W --no-update
composer update
```

<Aside variant="warning">
    When using Windows PowerShell to install Filament, you may need to run the command below, since it ignores `^` characters in version constraints:

    ```bash
    composer require filament/upgrade:"~4.0" -W --dev

    vendor/bin/filament-v4

    # Run the commands output by the upgrade script, they are unique to your app
    composer require filament/filament:"~4.0" -W --no-update
    composer update
    ```
</Aside>

<Aside variant="warning">
    If installing the upgrade script fails, make sure that your PHPStan version is at least v2, or your Larastan version is at least v3. The script uses Rector v2, which requires PHPStan v2 or higher.
</Aside>

Make sure to carefully follow the instructions, and review the changes made by the script. You may need to make some manual changes to your code afterwards, but the script should handle most of the repetitive work for you.

Filament v4 introduces a new default directory structure for your Filament resources and clusters. If you’re using Filament panels with resources and clusters, you can choose to keep the old directory structure, or migrate to the new one. If you want to migrate to the new directory structure, you can run the following command:

```bash
php artisan filament:upgrade-directory-structure-to-v4 --dry-run
```

The `--dry-run` option will show you what the command would do without actually making any changes. If you’re happy with the changes, you can run the command without the `--dry-run` option to apply the changes:

```bash
php artisan filament:upgrade-directory-structure-to-v4
```

<Aside variant="warning">
    This directory upgrade script is not able to perfectly update any references to classes in the same namespace that were present in resource and cluster files, and those references will need to be updated manually after the script has run. You should use tools like [PHPStan](https://phpstan.org) to identify references to classes that are broken after the upgrade.
</Aside>

You can now `composer remove filament/upgrade --dev` as you don't need it anymore.

## Publishing the configuration file

Some changes in Filament v4 can be reverted using the configuration file. If you haven't published the configuration file yet, you can do so by running the following command:

```bash
php artisan vendor:publish --tag=filament-config
```

Firstly, the `default_filesystem_disk` in v4 is set to the `FILESYSTEM_DISK` variable instead of `FILAMENT_FILESYSTEM_DISK`. To preserve the v3 behavior, make sure you use this setting:

```php
return [

    // ...

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    // ...

]
```

v4 introduces some changes to how Filament generates files. A new `file_generation` section has been added to the v4 configuration file, so that you can revert back to the v3 style if you would like to keep new code consistent with how it looked before upgrading. If your configuration file doesn't already have a `file_generation` section, you should add it yourself, or re-publish the configuration file and tweak it to your liking:

```php
use Filament\Support\Commands\FileGenerators\FileGenerationFlag;

return [

    // ...

    'file_generation' => [
        'flags' => [
            FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_SCHEMAS, // Define new forms and infolists inside the resource class instead of a separate schema class.
            FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_TABLES, // Define new tables inside the resource class instead of a separate table class.
            FileGenerationFlag::PANEL_CLUSTER_CLASSES_OUTSIDE_DIRECTORIES, // Create new cluster classes outside of their directories. Not required if you run `php artisan filament:upgrade-directory-structure-to-v4`.
            FileGenerationFlag::PANEL_RESOURCE_CLASSES_OUTSIDE_DIRECTORIES, // Create new resource classes outside of their directories. Not required if you run `php artisan filament:upgrade-directory-structure-to-v4`.
            FileGenerationFlag::PARTIAL_IMPORTS, // Partially import components such as form fields and table columns instead of importing each component explicitly.
        ],
    ],

    // ...

]
```

<Aside variant="tip">
    The `filament/upgrade` package includes a command to help you move panel resources and clusters to the new directory structure, which is the default in v4:

    ```bash
    php artisan filament:upgrade-directory-structure-to-v4 --dry-run
    ```

    The `--dry-run` option will show you what the command would do without actually making any changes. If you are happy with the changes, you can run the command without the `--dry-run` option to apply the changes:

    ```bash
    php artisan filament:upgrade-directory-structure-to-v4
    ```

    This directory upgrade script is not able to perfectly update any references to classes in the same namespace that were present in resource and cluster files, and those references will need to be updated manually after the script has run. You should use tools like [PHPStan](https://phpstan.org) to identify references to classes that are broken after the upgrade.

    Once you have run the command, you do not need to keep the `FileGenerationFlag::PANEL_CLUSTER_CLASSES_OUTSIDE_DIRECTORIES` or `FileGenerationFlag::PANEL_RESOURCE_CLASSES_OUTSIDE_DIRECTORIES` flags in your configuration file, as the new directory structure is now the default. You can remove them from the `file_generation.flags` array.
</Aside>

## Breaking changes that must be handled manually

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

    <Checkbox value="spatie-translatable-plugin" model="packages">
        Spatie Translatable Plugin
    </Checkbox>
</Checkboxes>

### High-impact changes

<Disclosure open x-show="packages.includes('forms') || packages.includes('infolists') || packages.includes('tables')">
<span slot="summary">File visibility is now private by default for non-local disks</span>

In addition to the [default disk being changed to `local`](#publishing-the-configuration-file), the file visibility settings for non-local disks (such as `s3`, but not `public` or `local`) across various components have been changed to `private` instead of `public` by default. This means that files aren't publicly accessible by default, and you need to generate a temporary signed URL to access them. This change affects the following components:

- `FileUpload` form field, including `SpatieMediaLibraryFileUpload`
- `ImageColumn` table column, including `SpatieMediaLibraryImageColumn`
- `ImageEntry` infolist entry, including `SpatieMediaLibraryImageEntry`

<Aside variant="tip">
    If you use a non-local disk such as `s3`, you can preserve the old default behavior across your entire app by adding the following code in the `boot()` method of a service provider like `AppServiceProvider`:

    ```php
    use Filament\Forms\Components\FileUpload;
    use Filament\Infolists\Components\ImageEntry;
    use Filament\Tables\Columns\ImageColumn;
    
    FileUpload::configureUsing(fn (FileUpload $fileUpload) => $fileUpload
        ->visibility('public'));
    
    ImageColumn::configureUsing(fn (ImageColumn $imageColumn) => $imageColumn
        ->visibility('public'));
    
    ImageEntry::configureUsing(fn (ImageEntry $imageEntry) => $imageEntry
        ->visibility('public'));
    ```
</Aside>
</Disclosure>

<Disclosure open x-show="packages.includes('panels')">
<span slot="summary">Custom themes need to be upgraded to Tailwind CSS v4</span>

Previously, custom theme CSS files contained this:

```css
@import '../../../../vendor/filament/filament/resources/css/theme.css';

@config 'tailwind.config.js';
```

Now, they should contain this:

```css
@import '../../../../vendor/filament/filament/resources/css/theme.css';

@source '../../../../app/Filament';
@source '../../../../resources/views/filament';
```

This will load Tailwind CSS. The `@source` entries tell Tailwind where to find the classes that are used in your app. You should check the `content` paths in your old `tailwind.config.js` file, and add them as `@source` entries like this. You **don't** need to include `vendor/filament` as a `@source`, but check plugins you have installed to see if they require `@source` entries.

Finally, you should use the [Tailwind upgrade tool](https://tailwindcss.com/docs/upgrade-guide#using-the-upgrade-tool) to automatically adjust your configuration files to use Tailwind v4, and install Tailwind v4 packages to replace Tailwind v3 ones:

```bash
npx @tailwindcss/upgrade
```

The `tailwind.config.js` file for your theme is no longer used, since Tailwind CSS v4 defines [configuration in CSS](https://tailwindcss.com/docs/adding-custom-styles). Any customizations you made to the `tailwind.config.js` file should be added to the CSS file.
</Disclosure>

<Disclosure open x-show="packages.includes('tables')">
<span slot="summary">Changes to table filters are deferred by default</span>

The `deferFilters()` method from Filament v3 is now the default behavior in Filament v4, so users must click a button before the filters are applied to the table. To disable this behavior, you can use the `deferFilters(false)` method.

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->deferFilters(false);
}
```

<Aside variant="tip">
    You can preserve the old default behavior across your entire app by adding the following code in the `boot()` method of a service provider like `AppServiceProvider`:

    ```php
    use Filament\Tables\Table;

    Table::configureUsing(fn (Table $table) => $table
        ->deferFilters(false));
    ```
</Aside>
</Disclosure>

<Disclosure open x-show="packages.includes('forms') || packages.includes('infolists')">
<span slot="summary">The `Grid`, `Section` and `Fieldset` layout components now do not span all columns by default</span>

In v3, the `Grid`, `Section` and `Fieldset` layout components consumed the full width of their parent grid by default. This was inconsistent with the behavior of every other component in Filament, which only consumes one column of the grid by default. The intention was to make these components easier to integrate into the default Filament resource form and infolist, which uses a two column grid out of the box.

In v4, the `Grid`, `Section` and `Fieldset` layout components now only consume one column of the grid by default. If you want them to span all columns, you can use the `columnSpanFull()` method:

```php
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

Fieldset::make()
    ->columnSpanFull()
    
Grid::make()
    ->columnSpanFull()

Section::make()
    ->columnSpanFull()
```

<Aside variant="tip">
    You can preserve the old default behavior across your entire app by adding the following code in the `boot()` method of a service provider like `AppServiceProvider`:

    ```php
    use Filament\Schemas\Components\Fieldset;
    use Filament\Schemas\Components\Grid;
    use Filament\Schemas\Components\Section;

    Fieldset::configureUsing(fn (Fieldset $fieldset) => $fieldset
        ->columnSpanFull());

    Grid::configureUsing(fn (Grid $grid) => $grid
        ->columnSpanFull());

    Section::configureUsing(fn (Section $section) => $section
        ->columnSpanFull());
    ```
</Aside>
</Disclosure>

<Disclosure open x-show="packages.includes('forms')">
<span slot="summary">The `unique()` validation rule behavior for ignoring Eloquent records</span>

In v3, the `unique()` method didn’t ignore the current form's Eloquent record when validating by default. This behavior was enabled by the `ignoreRecord: true` parameter, or by passing a custom `ignorable` record.

In v4, the `unique()` method's `ignoreRecord` parameter defaults to `true`.

If you were previously using `unique()` validation rule without the `ignoreRecord` or `ignorable` parameters, you should use `ignoreRecord: false` to disable the new behavior.

<Aside variant="tip">
    You can preserve the old default behavior across your entire app by adding the following code in the `boot()` method of a service provider like `AppServiceProvider`:

    ```php
    use Filament\Forms\Components\Field;

    Field::configureUsing(fn (Field $field) => $field
        ->uniqueValidationIgnoresRecordByDefault(false));
    ```
</Aside>
</Disclosure>

<Disclosure open x-show="packages.includes('tables')">
<span slot="summary">The `all` pagination page option is not available for tables by default</span>

The `all` pagination page method is now not available for tables by default. If you want to use it on a table, you can add it to the configuration:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->paginationPageOptions([5, 10, 25, 50, 'all']);
}
```

Be aware when using `all` as it will cause performance issues when dealing with a large number of records.

<Aside variant="tip">
    You can preserve the old default behavior across your entire app by adding the following code in the `boot()` method of a service provider like `AppServiceProvider`:

    ```php
    use Filament\Tables\Table;

    Table::configureUsing(fn (Table $table) => $table
        ->paginationPageOptions([5, 10, 25, 50, 'all']));
    ```
</Aside>
</Disclosure>

<Disclosure open x-show="packages.includes('spatie-translatable-plugin')">
<span slot="summary">The official Spatie Translatable Plugin is now deprecated</span>

Last year, the Filament team decided to hand over maintenance of the Spatie Translatable Plugin to the team at [Lara Zeus](https://larazeus.com), who are trusted developers of many Filament plugins. They’ve maintained a fork of the plugin ever since.

The official Spatie Translatable Plugin will not receive v4 support, and is now deprecated. You can use the [Lara Zeus Translatable Plugin](https://github.com/lara-zeus/spatie-translatable) as a direct replacement. The plugin is compatible with the same version of Spatie Translatable as the official plugin, and has been tested with Filament v4. It also fixes some long-standing bugs in the official plugin.

The [automated upgrade script](#running-the-automated-upgrade-script) suggests commands that uninstall the official plugin and install the Lara Zeus plugin, and replaces any references in your code to the official plugin with the Lara Zeus plugin.
</Disclosure>

### Medium-impact changes

<Disclosure x-show="packages.includes('forms') || packages.includes('infolists')">
<span slot="summary">`columnSpan()` now targets >= `lg` devices by default</span>

Similar to the `columns()` method, which accepts a number and affects >= `lg` devices by default, the `columnSpan()` method has been changed to do the same. This improves consistency between the APIs, making them easier to use and less prone to causing layout issues.

In v3, the following code might have been written. If you used `columnSpan(2)` instead of `columnSpan(['lg' => 2])`, the layout would have been a little broken on < `lg` devices:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

Section::make()
    ->columns(3)
    ->schema([
        TextInput::make()
            ->columnSpan(['lg' => 2]),
    ])
```

In v4, the `columnSpan()` affects >= `lg` devices by default, in the same way that `columns()` does, so the following code is required instead:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

Section::make()
    ->columns(3)
    ->schema([
        TextInput::make()
            ->columnSpan(2),
    ])
```

Of course, you can still continue to use an array to define how many columns a component should span at different breakpoints:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

Section::make()
    ->columns(3)
    ->schema([
        TextInput::make()
            ->columnSpan(['lg' => 3, 'xl' => 2, '2xl' => 1]),
    ])
```
</Disclosure>

<Disclosure x-show="packages.includes('forms')">
<span slot="summary">Enum field state</span>

In v3, fields that wrote to an enum attribute on a model, such as a `Select`, `CheckboxList` or `Radio` field using `options(Enum::class)`, would inconsistently return the value of the field as either the enum value or the enum instance, depending on whether or not the field was last modified by the server or by the user. This wasn’t useful, and you had to check the type of the value returned by the field to determine if it was an enum value or an enum instance.

In v4, the field state is always returned as the enum instance. This means that you can always use the enum methods on field state. If you weren’t handling the possibility of the field state being an enum instance in your code previously, you now need to do so.

The following code examples illustrate how field state may now return an enum instance:

```php
use App\Enums\Status;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

Select::make('status')
    ->options(Status::class)
    ->afterStateUpdated(function (?Status $state) {
        // `$state` is now always an instance of `Status`, or `null` if the field is empty.
    });

TextInput::make('...')
    ->afterStateUpdated(function (Get $get) {
        // `$get('status')` is now always an instance of `Status`, or `null` if the field is empty.
    });

$data = $this->form->getState();
// `$data['status']` is now always an instance of `Status`, or `null` if the field is empty.
```
</Disclosure>

<Disclosure x-show="packages.includes('panels')">
<span slot="summary">URL parameter names have changed</span>

Filament v4 has renamed some of the URL parameters that are used on resource pages, to make them cleaner in the URL and easier to remember:

- `activeRelationManager` has been renamed to `relation` on Edit / View resource pages.
- `activeTab` has been renamed to `tab` on List / Manage Relation resource pages.
- `isTableReordering` has been renamed to `reordering` on List / Manage Relation resource pages.
- `tableFilters` has been renamed to `filters` on List / Manage Relation resource pages.
- `tableGrouping` has been renamed to `grouping` on List / Manage Relation resource pages.
- `tableGroupingDirection` has been renamed to `groupingDirection` on List / Manage Relation resource pages.
- `tableSearch` has been renamed to `search` on List / Manage Relation resource pages.
- `tableSort` has been renamed to `sort` on List / Manage Relation resource pages.

To find out if you’re using this parameter in your code, try searching for `'activeRelationManager' => ` (etc.) in your code, and looking for areas where you’re using `::getUrl()` or another method of generating a URL with a parameter.
</Disclosure>

<Disclosure x-show="packages.includes('panels')">
<span slot="summary">Automatic tenancy global scoping and association</span>

When using tenancy in v3, Filament only scoped resource queries to the current tenant: to render the resource table, resolve URL parameters, and fetch global search results. There were many situations where other queries in the panel weren’t scoped by default, and the developer had to manually scope them. While this was a documented feature, it created a lot of additional work for developers.

In v4, Filament automatically scopes all queries in a panel to the current tenant, and automatically associates new records with the current tenant using model events. This means that you no longer need to manually scope queries or associate new Eloquent records in most cases. There are still some important points to consider, so the [documentation](users/tenancy#tenancy-security) has been updated to reflect this.
</Disclosure>

<Disclosure x-show="packages.includes('forms')">
<span slot="summary">The `Radio` `inline()` method behavior</span>

In v3, the `inline()` method put the radio buttons inline with each other, and also inline with the label at the same time. This is inconsistent with other components.

In v4, the `inline()` method now only puts the radio buttons inline with each other, and not with the label. If you want the radio buttons to be inline with the label, you can use the `inlineLabel()` method as well.

If you were previously using `inline()->inlineLabel(false)` to achieve the v4 behavior, you can now simply use `inline()`.

<Aside variant="tip">
    You can preserve the old default behavior across your entire app by adding the following code in the `boot()` method of a service provider like `AppServiceProvider`:

    ```php
    use Filament\Forms\Components\Radio;
    
    Radio::configureUsing(fn (Radio $radio) => $radio
        ->inlineLabel(fn (): bool => $radio->isInline()));
    ```
</Aside>
</Disclosure>

<Disclosure x-show="packages.includes('actions')">
<span slot="summary">Import and export job retries</span>

In Filament v3, import and export jobs were retries continuously for 24 hours if they failed, with no backoff between tries by default. This caused issues for some users, as there was no backoff period and the jobs could be retried too quickly, causing the queue to be flooded with continuously failing jobs.

In v4, they’re retried 3 times with a 60 second backoff between each retry.

This behavior can be customized in the [importer](import#customizing-the-import-job-retries) and [exporter](export#customizing-the-export-job-retries) classes.
</Disclosure>

### Low-impact changes

<Disclosure x-show="packages.includes('tables') || packages.includes('infolists')">
<span slot="summary">The `isSeparate` parameter of `ImageColumn::limitedRemainingText()` and `ImageEntry::limitedRemainingText()` has been removed</span>

Previously, users were able to display the number of limited images separately to an image stack using the `isSeparate` parameter. Now the parameter has been removed, and if a stack exists, the text will always be stacked on top and not separate. If the images aren’t stacked, the text will be separate.
</Disclosure>

<Disclosure x-show="packages.includes('forms')">
<span slot="summary">The `RichEditor` component's `disableGrammarly()` method has been removed</span>

The `disableGrammarly()` method has been removed from the `RichEditor` component. This method was used to disable the Grammarly browser extension acting on the editor. Since moving the underlying implementation of the editor from Trix to TipTap, we haven’t found a way to disable Grammarly on the editor.
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

If you’re overriding the `make()` method to pass default configuration to the object once it is instantiated, please note that it is recommended to instead override the `setUp()` method, which is called immediately after the object is instantiated:

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

If you’re overriding the `make()` method to pass default configuration to the object once it is instantiated, please note that it is recommended to instead override the `setUp()` method, which is called immediately after the object is instantiated:

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

If you’re overriding the `make()` method to pass default configuration to the object once it is instantiated, please note that it is recommended to instead override the `setUp()` method, which is called immediately after the object is instantiated:

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

If you’re overriding the `make()` method to pass default configuration to the object once it is instantiated, please note that it is recommended to instead override the `setUp()` method, which is called immediately after the object is instantiated:

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

<Disclosure x-show="packages.includes('tables')">
<span slot="summary">Tables now have default primary key sorting</span>

Filament v4 introduces a new default behavior for tables: they will now automatically have a primary key sort applied to their queries to ensure that records are always returned in a consistent order.

If your table doesn't have a primary key, or you want to disable this behavior, you can do so by using the `defaultKeySort(false)` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->defaultKeySort(false);
}
```

<Aside variant="tip">
    You can preserve the old default behavior across your entire app by adding the following code in the `boot()` method of a service provider like `AppServiceProvider`:

    ```php
    use Filament\Tables\Table;

    Table::configureUsing(fn (Table $table) => $table
        ->defaultKeySort(false));
    ```
</Aside>
</Disclosure>

<Disclosure x-show="packages.includes('panels')">
<span slot="summary">Overriding the `can*()` authorization methods on a `Resource`, `RelationManager` or `ManageRelatedRecords` class</span>

Although these methods, such as `canCreate()`, `canViewAny()` and `canDelete()`weren’t documented, if you’re overriding those to provide custom authorization logic in v3, you should be aware that they aren’t always called in v4. The authorization logic has been improved to properly support [policy response objects](https://laravel.com/docs/authorization#policy-responses), and these methods were too simple as they are just able to return booleans.

If you can make the authorization customization inside the policy of the model instead, you should do that. If you need to customize the authorization logic in the resource or relation manager class, you should override the `get*AuthorizationResponse()` methods instead, such as `getCreateAuthorizationResponse()`, `getViewAnyAuthorizationResponse()` and `getDeleteAuthorizationResponse()`. These methods are called when the authorization logic is executed, and they return a [policy response object](https://laravel.com/docs/authorization#policy-responses). If you remove the override for the `can*()` methods, the `get*AuthorizationResponse()` methods will be used to determine the authorization response boolean, so you don't have to maintain the logic twice.
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

<Disclosure x-show="packages.includes('actions')">
<span slot="summary">Khmer translations</span>

The Khmer translations have been moved from `kh` to `km`, which appears to be the more commonly used language code for the language within the Laravel community.
</Disclosure>

<Disclosure x-show="packages.includes('tables')">
<span slot="summary">Some deprecated table configuration methods have been removed</span>

Before Filament v3, tables could be configured by overriding methods on the Livewire component class, instead of modifying the `$table` configuration object. This was deprecated in v3, and removed in v4. If you were using any of the following methods, you should remove them from your Livewire component class, and use their corresponding `$table` configuration methods instead:

- `getTableRecordUrlUsing()` should be replaced with `$table->recordUrl()`
- `getTableRecordClassesUsing()` should be replaced with `$table->recordClasses()`
- `getTableRecordActionUsing()` should be replaced with `$table->recordAction()`
- `isTableRecordSelectable()` should be replaced with `$table->checkIfRecordIsSelectableUsing()`
</Disclosure>

</div>
