---
title: Dashboard
description:
extends: _layouts.documentation
section: content
toc: |
  - [Disabling the Default Widgets](#disabling-default-widgets)
---

# Dashboard

<p class="lg:text-2xl">Filament allows you to build dynamic custom dashboard widgets very easily. To get started building a `Stats` widget:</p>

```bash
php artisan make:filament-widget Stats
```

This command will create two files - a widget class in the `/Widgets` directory of the Filament directory, and a view in the `/widgets` directory of the Filament views directory.

Widgets are pure [Laravel Livewire](https://laravel-livewire.com) components, so may use any features of that package.

> Pre-built widget templates are coming soon. For more information, please see our [Development Roadmap](/docs/roadmap).

## Disabling the Default Widgets {#disabling-default-widgets}

By default, two widgets are displayed on the dashboard. These widgets can be disabled by updating the `widgets` section of the [configuration](/docs#configuration) file. Updating each entries to `false` will remove the corresponding default widget from the dashboard.

```php
  'widgets' => [
      // ...
      'default' => [
          'account' => false, // Disables the account widget.
          'info' => false, // Disables the info widget.
      ],
  ],
```
