---
title: Upgrading from v2.x
---

> If you see anything missing from this guide, please do not hesitate to [make a pull request](https://github.com/filamentphp/filament/edit/3.x/packages/forms/docs/10-upgrade-guide.md) to our repository! Any help is appreciated!

## New requirements

- Laravel v9.0+
- Livewire v3.0+

## Upgrading automatically

The easiest way to upgrade your app is to run the automated upgrade script. This script will automatically upgrade your application to the latest version of Filament, and make changes to your code which handle most breaking changes.

```bash
composer require filament/upgrade:"^3.0" --dev
vendor/bin/filament-v3
```

Make sure to carefully follow the instructions, and review the changes made by the script. You may need to make some manual changes to your code afterwards, but the script should handle most of the repetitive work for you.

Finally, you must run `php artisan filament:install` to finalize the Filament v3 installation. This command must be run for all new Filament projects.

You can now `composer remove filament/upgrade` as you don't need it any more.

> Some plugins you're using may not be available in v3 just yet. You could temporarily remove them from your `composer.json` file until they've been upgraded, replace them with a similar plugins that are v3-compatible, wait for the plugins to be upgraded before upgrading your app, or even write PRs to help the authors upgrade them.

## Upgrading manually

### High impact changes

#### Config file renamed and combined with other Filament packages

Only one config file is now used for all Filament packages. Most configuration has been moved into other parts of the codebase, and little remains. You should use the v3 documentation as a reference when replace the configuration options you did modify. To publish the new configuration file and remove the old one, run:

```bash
php artisan vendor:publish --tag=filament-config --force
rm config/forms.php
```

#### `FORMS_FILESYSTEM_DRIVER` .env variable

The `FORMS_FILESYSTEM_DRIVER` .env variable has been renamed to `FILAMENT_FILESYSTEM_DISK`. This is to make it more consistent with Laravel, as Laravel 9 introduced this change as well. Please ensure that you update your .env files accordingly, and don't forget production!

#### New `@filamentScripts` and `@filamentStyles` Blade directives

The `@filamentScripts` and `@filamentStyles` Blade directives must be added to your Blade layout file/s. You can insert `@filamentScripts` after `@livewireScripts` and `@filamentStyles` after `@livewireStyles`.

#### CSS file renamed

The CSS file for form components has been renamed from `module.esm.css` to `index.css`. You should update any references to this file in your code - check `resources/css/app.css`.

#### JavaScript assets

You no longer need to import the `FormsAlpinePlugin` in your JavaScript files. Alpine plugins are now automatically loaded by `@filamentScripts`.

#### Heroicons have been updated to v2

The Heroicons library has been updated to v2. This means that any icons you use in your app may have changed names. You can find a list of changes [here](https://github.com/tailwindlabs/heroicons/releases/tag/v2.0.0).

### Medium impact changes

#### Date-time pickers

The date-time picker form field now uses the browser's native date picker by default. It usually has a better UX than the old date picker, but you may notice features missing, bad browser compatibility, or behavioural bugs. If you want to revert to the old date picker, you can use the `native(false)` method:

```php
use Filament\Forms\Components\DateTimePicker;

DateTimePicker::make('published_at')
    ->native(false)
```

#### Secondary color

Filament v2 had a `secondary` color for many components which was gray. Now, you can define a non-gray `secondary` color alongside `primary`. All references to `secondary` should be replaced with `gray` to preserve the same appearance.

#### `$get` and `$set` closure parameters

`$get` and `$set` parameters now use a type of either `\Filament\Forms\Get` or `\Filament\Forms\Set` instead of `\Closure`. This allows for better IDE autocomplete support of the parameters of each function.

An easy way to upgrade your code quickly is to find and replace:

- `Closure $get` to `\Filament\Forms\Get $get`
- `Closure $set` to `\Filament\Forms\Set $set`
