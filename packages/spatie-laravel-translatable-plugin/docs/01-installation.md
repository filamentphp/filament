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
composer require filament/spatie-laravel-translatable-plugin:"^2.0"
```

You're now ready to start [translating resources](getting-started)!

## Publishing the configuration

If you wish, you may publish the configuration of the package using:

```bash
php artisan vendor:publish --tag=filament-spatie-laravel-translatable-plugin-config
```

## Publishing the translations

If you wish to translate the package, you may publish the language files using:

```bash
php artisan vendor:publish --tag=filament-spatie-laravel-translatable-plugin-translations
```

## Upgrade Guide

To upgrade the package to the latest version, you must run:

```bash
composer update
```
