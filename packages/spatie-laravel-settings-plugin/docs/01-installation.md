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
composer require filament/spatie-laravel-settings-plugin:"^2.0"
```

You're now ready to start building [settings pages](getting-started)!

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
