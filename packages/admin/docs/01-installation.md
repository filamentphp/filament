---
title: Installation
---

## Requirements

Filament has a few requirements to run:

- PHP 8.0+
- Laravel v8.0+
- Livewire v2.0+

This plugin is compatible with other Filament v2.x packages. The [form builder](/docs/forms) and [table builder](/docs/tables) come pre-installed with the package, and no other installation steps are required to use them within the admin panel.

## Installation

You're now ready to start [building resources](building-resources)!

## Upgrade Guide

To upgrade the package to the latest version, you must run:

```bash
php artisan config:clear
php artisan livewire:discover
php artisan route:clear
php artisan view:clear
```

Alternatively, you may use the `filament:upgrade` command to do this all at once:

```bash
composer update
php artisan filament:upgrade
```

We recommend adding these commands to your `composer.json`'s `post-update-cmd`:

```json
"post-update-cmd": [
    // ...
    "@php artisan filament:upgrade"
],
```
