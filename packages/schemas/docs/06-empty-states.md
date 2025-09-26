---
title: Empty states
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

You can display an empty state in your schema to communicate that there is no content to show yet, and to guide the user towards the next action. An empty state requires a heading, but can also have a `description()`, [`icon()`](#adding-an-icon-to-the-empty-state) and [`footer()`](#inserting-actions-and-other-components-in-the-footer-of-an-empty-state):

```php
use Filament\Actions\Action;
use Filament\Schemas\Components\EmptyState;
use Filament\Support\Icons\Heroicon;

EmptyState::make('No users yet')
    ->description('Get started by creating a new user.')
    ->icon(Heroicon::OutlinedUser)
    ->footer([
        Action::make('createUser')
            ->icon(Heroicon::Plus),
    ])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing static values, the `make()` and `description()` methods also accept functions to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="schemas/layout/empty-state/simple" alt="Empty state" version="4.x" />

## Adding an icon to the empty state

You may add an [icon](../styling/icons) to the empty state using the `icon()` method:

```php
use Filament\Schemas\Components\EmptyState;
use Filament\Support\Icons\Heroicon;

EmptyState::make('No users yet')
    ->description('Get started by creating a new user.')
    ->icon(Heroicon::OutlinedUser)
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `icon()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Inserting actions and other components in the footer of an empty state

You may insert [actions](../actions) and any other schema component (usually [prime components](primes)) into the footer of an empty state by passing an array of components to the `footer()` method:

```php
use Filament\Actions\Action;
use Filament\Schemas\Components\EmptyState;

EmptyState::make('No users yet')
    ->description('Get started by creating a new user.')
    ->footer([
        Action::make('createUser')
            ->icon(Heroicon::Plus),
    ])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `footer()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>
