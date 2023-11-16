---
title: Custom columns
---
import LaracastsBanner from "@components/LaracastsBanner.astro"

<LaracastsBanner
    title="Build a Custom Table Column"
    description="Watch the Build Advanced Components for Filament series on Laracasts - it will teach you how to build components, and you'll get to know all the internal tools to help you."
    url="https://laracasts.com/series/build-advanced-components-for-filament/episodes/10"
/>

## View columns

You may render a custom view for a cell using the `view()` method:

```php
use Filament\Tables\Columns\ViewColumn;

ViewColumn::make('status')->view('filament.tables.columns.status-switcher')
```

This assumes that you have a `resources/views/filament/tables/columns/status-switcher.blade.php` file.

## Custom classes

You may create your own custom column classes and cell views, which you can reuse across your project, and even release as a plugin to the community.

> If you're just creating a simple custom column to use once, you could instead use a [view column](#view-columns) to render any custom Blade file.

To create a custom column class and view, you may use the following command:

```bash
php artisan make:table-column StatusSwitcher
```

This will create the following column class:

```php
use Filament\Tables\Columns\Column;

class StatusSwitcher extends Column
{
    protected string $view = 'filament.tables.columns.status-switcher';
}
```

It will also create a view file at `resources/views/filament/tables/columns/status-switcher.blade.php`.

## Accessing the state

Inside your view, you may retrieve the state of the cell using the `$getState()` function:

```blade
<div>
    {{ $getState() }}
</div>
```

## Accessing the Eloquent record

Inside your view, you may access the Eloquent record using the `$getRecord()` function:

```blade
<div>
    {{ $getRecord()->name }}
</div>
```
