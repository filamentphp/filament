---
title: Dashboard
---

Filament allows you to build dynamic custom dashboard widgets very easily.

## Getting started

To get started building a `BlogPostsOverview` widget:

```bash
php artisan make:filament-widget BlogPostsOverview
```

This command will create two files - a widget class in the `/Widgets` directory of the Filament directory, and a view in the `/widgets` directory of the Filament views directory.

Widgets are pure [Livewire](https://laravel-livewire.com) components, so may use any features of that package.

## Disabling the default widgets

By default, two widgets are displayed on the dashboard. These widgets can be disabled by updating the `widgets.register` property of the [configuration](installation#publishing-the-configuration) file:

```php
'widgets' => [
    // ...
    'register' => [],
],
```
