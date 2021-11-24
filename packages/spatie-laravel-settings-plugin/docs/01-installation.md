---
title: Installation
---

## Requirements

Filament has a few requirements to run:

- PHP 8.0+
- Laravel v8.0+
- Livewire v2.0+

## Installation

Install the plugin with Composer:

```bash
composer require filament/spatie-laravel-settings-plugin
```

> Please note that this package is incompatible with `filament/filament` v1, until v2 is released in late 2021. This is due to namespacing collisions.

You're now ready to start building [settings pages](building-settings-pages)!

## Upgrade Guide

To upgrade the package to the latest version, you must run:

```bash
composer update
php artisan view:clear
```

