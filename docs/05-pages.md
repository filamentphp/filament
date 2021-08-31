---
title: Custom Pages
---

Filament allows you to create completely custom pages for the admin panel.

To create a new page, you can use:

```bash
php artisan make:filament-page Settings
```

This command will create two files - a page class in the `/Pages` directory of the Filament directory, and a view in the `/pages` directory of the Filament views directory.

Page classes are essentially [Laravel Livewire](https://laravel-livewire.com) components with custom integration utilities for use with Filament.

## Authorization

You may create roles for users of Filament that allow them to access specific pages. You may create a `Manager` role using:

```php
php artisan make:filament-role Manager
```

Administrators will now be able to assign this role to any Filament user using the admin panel.

To only allow users with the `Manager` role to access this page, declare so in the static `authorization()` method:

```php
use App\Filament\Roles;

public static function authorization()
{
    return [
        Roles\Manager::allow(),
    ];
}
```

You may authorize as many roles as you wish.

> Please note: administrators will always have full access to every page in your admin panel.

You may want to only deny users with the `Manager` role from accessing this page. To do this, you may use the static `deny()` method instead:

```php
use App\Filament\Roles;

public static function authorization()
{
    return [
        Roles\Manager::deny(),
    ];
}
```

## Customization

Filament will automatically generate a title, navigation label and URL (slug) for your page based on its name. You may override it using static properties of your page class:

```php
public static $label = 'Custom Navigation Label';

public static $slug = 'custom-url-slug';

public static $title = 'Custom Page Title';
```
