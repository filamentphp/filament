---
title: Empty state
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

The table's "empty state" is rendered when there are no rows in the table.

<AutoScreenshot name="tables/empty-state" alt="Table with empty state" version="4.x" />

## Setting the empty state heading

To customize the heading of the empty state, use the `emptyStateHeading()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->emptyStateHeading('No posts yet');
}
```

<AutoScreenshot name="tables/empty-state-heading" alt="Table with customized empty state heading" version="4.x" />

## Setting the empty state description

To customize the description of the empty state, use the `emptyStateDescription()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->emptyStateDescription('Once you write your first post, it will appear here.');
}
```

<AutoScreenshot name="tables/empty-state-description" alt="Table with empty state description" version="4.x" />

## Setting the empty state icon

To customize the [icon](../styling/icons) of the empty state, use the `emptyStateIcon()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->emptyStateIcon('heroicon-o-bookmark');
}
```

<AutoScreenshot name="tables/empty-state-icon" alt="Table with customized empty state icon" version="4.x" />

## Adding empty state actions

You can add [Actions](actions) to the empty state to prompt users to take action. Pass these to the `emptyStateActions()` method:

```php
use Filament\Actions\Action;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->emptyStateActions([
            Action::make('create')
                ->label('Create post')
                ->url(route('posts.create'))
                ->icon('heroicon-m-plus')
                ->button(),
        ]);
}
```

<AutoScreenshot name="tables/empty-state-actions" alt="Table with empty state actions" version="4.x" />

## Using a custom empty state view

You may use a completely custom empty state view by passing it to the `emptyState()` method:

```php
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->emptyState(view('tables.posts.empty-state'));
}
```
