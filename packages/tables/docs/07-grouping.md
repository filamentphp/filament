---
title: Grouping rows
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

You may allow users to group table rows together using a common attribute. This is useful for displaying lots of data in a more organized way.

Groups can be set up using the name of the attribute to group by (e.g. `'status'`), or a `Group` object which allows you to customize the behavior of that grouping (e.g. `Group::make('status')->collapsible()`).

## Grouping rows by default

You may want to always group posts by a specific attribute. To do this, pass the group to the `defaultGroup()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->defaultGroup('status');
}
```

<AutoScreenshot name="tables/grouping" alt="Table with grouping" version="4.x" />

## Allowing users to choose between groupings

You may also allow users to pick between different groupings, by passing them in an array to the `groups()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->groups([
            'status',
            'category',
        ]);
}
```

You can use both `groups()` and `defaultGroup()` together to allow users to choose between different groupings, but have a default grouping set:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->groups([
            'status',
            'category',
        ])
        ->defaultGroup('status');
}
```

## Grouping by a relationship attribute

You can also group by a relationship attribute using dot-syntax. For example, if you have an `author` relationship which has a `name` attribute, you can use `author.name` as the name of the attribute:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->groups([
            'author.name',
        ]);
}
```

## Setting a grouping label

By default, the label of the grouping will be generated based on the attribute. You may customize it with a `Group` object, using the `label()` method:

```php
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->groups([
            Group::make('author.name')
                ->label('Author name'),
        ]);
}
```

## Setting a group title

By default, the title of a group will be the value of the attribute. You may customize it by returning a new title from the `getTitleFromRecordUsing()` method of a `Group` object:

```php
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->groups([
            Group::make('status')
                ->getTitleFromRecordUsing(fn (Post $record): string => ucfirst($record->status->getLabel())),
        ]);
}
```

### Disabling the title label prefix

By default, the title is prefixed with the label of the group. To disable this prefix, utilize the `titlePrefixedWithLabel(false)` method:

```php
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->groups([
            Group::make('status')
                ->titlePrefixedWithLabel(false),
        ]);
}
```

## Setting a group description

You may also set a description for a group, which will be displayed underneath the group title. To do this, use the `getDescriptionFromRecordUsing()` method on a `Group` object:

```php
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->groups([
            Group::make('status')
                ->getDescriptionFromRecordUsing(fn (Post $record): string => $record->status->getDescription()),
        ]);
}
```

<AutoScreenshot name="tables/grouping-descriptions" alt="Table with group descriptions" version="4.x" />

## Setting a group key

By default, the key of a group will be the value of the attribute. It is used internally as a raw identifier of that group, instead of the [title](#setting-a-group-title). You may customize it by returning a new key from the `getKeyFromRecordUsing()` method of a `Group` object:

```php
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->groups([
            Group::make('status')
                ->getKeyFromRecordUsing(fn (Post $record): string => $record->status->value),
        ]);
}
```

## Date groups

When using a date-time column as a group, you may want to group by the date only, and ignore the time. To do this, use the `date()` method on a `Group` object:

```php
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->groups([
            Group::make('created_at')
                ->date(),
        ]);
}
```

## Collapsible groups

You can allow rows inside a group to be collapsed underneath their group title. To enable this, use a `Group` object with the `collapsible()` method:

```php
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->groups([
            Group::make('author.name')
                ->collapsible(),
        ]);
}
```

## Summarising groups

You can use [summaries](summaries) with groups to display a summary of the records inside a group. This works automatically if you choose to add a summariser to a column in a grouped table.

### Hiding the grouped rows and showing the summary only

You may hide the rows inside groups and just show the summary of each group using the `groupsOnly()` method. This is very useful in many reporting scenarios.

```php
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('views_count')
                ->summarize(Sum::make()),
            TextColumn::make('likes_count')
                ->summarize(Sum::make()),
        ])
        ->defaultGroup('category')
        ->groupsOnly();
}
```

## Customizing the Eloquent query ordering behavior

Some features require the table to be able to order an Eloquent query according to a group. You can customize how we do this using the `orderQueryUsing()` method on a `Group` object:

```php
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

public function table(Table $table): Table
{
    return $table
        ->groups([
            Group::make('status')
                ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('status', $direction)),
        ]);
}
```

## Customizing the Eloquent query scoping behavior

Some features require the table to be able to scope an Eloquent query according to a group. You can customize how we do this using the `scopeQueryByKeyUsing()` method on a `Group` object:

```php
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;

public function table(Table $table): Table
{
    return $table
        ->groups([
            Group::make('status')
                ->scopeQueryByKeyUsing(fn (Builder $query, string $key) => $query->where('status', $key)),
        ]);
}
```

## Customizing the Eloquent query grouping behavior

Some features require the table to be able to group an Eloquent query according to a group. You can customize how we do this using the `groupQueryUsing()` method on a `Group` object:

```php
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;

public function table(Table $table): Table
{
    return $table
        ->groups([
            Group::make('status')
                ->groupQueryUsing(fn (Builder $query) => $query->groupBy('status')),
        ]);
}
```

## Customizing the groups dropdown trigger action

To customize the groups dropdown trigger button, you may use the `groupRecordsTriggerAction()` method, passing a closure that returns an action. All methods that are available to [customize action trigger buttons](../actions/overview) can be used:

```php
use Filament\Actions\Action;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->groups([
            // ...
        ])
        ->groupRecordsTriggerAction(
            fn (Action $action) => $action
                ->button()
                ->label('Group records'),
        );
}
```

## Using the grouping settings dropdown on desktop

By default, the grouping settings dropdown will only be shown on mobile devices. On desktop devices, the grouping settings are in the header of the table. You can enable the dropdown on desktop devices too by using the `groupingSettingsInDropdownOnDesktop()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->groups([
            // ...
        ])
        ->groupingSettingsInDropdownOnDesktop();
}
```

## Hiding the grouping settings

You can hide the grouping settings interface using the `groupingSettingsHidden()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
		->defaultGroup('status')
        ->groupingSettingsHidden();
}
```

### Hiding the grouping direction setting only

You can hide the grouping direction select interface using the `groupingDirectionSettingHidden()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
		->defaultGroup('status')
        ->groupingDirectionSettingHidden();
}
```
