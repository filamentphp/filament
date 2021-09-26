---
title: Getting Started
---

Filament is a content management framework for rapidly building a beautiful administration interface designed for humans.

> Filament requires Laravel 8.x or higher, and PHP 7.4 or higher.

Installation:

```bash
composer require filament/filament
php artisan migrate
```

Create an administrator account for your admin panel by running

```bash
php artisan make:filament-user
```

and answering the input prompts. Administrators have access to all areas of Filament, and are able to manage other users.

Once you have a user account, you can sign in to the admin panel by visiting `/admin` in your browser.

To start building your admin panel, [create a resource](resources).

## Configuration

If you'd like to expose advanced configuration options for Filament, you may publish its configuration file:

```bash
php artisan vendor:publish --tag=filament-config
```

> If you have published the configuration file for Filament, please ensure that you republish it when you upgrade.

## Users

By default, Filament includes its own authentication guard and users table that is completely separate from your app's users table. This enables you to get up and running with Filament at record speed.

Some projects may choose to allow their app users access to Filament. In this case, they may customize the auth guard that Filament uses by [configuring](#configuration) `auth.guard` to the name of your default guard, typically `web`.

The next step is to prepare your `User` model for use with Filament. Implement the `Filament\Models\Contracts\FilamentUser` interface, and apply the `Filament\Models\Concerns\IsFilamentUser` trait. These provide Filament with an API that it can use to interact with your existing user data:

```php
<?php

namespace App\Models;

use Filament\Models\Concerns\IsFilamentUser;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    use IsFilamentUser;

    // ...
}
```

By default, all users will be able to sign in to Filament. To change this, you may set the static `$filamentUserColumn` property on your custom user class to the name of a boolean column in your database:

```php
public static $filamentUserColumn = 'is_filament_user'; // The name of a boolean column in your database.
```

Alternatively, you may override the `canAccessFilament()` on your custom user class, returning a boolean:

```php
public function canAccessFilament()
{
    return $this->group === 'Filament Users';
}
```

Filament implements authorization features in its default users table. Admin users are able to access all areas of Filament, and manage other users. Users may have roles, which are associated with certain permissions in your admin panel.

To configure columns to granting users admin permissions and roles, you may set the static `$filamentAdminColumn` and `$filamentRolesColumn` properties on your class:

```php
public static $filamentAdminColumn = 'is_filament_admin'; // The name of a boolean column in your database.

public static $filamentRolesColumn = 'filament_roles'; // The name of a JSON column in your database.
```

To disable roles and admin features, just emit these properties from your class.

Alternatively, you may specify custom logic for calculating if a user has admin permissions by overriding the `isFilamentAdmin()` method:

```php
public function isFilamentAdmin()
{
    return $this->email === 'dan@danharrin.com';
}
```

Finally, for the correct reset password URL to be sent, you should implement a `sendPasswordResetNotification($token)` function. You can use the `Filament\Models\Concerns\SendsFilamentPasswordResetNotification` trait to add this functionality to your User model, fully integrated with Filament:

```php
<?php

namespace App\Models;

use Filament\Models\Concerns\IsFilamentUser;
use Filament\Models\Concerns\SendsFilamentPasswordResetNotification;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    use IsFilamentUser;
    use SendsFilamentPasswordResetNotification;

    // ...
}
```

### Disabling the Default Migrations

You may wish to prevent the migration for the default users table from being registered. You may do this by calling:

```php
use Filament\Filament;

Filament::ignoreMigrations();
```

from the `register()` method of your `AppServiceProvider`.

## Stubs

Filament commands use stubs as templates when creating new files in your project. You may customize these stubs by publishing them to your app:

```bash
php artisan vendor:publish --tag=filament-stubs
```

> If you have published the stubs for Filament, please ensure that you republish them when you upgrade.

## Upgrade Guide

To upgrade Filament to the latest version, you may run:

```bash
php artisan filament:upgrade
```

or the following commands manually:

```bash
composer update
php artisan migrate
php artisan livewire:discover
php artisan route:clear
php artisan view:clear
```
