---
title: Select column
---

The select column allows you to render a select field inside the table, which can be used to update that database record without needing to open a new page or a modal.

You must pass options to the column:

```php
use Filament\Tables\Columns\SelectColumn;

SelectColumn::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
```

## Validation

You can validate the input by passing any [Laravel validation rules](https://laravel.com/docs/validation#available-validation-rules) in an array:

```php
use Filament\Tables\Columns\SelectColumn;

SelectColumn::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
    ->rules(['required'])
```

## Relationships

You can populate data based on a database and directly link it to the relation id in a table:

```php
use Filament\Tables\Columns\SelectColumn;

SelectColumn::make('authorId')
   ->label('Author')
   ->options(User::all()->pluck('name', 'id'))
    ->rules(['required'])
```

## Disabling placeholder selection

You can prevent the placeholder from being selected using the `disablePlaceholderSelection()` method:

```php
use Filament\Tables\Columns\SelectColumn;

SelectColumn::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
    ->disablePlaceholderSelection()
```
