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

## Calculated values

Sometimes you need to calculate the content values that depends on others fields in the database. This is also called _computed column_.

By passing a callback function to the `getStateUsing()` method from a Column component, you can customize the returned $state value.

```php
Tables\Columns\TextColumn::make('incl_vat_amount')
    ->getStateUsing(function(Model $record) {
        return $record->excl_vat_amout * (1 + $record->vat_rate);
    })
    
Tables\Columns\TextColumn::make('name')
    ->getStateUsing(function(User $record) {
        return strtoupper($record->lastname) . ' '. $record->firstname;
    })
    
Tables\Columns\TextColumn::make("download_url")
    ->getStateUsing(function(Model $record) {
         return new HtmlString('<a href="'.Storage::url($record->filename).'">Download</a>);
     }),
```
