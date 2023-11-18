---
title: Global search
---

## Overview

Global search allows you to search across all of your resource records, from anywhere in the app.

## Setting global search result titles

To enable global search on your model, you must [set a title attribute](getting-started#record-titles) for your resource:

```php
protected static ?string $recordTitleAttribute = 'title';
```

This attribute is used to retrieve the search result title for that record.

> Your resource needs to have an Edit or View page to allow the global search results to link to a URL, otherwise no results will be returned for this resource.

You may customize the title further by overriding `getGlobalSearchResultTitle()` method. It may return a plain text string, or an instance of `Illuminate\Support\HtmlString` or `Illuminate\Contracts\Support\Htmlable`. This allows you to render HTML, or even Markdown, in the search result title:

```php
use Illuminate\Contracts\Support\Htmlable;

public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
{
    return $record->name;
}
```

## Globally searching across multiple columns

If you would like to search across multiple columns of your resource, you may override the `getGloballySearchableAttributes()` method. "Dot notation" allows you to search inside relationships:

```php
public static function getGloballySearchableAttributes(): array
{
    return ['title', 'slug', 'author.name', 'category.name'];
}
```

## Adding extra details to global search results

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
public static function getGlobalSearchEloquentQuery(): Builder
{
    return parent::getGlobalSearchEloquentQuery()->with(['author', 'category']);
}
```

## Customizing global search result URLs

Global search results will link to the [Edit page](editing-records) of your resource, or the [View page](viewing-page) if the user does not have [edit permissions](editing-records#authorization). To customize this, you may override the `getGlobalSearchResultUrl()` method and return a route of your choice:

```php
public static function getGlobalSearchResultUrl(Model $record): string
{
    return UserResource::getUrl('edit', ['record' => $record]);
}
```

## Adding actions to global search results

Global search supports actions, which are buttons that render below each search result. They can open a URL or dispatch a Livewire event.

Actions can be defined as follows:

```php
use Filament\GlobalSearch\Actions\Action;

public static function getGlobalSearchResultActions(Model $record): array
{
    return [
        Action::make('edit')
            ->url(static::getUrl('edit', ['record' => $record])),
    ];
}
```

You can learn more about how to style action buttons [here](../../actions/trigger-button).

### Opening URLs from global search actions

You can open a URL, optionally in a new tab, when clicking on an action:

```php
use Filament\GlobalSearch\Actions\Action;

Action::make('view')
    ->url(static::getUrl('view', ['record' => $record]), shouldOpenInNewTab: true)
```

### Dispatching Livewire events from global search actions

Sometimes you want to execute additional code when a global search result action is clicked. This can be achieved by setting a Livewire event which should be dispatched on clicking the action. You may optionally pass an array of data, which will be available as parameters in the event listener on your Livewire component:

```php
use Filament\GlobalSearch\Actions\Action;

Action::make('quickView')
    ->dispatch('quickView', [$record->id])
```

## Limiting the number of global search results

By default, global search will return up to 50 results per resource. You can customize this on the resource label by overriding the `$globalSearchResultsLimit` property:

```php
protected static int $globalSearchResultsLimit = 20;
```

## Disabling global search

As [explained above](#title), global search is automatically enabled once you set a title attribute for your resource. Sometimes you may want to specify the title attribute while not enabling global search.

This can be achieved by disabling global search in the [configuration](configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->globalSearch(false);
}
```

## Registering global search keybindings

The global search field can be opened using keyboard shortcuts. To configure these, pass the `globalSearchKeyBindings()` method to the [configuration](configuration):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->globalSearchKeyBindings(['command+k', 'ctrl+k']);
}
```
