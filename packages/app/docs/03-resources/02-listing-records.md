---
title: Listing records
---

## Authorization

For authorization, Filament will observe any [model policies](https://laravel.com/docs/authorization#creating-policies) that are registered in your app.

Users may access the List page if the `viewAny()` method of the model policy returns `true`.

The `reorder()` method is used to control [reordering a record](#reordering-records).

## Customizing the Eloquent query

Although you can [customize the Eloquent query for the entire resource](getting-started#customizing-the-eloquent-query), you may also make specific modifications for the List page table. To do this, override the `query()` method on the List page class:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->query(static::getResource()::getEloquentQuery()->withoutGlobalScopes());
}
```

## Custom view

For further customization opportunities, you can override the static `$view` property on the page class to a custom view in your app:

```php
protected static string $view = 'filament.resources.users.pages.list-users';
```
