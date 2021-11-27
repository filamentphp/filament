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
composer require filament/spatie-laravel-translatable-plugin
```

You're now ready to start [translating content](translating-content)!

## Upgrade Guide

To upgrade the package to the latest version, you must run:

```bash
composer update
php artisan config:clear
php artisan view:clear
```

To do this automatically, we recommend adding these commands to your `composer.json`'s `post-update-cmd`:

```json
"post-update-cmd": [
    // ...
    "@php artisan config:clear",
    "@php artisan view:clear"
],
```
