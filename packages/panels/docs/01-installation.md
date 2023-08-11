---
title: Installation
---

## Requirements

Filament requires the following to run:

- PHP 8.1+
- Laravel v10.0+
- Livewire v3.0+

> **Livewire v3 is still in beta!**<br />
> Although breaking changes should be minimal, we recommend testing your application thoroughly before using Filament v3 in production.

## Installation

> If you are upgrading from Filament v2, please review the [upgrade guide](upgrade-guide).

Since Livewire v3 is still in beta, set the `minimum-stability` in your `composer.json` to `dev`:

```json
"minimum-stability": "dev",
```

Install the Filament Panel Builder by running the following commands in your Laravel project directory:

```bash
composer require filament/filament:"^3.0-stable" -W

php artisan filament:install --panels
```

This will create and register a new [Laravel service provider](https://laravel.com/docs/providers) called `app/Providers/Filament/AdminPanelProvider.php`.

> If you get an error when accessing your panel, check that the service provider was registered in your `config/app.php`. If not, you should manually add it to the `providers` array.

## Create a user
You can create a new user account with the following command:

```bash
php artisan make:filament-user
```

Open `/admin` in your web browser, sign in, and start building your app!

Not sure where to start? Review the [Getting Started guide](getting-started) to learn how to build a complete Filament admin panel.

## Using other Filament packages
The Filament Panel Builder pre-installs the [Form Builder](/docs/forms), [Table Builder](/docs/tables), [Notifications](/docs/notifications), [Actions](/docs/actions), [Infolists](/docs/infolists), and [Widgets](/docs/widgets) packages. No other installation steps are required to use these packages within a panel.

## Deploying to production

By default, all `User` models can access Filament locally. However, when deploying to production, you must update your `App\Models\User.php` to implement the `FilamentUser` contract — ensuring that only the correct users can access your panel:

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    // ...

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
    }
}
```

> If you don't complete these steps, a 403 Forbidden error will be returned when accessing the app in production.

Learn more about [users](users).

## Publishing configuration

You can publish the Filament package configuration (if needed) using the following command:

```bash
php artisan vendor:publish --tag=filament-config
```

## Publishing translations

You can publish the language files for translations (if needed) with the following command:

```bash
php artisan vendor:publish --tag=filament-panels-translations
```

Since this package depends on other Filament packages, you can publish the language files for those packages with the following commands:

```bash
php artisan vendor:publish --tag=filament-actions-translations

php artisan vendor:publish --tag=filament-forms-translations

php artisan vendor:publish --tag=filament-notifications-translations

php artisan vendor:publish --tag=filament-tables-translations

php artisan vendor:publish --tag=filament-translations
```

## Upgrading

> Upgrading from Filament v2? Please review the [upgrade guide](upgrade-guide).

Filament automatically upgrades to the latest non-breaking version when you run `composer update`. If you notice that Filament is not upgrading automatically, ensure that the `filament:upgrade` command is present in your `composer.json`:

```json
"post-autoload-dump": [
    // ...
    "@php artisan filament:upgrade"
],
```

If you prefer not to use automatic upgrades, remove the `filament:upgrade` command from your `composer.json` and run the following commands:

```bash
composer update

php artisan filament:upgrade
```
