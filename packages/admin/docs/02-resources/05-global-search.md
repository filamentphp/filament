---
title: Global Search
---

"Global search" is a feature that allows you to search across all of your resources.

To enable global search on your model, you must [set a title attribute](#setting-a-title-attribute) for your resource:

```php
protected static ?string $recordTitleAttribute = 'title';
```

If you would like to search across multiple columns of your resource, you may override the `getGloballySearchableAttributes()` method. "Dot-syntax" allows you to search inside of relationships:

```php
public static function getGloballySearchableAttributes(): array
{
    return ['title', 'slug', 'author.name', 'category.name'];
}
```

Search results can display "details" below their title, which gives the user more information about the record. To enable this feature, you must override the `getGlobalSearchResultDetails()` method:

```php
public static function getGlobalSearchResultDetails(Model $record): array
{
    return [
        'Author' => $record->author->name,
        'Category' => $record->category->name,
    ];
}
```

In this example, the category and author of the record will be displayed below its title in the search result. However, the `category` and `author` relationships will be lazy-loaded, which will result in poor results performance. To [eager-load](https://laravel.com/docs/eloquent-relationships#eager-loading) these relationships, we must override the `getGlobalSearchEloquentQuery()` method:

```php
protected static function getGlobalSearchEloquentQuery(): Builder
{
    return parent::getGlobalSearchEloquentQuery()->with(['author', 'category']);
}
```

You may customise the record "title" displayed in global search results by overriding `getGlobalSearchResultTitle()` method:

```php
public static function getGlobalSearchResultTitle(Model $record): string
{
    return $record->name;
}
```

Global search results will link to the edit page of your resource, or the [view page](#view-page) if the user does not have [edit permissions](#authorization). To customize this, you may override the `getGlobalSearchResultUrl()` method and return a route of your choice:

```php
public static function getGlobalSearchResultUrl(Model $record): string
{
    return route('users.edit', ['user' => $record]);
}
```
