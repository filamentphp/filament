---
title: Upgrading from v2.x
---

> If you see anything missing from this guide, please do not hesitate to [make a pull request](https://github.com/filamentphp/filament/edit/3.x/packages/tables/docs/13-upgrade-guide.md) to our repository! Any help is appreciated!

## New requirements

- Laravel v10.0+
- Livewire v3.0+

Please upgrade Filament before upgrading to Livewire v3. Instructions on how to upgrade Livewire can be found [here](https://livewire.laravel.com/docs/upgrading).

> **Livewire v3 is recently released!**<br />
> The Livewire team have done a great job in making it stable, but it was a complete rewrite of Livewire v2. You may encounter issues, so we recommend testing your application thoroughly before using Filament v3 in production.

## Upgrading automatically

The easiest way to upgrade your app is to run the automated upgrade script. This script will automatically upgrade your application to the latest version of Filament, and make changes to your code which handle most breaking changes.

```bash
composer require filament/upgrade:"^3.1" -W --dev
vendor/bin/filament-v3
```

Make sure to carefully follow the instructions, and review the changes made by the script. You may need to make some manual changes to your code afterwards, but the script should handle most of the repetitive work for you.

Finally, you must run `php artisan filament:install` to finalize the Filament v3 installation. This command must be run for all new Filament projects.

You can now `composer remove filament/upgrade` as you don't need it anymore.

> Some plugins you're using may not be available in v3 just yet. You could temporarily remove them from your `composer.json` file until they've been upgraded, replace them with a similar plugins that are v3-compatible, wait for the plugins to be upgraded before upgrading your app, or even write PRs to help the authors upgrade them.

## Upgrading manually

After upgrading the dependency via Composer, you should execute `php artisan filament:upgrade` in order to clear any Laravel caches and publish the new frontend assets.

### High-impact changes

#### Config file renamed and combined with other Filament packages

Only one config file is now used for all Filament packages. Most configuration has been moved into other parts of the codebase, and little remains. You should use the v3 documentation as a reference when replace the configuration options you did modify. To publish the new configuration file and remove the old one, run:

```bash
php artisan vendor:publish --tag=filament-config --force
rm config/tables.php
```

#### `TABLES_FILESYSTEM_DRIVER` .env variable

The `TABLES_FILESYSTEM_DRIVER` .env variable has been renamed to `FILAMENT_FILESYSTEM_DISK`. This is to make it more consistent with Laravel, as Laravel v9 introduced this change as well. Please ensure that you update your .env files accordingly, and don't forget production!

#### New `@filamentScripts` and `@filamentStyles` Blade directives

The `@filamentScripts` and `@filamentStyles` Blade directives must be added to your Blade layout file/s. Since Livewire v3 no longer uses similar directives, you can replace `@livewireScripts` with `@filamentScripts`  and `@livewireStyles` with `@filamentStyles`.

#### CSS file removed

The CSS file for form components, `module.esm.css`, has been removed. Check `resources/css/app.css`. That CSS is now automatically loaded by `@filamentStyles`.

#### JavaScript files removed

You no longer need to import the `FormsAlpinePlugin` in your JavaScript files. Alpine plugins are now automatically loaded by `@filamentScripts`.

#### Heroicons have been updated to v2

The Heroicons library has been updated to v2. This means that any icons you use in your app may have changed names. You can find a list of changes [here](https://github.com/tailwindlabs/heroicons/releases/tag/v2.0.0).

### Medium-impact changes

#### Secondary color

Filament v2 had a `secondary` color for many components which was gray. All references to `secondary` should be replaced with `gray` to preserve the same appearance. This frees `secondary` to be registered to a new custom color of your choice.

#### `BadgeColumn::enum()` removed

You can use a `formatStateUsing()` function to transform text.

#### Enum classes moved

The following enum classes have moved:

- `Filament\Tables\Actions\Position` has moved to `Filament\Tables\Enums\ActionsPosition`.
- `Filament\Tables\Actions\RecordCheckboxPosition` has moved to `Filament\Tables\Enums\RecordCheckboxPosition`.
- `Filament\Tables\Filters\Layout` has moved to `Filament\Tables\Enums\FiltersLayout`.
