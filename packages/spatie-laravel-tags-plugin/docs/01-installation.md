---
title: Installation
---

## Requirements

Filament has a few requirements to run:

- PHP 8.0+
- Laravel v8.0+
- Livewire v2.0+

This plugin is compatible with other Filament v2.x packages.

## Installation

Install the plugin with Composer:

```bash
composer require filament/spatie-laravel-tags-plugin:"^2.0"
```

If you haven't already done so, you need to publish the migration to create the tags table:

```bash
php artisan vendor:publish --provider="Spatie\Tags\TagsServiceProvider" --tag="tags-migrations"
```

Run the migrations:

```bash
php artisan migrate
```

You must also [prepare your Eloquent model](https://spatie.be/docs/laravel-tags/basic-usage/using-tags) for attaching tags.

> For more information, check out [Spatie's documentation](https://spatie.be/docs/laravel-tags).

You're now ready to start using the [form components](form-components) and [table columns](table-columns)!

## Upgrading

To upgrade the package to the latest version, you must run:

```bash
composer update
php artisan filament:upgrade
```

We recommend adding the `filament:upgrade` command to your `composer.json`'s `post-update-cmd` to run it automatically:

```json
"post-update-cmd": [
    // ...
    "@php artisan filament:upgrade"
],
```
