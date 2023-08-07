---
title: Upgrading from v2.x
---

> If you see anything missing from this guide, please do not hesitate to [make a pull request](https://github.com/filamentphp/filament/edit/3.x/packages/panels/docs/14-upgrade-guide.md) to our repository! Any help is appreciated!

## New requirements

- Laravel v9.0+
- Livewire v3.0+

Please upgrade Filament before upgrading to Livewire v3. Instructions on how to upgrade Livewire can be found [here](https://livewire.laravel.com/docs/upgrading).

> **Livewire v3 is still in beta!**<br>
> Although breaking changes should be minimal, we recommend testing your application thoroughly before using Filament v3 in production.

## Upgrading automatically

Since Livewire v3 is still in beta, set the `minimum-stability` in your `composer.json` to `dev`:

```json
"minimum-stability": "dev",
```

The easiest way to upgrade your app is to run the automated upgrade script. This script will automatically upgrade your application to the latest version of Filament and make changes to your code which handle most breaking changes.

```bash
composer require filament/upgrade:"^3.0-stable" -W --dev

vendor/bin/filament-v3
```

Make sure to carefully follow the instructions, and review the changes made by the script. You may need to make some manual changes to your code afterwards, but the script should handle most of the repetitive work for you.

A new `app/Providers/Filament/*PanelProvider.php` file will be created, and the configuration from your old `config/filament.php` file should be copied. Since this is a [Laravel service provider](https://laravel.com/docs/providers), it needs to be registered in `config/app.php`. Filament will attempt to do this for you, but if you get an error while trying to access your panel then this process has probably failed. You can manually register the service provider by adding it to the `providers` array.

Finally, you must run `php artisan filament:install` to finalize the Filament v3 installation. This command must be run for all new Filament projects.

You can now `composer remove filament/upgrade` as you don't need it any more.

> Some plugins you're using may not be available in v3 just yet. You could temporarily remove them from your `composer.json` file until they've been upgraded, replace them with a similar plugins that are v3-compatible, wait for the plugins to be upgraded before upgrading your app, or even write PRs to help the authors upgrade them.

## Upgrading manually

Since Livewire v3 is still in beta, set the `minimum-stability` in your `composer.json` to `dev`:

```json
"minimum-stability": "dev",
```

### Low impact changes

#### Action execution with forms

In v2, you were able to externalise action logic to a function, like so:
```php
 Action::make('import_data')
    ->action('importData')
    ->form([
        FileUpload::make('file'),
    ])
```
In v3, if your Action uses a form like the above example, you'll need to change how your action logic is called. Otherwise, the function will be called when the action modal attempts to load, which may cause errors. 

It is recommended to place your logic in a closure instead. See the [Action documentation](https://filamentphp.com/docs/3.x/actions/overview) for more information.

If you absolutely need the action logic to be in a separate function, you can use `Closure::fromCallable()`:
```php
Action::make('import_data')
    ->action(Closure::fromCallable([$this, 'importData']))
```
