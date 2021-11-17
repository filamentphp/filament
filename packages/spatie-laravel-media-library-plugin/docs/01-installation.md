---
title: Installation
---

Install the plugin with Composer:

```bash
composer require filament/spatie-laravel-media-library-plugin
```

> Please note that this package is incompatible with `filament/filament` v1, until v2 is released in late 2021. This is due to namespacing collisions.

You're now ready to start using the [form components](form-components) and [table columns](table-columns)!

## Upgrade Guide

To upgrade the package to the latest version, you must run:

```bash
composer update
php artisan config:clear
php artisan view:clear
```
