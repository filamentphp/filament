---
title: Installation
---

## Requirements

Filament has a few requirements to run:

- PHP 8.1+
- Laravel v9.0+
- Livewire v3.0+

> **Livewire v3 is still in beta**
> While they will attempt to keep breaking changes to a minimum, they could still happen. Filament v3 is also not stable because of that. Therefore, we recommend testing your application thoroughly before using Filament v3 in production.

This package is compatible with other Filament v3.x products. The [form builder](/docs/forms), [table builder](/docs/tables) and [notifications](/docs/notifications) come pre-installed with the package, and no other installation steps are required to use them within a panel.

## Installation

To get started with a panel, you can install it using the commands:

```bash
composer require filament/filament:"^3.0@beta"
php artisan filament:install --panels
```

This will create a file at `app/Providers/Filament/AdminPanelProvider.php`. Since this is a [Laravel service provider](https://laravel.com/docs/providers), it needs to be registered in `config/app.php`. Filament will attempt to do this for you, but if you get an error while trying to access your panel then this process has probably failed. You can manually register the service provider by adding it to the `providers` array.

If you don't have one, you may create a new user account using:

```bash
php artisan make:filament-user
```

Visit your app at `/admin` to sign in, and you're now ready to start [building your app](getting-started)!

## Deploying to production

By default, all `App\Models\User`s can access Filament locally. To allow them to access Filament in production, you must take a few extra steps to ensure that only the correct users have access to the app.

Please see the [Users page](users#authorizing-access-to-the-admin-panel).

If you don't complete these steps, there will be a 403 error when you try to access the app in production.

## Publishing configuration

If you wish, you may publish the configuration of the package using:

```bash
php artisan vendor:publish --tag=filament-config
```

## Publishing translations

If you wish to translate the package, you may publish the language files using:

```bash
php artisan vendor:publish --tag=filament-panels-translations
```

Since this package depends on other Filament packages, you may wish to translate those as well:

```bash
php artisan vendor:publish --tag=filament-actions-translations
php artisan vendor:publish --tag=filament-forms-translations
php artisan vendor:publish --tag=filament-notifications-translations
php artisan vendor:publish --tag=filament-tables-translations
php artisan vendor:publish --tag=filament-translations
```

## Upgrading

To upgrade the package to the latest version, you must run:

```bash
composer update
php artisan filament:upgrade
```

We recommend adding the `filament:upgrade` command to your `composer.json`'s `post-autoload-dump` to run it automatically:

```json
"post-autoload-dump": [
    // ...
    "@php artisan filament:upgrade"
],
```

This should be done during the `filament:install` process, but double check it's been done.
