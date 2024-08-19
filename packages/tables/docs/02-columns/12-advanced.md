---
title: Advanced columns
---

## Table column utility injection

The vast majority of methods used to configure columns accept functions as parameters instead of hardcoded values:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('status')
    ->color(fn (string $state): string => match ($state) {
        'draft' => 'gray',
        'reviewing' => 'warning',
        'published' => 'success',
        'rejected' => 'danger',
    })
```

This alone unlocks many customization possibilities.

The package is also able to inject many utilities to use inside these functions, as parameters. All customization methods that accept functions as arguments can inject utilities.

These injected utilities require specific parameter names to be used. Otherwise, Filament doesn't know what to inject.

### Injecting the current state of a column

If you wish to access the current state (value) of the column, define a `$state` parameter:

```php
function ($state) {
    // ...
}
```

### Injecting the current Eloquent record

If you wish to access the current Eloquent record of the column, define a `$record` parameter:

```php
use Illuminate\Database\Eloquent\Model;

function (Model $record) {
    // ...
}
```

Be aware that this parameter will be `null` if the column is not bound to an Eloquent record. For instance, the `label()` method of a column will not have access to the record, as the label is not related to any table row.

### Injecting the current column instance

If you wish to access the current column instance, define a `$column` parameter:

```php
use Filament\Tables\Columns\Column;

function (Column $column) {
    // ...
}
```

### Injecting the current Livewire component instance

If you wish to access the current Livewire component instance that the table belongs to, define a `$livewire` parameter:

```php
use Filament\Tables\Contracts\HasTable;

function (HasTable $livewire) {
    // ...
}
```

### Injecting the current table instance

If you wish to access the current table configuration instance that the column belongs to, define a `$table` parameter:

```php
use Filament\Tables\Table;

function (Table $table) {
    // ...
}
```

### Injecting the current table row loop

If you wish to access the current [Laravel Blade loop object](https://laravel.com/docs/blade#the-loop-variable) that the column is rendered part of, define a `$rowLoop` parameter:

```php
function (stdClass $rowLoop) {
    // ...
}
```

As `$rowLoop` is [Laravel Blade's `$loop` object](https://laravel.com/docs/blade#the-loop-variable), you can access the current row index using `$rowLoop->index`. Similar to `$record`, this parameter will be `null` if the column is currently being rendered outside a table row.

### Injecting multiple utilities

The parameters are injected dynamically using reflection, so you are able to combine multiple parameters in any order:

```php
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;

function (HasTable $livewire, Model $record) {
    // ...
}
```

### Injecting dependencies from Laravel's container

You may inject anything from Laravel's container like normal, alongside utilities:

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

function (Request $request, Model $record) {
    // ...
}
```
