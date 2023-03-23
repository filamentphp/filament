---
title: Global search
---

Global search allows you to search across all of your resource records, from anywhere in the admin panel.

## Title

To enable global search on your model, you must [set a title attribute](#setting-a-title-attribute) for your resource:

```php
protected static ?string $recordTitleAttribute = 'title';
```

This attribute is used to retrieve the search result title for that record.

**Note:** Your resource needs to have an Edit or View page to allow the global search results to link to a URL, otherwise no results will be returned for this resource.

You may customize the title further by overriding `getGlobalSearchResultTitle()` method:

```php
public static function getGlobalSearchResultTitle(Model $record): string
{
    return $record->name;
}
```

## Multi-column search

If you would like to search across multiple columns of your resource, you may override the `getGloballySearchableAttributes()` method. "Dot-syntax" allows you to search inside of relationships:

```php
public static function getGloballySearchableAttributes(): array
{
    return ['title', 'slug', 'author.name', 'category.name'];
}
```

## Details

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

## URL

Global search results will link to the [Edit page](editing-records) of your resource, or the [View page](viewing-page) if the user does not have [edit permissions](editing-records#authorization). To customize this, you may override the `getGlobalSearchResultUrl()` method and return a route of your choice:

```php
public static function getGlobalSearchResultUrl(Model $record): string
{
    return UserResource::getUrl('edit', ['record' => $record]);
}
```

## Actions

Global search support actions that render a button or link which may open a URL or emit a Livewire event. Actions will render as link by default, but you may configure it to render a button using the `button()` method. 

Actions can be defined as follows:

```php
use Filament\GlobalSearch\Actions\Action;

public static function getGlobalSearchResultActions(Model $record): array
{
    return [
        Action::make('edit')
            ->iconButton()
            ->icon('heroicon-s-pencil')
            ->url(static::getUrl('edit', ['record' => $record])),
    ];
}
```
