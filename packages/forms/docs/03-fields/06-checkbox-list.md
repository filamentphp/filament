---
title: Checkbox list
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import LaracastsBanner from "@components/LaracastsBanner.astro"

## Overview

<LaracastsBanner
    title="Checkbox List"
    description="Watch the Rapid Laravel Development with Filament series on Laracasts - it will teach you the basics of adding checkbox list fields to Filament forms."
    url="https://laracasts.com/series/rapid-laravel-development-with-filament/episodes/5"
    series="rapid-laravel-development"
/>


The checkbox list component allows you to select multiple values from a list of predefined options:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->options([
        'tailwind' => 'Tailwind CSS',
        'alpine' => 'Alpine.js',
        'laravel' => 'Laravel',
        'livewire' => 'Laravel Livewire',
    ])
```

<AutoScreenshot name="forms/fields/checkbox-list/simple" alt="Checkbox list" version="3.x" />

These options are returned in JSON format. If you're saving them using Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    protected $casts = [
        'technologies' => 'array',
    ];

    // ...
}
```

## Setting option descriptions

You can optionally provide descriptions to each option using the `descriptions()` method. This method accepts an array of plain text strings, or instances of `Illuminate\Support\HtmlString` or `Illuminate\Contracts\Support\Htmlable`. This allows you to render HTML, or even markdown, in the descriptions:

```php
use Filament\Forms\Components\CheckboxList;
use Illuminate\Support\HtmlString;

CheckboxList::make('technologies')
    ->options([
        'tailwind' => 'Tailwind CSS',
        'alpine' => 'Alpine.js',
        'laravel' => 'Laravel',
        'livewire' => 'Laravel Livewire',
    ])
    ->descriptions([
        'tailwind' => 'A utility-first CSS framework for rapidly building modern websites without ever leaving your HTML.',
        'alpine' => new HtmlString('A rugged, minimal tool for composing behavior <strong>directly in your markup</strong>.'),
        'laravel' => str('A **web application** framework with expressive, elegant syntax.')->inlineMarkdown()->toHtmlString(),
        'livewire' => 'A full-stack framework for Laravel building dynamic interfaces simple, without leaving the comfort of Laravel.',
    ])
```

<AutoScreenshot name="forms/fields/checkbox-list/option-descriptions" alt="Checkbox list with option descriptions" version="3.x" />

Be sure to use the same `key` in the descriptions array as the `key` in the option array so the right description matches the right option.

## Splitting options into columns

You may split options into columns by using the `columns()` method:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->options([
        // ...
    ])
    ->columns(2)
```

<AutoScreenshot name="forms/fields/checkbox-list/columns" alt="Checkbox list with 2 columns" version="3.x" />

This method accepts the same options as the `columns()` method of the [grid](layout/grid). This allows you to responsively customize the number of columns at various breakpoints.

### Setting the grid direction

By default, when you arrange checkboxes into columns, they will be listed in order vertically. If you'd like to list them horizontally, you may use the `gridDirection('row')` method:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->options([
        // ...
    ])
    ->columns(2)
    ->gridDirection('row')
```

<AutoScreenshot name="forms/fields/checkbox-list/rows" alt="Checkbox list with 2 rows" version="3.x" />

## Disabling specific options

You can disable specific options using the `disableOptionWhen()` method. It accepts a closure, in which you can check if the option with a specific `$value` should be disabled:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->options([
        'tailwind' => 'Tailwind CSS',
        'alpine' => 'Alpine.js',
        'laravel' => 'Laravel',
        'livewire' => 'Laravel Livewire',
    ])
    ->disableOptionWhen(fn (string $value): bool => $value === 'livewire')
```

If you want to retrieve the options that have not been disabled, e.g. for validation purposes, you can do so using `getEnabledOptions()`:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->options([
        'tailwind' => 'Tailwind CSS',
        'alpine' => 'Alpine.js',
        'laravel' => 'Laravel',
        'livewire' => 'Laravel Livewire',
        'heroicons' => 'SVG icons',
    ])
    ->disableOptionWhen(fn (string $value): bool => $value === 'heroicons')
    ->in(fn (CheckboxList $component): array => array_keys($component->getEnabledOptions()))
```

## Searching options

You may enable a search input to allow easier access to many options, using the `searchable()` method:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->options([
        // ...
    ])
    ->searchable()
```

<AutoScreenshot name="forms/fields/checkbox-list/searchable" alt="Searchable checkbox list" version="3.x" />

## Bulk toggling checkboxes

You may allow users to toggle all checkboxes at once using the `bulkToggleable()` method:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->options([
        // ...
    ])
    ->bulkToggleable()
```

<AutoScreenshot name="forms/fields/checkbox-list/bulk-toggleable" alt="Bulk toggleable checkbox list" version="3.x" />

## Integrating with an Eloquent relationship

> If you're building a form inside your Livewire component, make sure you have set up the [form's model](../adding-a-form-to-a-livewire-component#setting-a-form-model). Otherwise, Filament doesn't know which model to use to retrieve the relationship from.

You may employ the `relationship()` method of the `CheckboxList` to point to a `BelongsToMany` relationship. Filament will load the options from the relationship, and save them back to the relationship's pivot table when the form is submitted. The `titleAttribute` is the name of a column that will be used to generate a label for each option:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->relationship(titleAttribute: 'name')
```

When using `disabled()` with `relationship()`, ensure that `disabled()` is called before `relationship()`. This ensures that the `dehydrated()` call from within `relationship()` is not overridden by the call from `disabled()`:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->disabled()
    ->relationship(titleAttribute: 'name')
```

### Customizing the relationship query

You may customize the database query that retrieves options using the `modifyOptionsQueryUsing` parameter of the `relationship()` method:

```php
use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\Builder;

CheckboxList::make('technologies')
    ->relationship(
        titleAttribute: 'name',
        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
    )
```

### Customizing the relationship option labels

If you'd like to customize the label of each option, maybe to be more descriptive, or to concatenate a first and last name, you could use a virtual column in your database migration:

```php
$table->string('full_name')->virtualAs('concat(first_name, \' \', last_name)');
```

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('authors')
    ->relationship(titleAttribute: 'full_name')
```

Alternatively, you can use the `getOptionLabelFromRecordUsing()` method to transform an option's Eloquent model into a label:

```php
use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

CheckboxList::make('authors')
    ->relationship(
        modifyQueryUsing: fn (Builder $query) => $query->orderBy('first_name')->orderBy('last_name'),
    )
    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
```

## Setting a custom no search results message

When you're using a searchable checkbox list, you may want to display a custom message when no search results are found. You can do this using the `noSearchResultsMessage()` method:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->options([
        // ...
    ])
    ->searchable()
    ->noSearchResultsMessage('No technologies found.')
```

## Setting a custom search prompt

When you're using a searchable checkbox list, you may want to tweak the search input's placeholder when the user has not yet entered a search term. You can do this using the `searchPrompt()` method:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->options([
        // ...
    ])
    ->searchable()
    ->searchPrompt('Search for a technology')
```

## Tweaking the search debounce

By default, Filament will wait 1000 milliseconds (1 second) before searching for options when the user types in a searchable checkbox list. It will also wait 1000 milliseconds between searches if the user is continuously typing into the search input. You can change this using the `searchDebounce()` method:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->options([
        // ...
    ])
    ->searchable()
    ->searchDebounce(500)
```

## Customizing the checkbox list action objects

This field uses action objects for easy customization of buttons within it. You can customize these buttons by passing a function to an action registration method. The function has access to the `$action` object, which you can use to [customize it](../../actions/trigger-button). The following methods are available to customize the actions:

- `selectAllAction()`
- `deselectAllAction()`

Here is an example of how you might customize an action:

```php
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->options([
        // ...
    ])
    ->selectAllAction(
        fn (Action $action) => $action->label('Select all technologies'),
    )
```
