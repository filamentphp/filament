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
composer require filament/spatie-laravel-media-library-plugin:"^2.0"
```

If you haven't already done so, you need to publish the migration to create the media table:

```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
```

Run the migrations:

```bash
php artisan migrate
```

You must also [prepare your Eloquent model](https://spatie.be/docs/laravel-medialibrary/basic-usage/preparing-your-model) for attaching media.

> For more information, check out [Spatie's documentation](https://spatie.be/docs/laravel-medialibrary).

You're now ready to start using the [form components](form-components) and [table columns](table-columns)!

## Upgrading

To upgrade the package to the latest version, you must run:

```bash
composer update
```
