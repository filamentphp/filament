---
title: Upgrading from v2.x
---

> If you see anything missing from this guide, please do not hesitate to [make a pull request](https://github.com/filamentphp/filament/edit/3.x/packages/actions/docs/10-upgrade-guide.md) to our repository! Any help is appreciated!

## New requirements

- Laravel v9.0+
- Livewire v3.0+

Please upgrade Filament before upgrading to Livewire v3. Instructions on how to upgrade Livewire can be found [here](https://livewire.laravel.com/docs/upgrading).

> **Livewire v3 is recently released!**<br />
> The Livewire team have done a great job in making it stable, but it was a complete rewrite of Livewire v2. You may encounter issues, so we recommend testing your application thoroughly before using Filament v3 in production.

## Upgrading automatically

The easiest way to upgrade your app is to run the automated upgrade script. This script will automatically upgrade your application to the latest version of Filament and make changes to your code, which handles most breaking changes.

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

### Low-impact changes

#### Action execution with forms

In v2, if you passed a string to the `action()` function, and used a modal, the method with that name on the class would be run when the modal was submitted:

```php
 Action::make('import_data')
    ->action('importData')
    ->form([
        FileUpload::make('file'),
    ])
```

In v3, passing a string to the `action()` hard-wires it to run that action when the trigger button is clicked, so it does not open a modal.

It is recommended to place your logic in a closure function instead. See the [documentation](overview) for more information.

One easy alternative is using `Closure::fromCallable()`:

```php
Action::make('import_data')
    ->action(Closure::fromCallable([$this, 'importData']))
```
