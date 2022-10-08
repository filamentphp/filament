---
title: Custom columns
---

## View column

You may render a custom view for a cell using the `view()` method:

```php
use Filament\Tables\Columns\ViewColumn;

ViewColumn::make('status')->view('filament.tables.columns.status-switcher')
```

Inside your view, you may retrieve the state of the cell using the `$getState()` method:

```blade
<div>
    {{ $getState() }}
</div>
```

You can also access the entire Eloquent record with `$getRecord()`.

## Custom classes

You may create your own custom column classes and cell views, which you can reuse across your project, and even release as a plugin to the community.

> If you're just creating a simple custom column to use once, you could instead use a [view column](#view-column) to render any custom Blade file.

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

Inside your view, you may retrieve the state of the cell using the `$getState()` method:

```blade
<div>
    {{ $getState() }}
</div>
```

You can also access the entire Eloquent record with `$getRecord()`.
