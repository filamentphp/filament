---
title: No result state
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The table's "no result state" is rendered when no records match a search query or the active filters.

<AutoScreenshot name="tables/no-result-state" alt="Table with no result state" version="3.x" />

## Setting the no result state heading

To customize the heading of the no result state, use the `noResultStateHeading()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->noResultStateHeading('No posts.');
}
```

<AutoScreenshot name="tables/no-result-state-heading" alt="Table with customized no result state heading" version="3.x" />

## Setting the no result state description

To customize the description of the no result state, use the `noResultStateDescription()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->noResultStateDescription('No matching posts were found.');
}
```

<AutoScreenshot name="tables/no-result-state-description" alt="Table with no result state description" version="3.x" />

## Setting the no result state icon

To customize the [icon](https://blade-ui-kit.com/blade-icons?set=1#search) of the no result state, use the `noResultStateIcon()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->noResultStateIcon('heroicon-o-x-circle');
}
```

<AutoScreenshot name="tables/no-result-state-icon" alt="Table with customized no result state icon" version="3.x" />

## Adding no result state actions

You can add [Actions](actions) to the no result state to prompt users to take action. Pass these to the `noResultStateActions()` method:

```php
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->noResultStateActions([
            Action::make('create')
                ->label('Create post')
                ->url(route('posts.create'))
                ->icon('heroicon-m-plus')
                ->button(),
        ]);
}
```

<AutoScreenshot name="tables/no-result-state-actions" alt="Table with no result state actions" version="3.x" />

## Using a custom no result state view

You may use a completely custom no result state view by passing it to the `noResultState()` method:

```php
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->noResultState(view('tables.posts.no-result-state'));
}
```
