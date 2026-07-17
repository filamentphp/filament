---
title: Upgrade guide
---
import Aside from "@components/Aside.astro"

<Aside variant="info">
    If you see anything missing from this guide, please don’t hesitate to [make a pull request](https://github.com/filamentphp/filament/edit/6.x/docs/14-upgrade-guide.md) to our repository! Any help is appreciated!
</Aside>

## Running the automated upgrade script

<Aside variant="info">
    Some plugins you're using may not be available in v6 just yet. You could temporarily remove them from your `composer.json` file until they've been upgraded, replace them with a similar plugins that are v6-compatible, wait for the plugins to be upgraded before upgrading your app, or even write PRs to help the authors upgrade them.
</Aside>

The first step to upgrade your Filament app is to run the automated upgrade script. This script will automatically upgrade your application to the latest version of Filament and make changes to your code, which handles breaking changes:

```bash
composer require filament/upgrade:"^6.0" -W --dev

vendor/bin/filament-v6

# Run the commands output by the upgrade script, they are unique to your app
composer require filament/filament:"^6.0" -W --no-update
composer update
```

<Aside variant="warning">
    When using Windows PowerShell to install Filament, you may need to run the command below, since it ignores `^` characters in version constraints:

    ```bash
    composer require filament/upgrade:"~6.0" -W --dev

    vendor/bin/filament-v6

    # Run the commands output by the upgrade script, they are unique to your app
    composer require filament/filament:"~6.0" -W --no-update
    composer update
    ```
</Aside>

Make sure to carefully follow the instructions, and review the changes made by the script. You may need to make some manual changes to your code afterwards, but the script should handle most of the repetitive work for you.

You can now `composer remove filament/upgrade --dev` as you don't need it anymore.

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
</Checkboxes>

### Medium-impact changes

<Disclosure open x-show="packages.includes('actions')">
<span slot="summary">Exports now prevent CSV/XLSX formula injection by default</span>

To protect people who open exported files in spreadsheet software, every `ExportColumn` now neutralizes CSV/XLSX formula injection by default. Previously this check was opt-in via `preventFormulaInjection()`.

Any exported string value that begins with a formula-triggering character (`=`, `+`, `-`, `@`, a tab, or a carriage return) is now prefixed with a single quote (`'`) so that spreadsheet software treats it as plain text. Purely numeric strings such as `-5` are left unchanged, since spreadsheets interpret them as numbers rather than formulas.

This only changes the output for string values that begin with one of those characters and are not purely numeric — for example, a phone number stored as a string like `+44 1234 567890` will now be exported as `'+44 1234 567890`. If a specific column exports trusted data where this transformation is unwanted, disable the protection for that column:

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('phone')
    ->preventFormulaInjection(false)
```

To restore the previous behavior for every export column in your application, call `configureUsing()` in a service provider's `boot()` method. This reintroduces the formula injection risk across all your exports, so prefer disabling it only on the individual columns that need it:

```php
use Filament\Actions\Exports\ExportColumn;

ExportColumn::configureUsing(function (ExportColumn $column): void {
    $column->preventFormulaInjection(false);
});
```

See the [export documentation](../actions/export#csv-formula-injection) for more details.
</Disclosure>

<Disclosure open x-show="packages.includes('actions')">
<span slot="summary">Import failure CSVs now prevent formula injection by default</span>

When rows fail validation during an import, Filament compiles them into a downloadable failure CSV. To protect people who open that file in spreadsheet software, formula injection is now neutralized in the failure CSV by default. Previously this check was opt-in via `preventFormulaInjection()`.

Any cell that begins with a formula-triggering character (`=`, `+`, `-`, `@`, a tab, or a carriage return) is now prefixed with a single quote (`'`). Purely numeric strings such as `-5` are left unchanged, so the failure CSV can still be corrected and re-uploaded without corrupting legitimate data.

This only changes the failure CSV output for values that begin with one of those characters and are not purely numeric — for example, a phone number stored as a string like `+44 1234 567890` will now appear as `'+44 1234 567890`. If a specific importer processes trusted files where this transformation is unwanted, disable it by redeclaring the property on your importer class:

```php
use Filament\Actions\Imports\Importer;

class ProductImporter extends Importer
{
    protected static bool $shouldPreventFormulaInjection = false;
}
```

To restore the previous behavior for every importer in your application, call `preventFormulaInjection(false)` in a service provider's `boot()` method:

```php
use Filament\Actions\Imports\Importer;

Importer::preventFormulaInjection(false);
```

See the [import documentation](../actions/import#csv-formula-injection) for more details.
</Disclosure>

<Disclosure open x-show="packages.includes('support')">
<span slot="summary">Livewire file uploads are now restricted to schema components by default</span>

Every Livewire component that uses the `InteractsWithSchemas` trait exposes Livewire's `_startUpload` and `_finishUpload` RPC methods, which by default accept uploads to any property name — even ones that are not real upload fields. To close this, Filament now restricts these uploads by default: `_startUpload` and `_finishUpload` abort with a `403` unless the target property maps to a `FileUpload` field (or any field that supports file attachments) registered in one of the component's schemas. Previously this was opt-in via the `RestrictsFileUploadsToSchemaComponents` trait.

Legitimate uploads from your schema's fields are unaffected. This only changes behavior for components that accept uploads to a property that is **not** a schema field — for example, a custom Livewire component that wires `wire:model` for a file upload to a property outside its Filament schema.

The `RestrictsFileUploadsToSchemaComponents` trait has been removed, since its behavior is now the default. The upgrade command removes any usage of it from your components automatically. If a component legitimately needs to accept uploads to a non-schema property, opt out by overriding the method:

```php
public function shouldRestrictFileUploadsToSchemaComponents(): bool
{
    return false;
}
```

See the [security documentation](../advanced/security#restricting-livewire-file-uploads-to-schema-components) for more details.
</Disclosure>

### Low-impact changes

<Disclosure open x-show="packages.includes('support')">
<span slot="summary">The modal content is now wrapped in a `fi-modal-window-scroll` element when using a sticky header or footer</span>

To fix an issue where modals with a sticky header or footer could overflow the viewport, the internal DOM structure of the modal component has changed. When a modal has a sticky header (`stickyHeader()`) or a sticky footer (`stickyFooter()`), and is not a slide-over or a screen-width modal, its heading, content, and footer are now wrapped in a new scrollable `fi-modal-window-scroll` element:

```html
<div class="fi-modal-window">
    <div class="fi-modal-window-scroll">
        <div class="fi-modal-header"><!-- ... --></div>
        <div class="fi-modal-content"><!-- ... --></div>
        <div class="fi-modal-footer"><!-- ... --></div>
    </div>
</div>
```

If you have written custom CSS or a theme that targets `fi-modal-header`, `fi-modal-content`, or `fi-modal-footer` as direct children of `fi-modal-window`, you should update your selectors to account for the new `fi-modal-window-scroll` wrapper.
</Disclosure>

<Disclosure open x-show="packages.includes('support')">
<span slot="summary">The `ryangjchandler/blade-capture-directive` Composer dependency has been removed</span>

Filament no longer depends on the [`ryangjchandler/blade-capture-directive`](https://github.com/ryangjchandler/blade-capture-directive) package, since Filament registers its own `@capture` and `@endcapture` Blade directives with the same functionality. If you use these directives in your own Blade views, they will continue to work without any changes.

If you use `@capture` outside of Filament and want to ensure that the directives remain available even if you remove Filament from your project, you can install the package yourself:

```bash
composer require ryangjchandler/blade-capture-directive
```

If you are a plugin developer and you register the package's service provider in your test cases, for example in the `getPackageProviders()` method of a [Testbench](https://packages.tools/testbench) test case, you should remove `RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider` from the list.
</Disclosure>

</div>
