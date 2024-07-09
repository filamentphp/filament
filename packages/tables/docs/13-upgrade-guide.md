---
title: Upgrading from v4.x
---

> If you see anything missing from this guide, please do not hesitate to [make a pull request](https://github.com/filamentphp/filament/edit/4.x/packages/tables/docs/13-upgrade-guide.md) to our repository! Any help is appreciated!

## New requirements

- Tailwind CSS v4.0+

## Upgrading automatically

The easiest way to upgrade your app is to run the automated upgrade script. This script will automatically upgrade your application to the latest version of Filament, and make changes to your code which handle most breaking changes.

```bash
composer require filament/upgrade:"^4.0" -W --dev
vendor/bin/filament-v4
```

Make sure to carefully follow the instructions, and review the changes made by the script. You may need to make some manual changes to your code afterwards, but the script should handle most of the repetitive work for you.

Finally, you must run `php artisan filament:install` to finalize the Filament v4 installation. This command must be run for all new Filament projects.

You can now `composer remove filament/upgrade` as you don't need it anymore.

> Some plugins you're using may not be available in v4 just yet. You could temporarily remove them from your `composer.json` file until they've been upgraded, replace them with a similar plugins that are v4-compatible, wait for the plugins to be upgraded before upgrading your app, or even write PRs to help the authors upgrade them.

## Upgrading manually

After upgrading the dependency via Composer, you should execute `php artisan filament:upgrade` in order to clear any Laravel caches and publish the new frontend assets.

### High-impact changes

#### The `all` pagination page option is not available for tables by default

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

#### The `FILAMENT_FILESYSTEM_DISK` environment variable

Please see the [Panel Builder](../panels/upgrade-guide#the-filament_filesystem_disk-environment-variable) upgrade guide for information about this change.

### Medium-impact changes

### Low-impact changes

#### Overriding the `Column::make()` or `Constraint::make()` methods

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

#### The European Portuguese translations

The European Portuguese translations have been moved from `pt_PT` to `pt`, which appears to be the more commonly used language code for the language within the Laravel community.

#### Nepalese translations

The Nepalese translations have been moved from `np` to `ne`, which appears to be the more commonly used language code for the language within the Laravel community.
