---
title: Section
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

You may want to separate your fields into sections, each with a heading and description. To do this, you can use a section component:

```php
use Filament\Forms\Components\Section;

Section::make('Rate limiting')
    ->description('Prevent abuse by limiting the number of requests per period')
    ->schema([
        // ...
    ])
```

<AutoScreenshot name="forms/layout/section/simple" alt="Section" version="3.x" />

You can also use a section without a header, which just wraps the components in a simple card:

```php
use Filament\Forms\Components\Section;

Section::make()
    ->schema([
        // ...
    ])
```

<AutoScreenshot name="forms/layout/section/without-header" alt="Section without header" version="3.x" />

## Adding actions to the section's header

You may add [actions](../actions) to the section's header using the `headerActions()` method:

```php
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;

Section::make('Rate limiting')
    ->headerActions([
        Action::make('test')
            ->action(function () {
                // ...
            }),
    ])
    ->schema([
        // ...
    ])
```

<AutoScreenshot name="forms/layout/section/actions" alt="Section with header actions" version="3.x" />

If your section does not have a heading, Filament has no way of locating the action inside it. In this case, you must pass a unique `id()` to the section:

```php
use Filament\Forms\Components\Section;

Section::make()
    ->id('rateLimitingSection')
    ->headerActions([
        // ...
    ])
    ->schema([
        // ...
    ])
```

## Adding an icon to the section's header

You may add an [icon](https://blade-ui-kit.com/blade-icons?set=1#search) to the section's header using the `icon()` method:

```php
use Filament\Forms\Components\Section;

Section::make('Cart')
    ->description('The items you have selected for purchase')
    ->icon('heroicon-m-shopping-bag')
    ->schema([
        // ...
    ])
```

<AutoScreenshot name="forms/layout/section/icons" alt="Section with icon" version="3.x" />

## Positioning the heading and description aside

You may use the `aside()` to align heading & description on the left, and the form components inside a card on the right:

```php
use Filament\Forms\Components\Section;

Section::make('Rate limiting')
    ->description('Prevent abuse by limiting the number of requests per period')
    ->aside()
    ->schema([
        // ...
    ])
```

<AutoScreenshot name="forms/layout/section/aside" alt="Section with heading and description aside" version="3.x" />

## Collapsing sections

Sections may be `collapsible()` to optionally hide content in long forms:

```php
use Filament\Forms\Components\Section;

Section::make('Cart')
    ->description('The items you have selected for purchase')
    ->schema([
        // ...
    ])
    ->collapsible()
```

Your sections may be `collapsed()` by default:

```php
use Filament\Forms\Components\Section;

Section::make('Cart')
    ->description('The items you have selected for purchase')
    ->schema([
        // ...
    ])
    ->collapsed()
```

<AutoScreenshot name="forms/layout/section/collapsed" alt="Collapsed section" version="3.x" />

### Persisting collapsed sections

You can persist whether a section is collapsed in local storage using the `persistCollapsed()` method, so it will remain collapsed when the user refreshes the page:

```php
use Filament\Infolists\Components\Section;

Section::make('Cart')
    ->description('The items you have selected for purchase')
    ->schema([
        // ...
    ])
    ->collapsible()
    ->persistCollapsed()
```

To persist the collapse state, the local storage needs a unique ID to store the state. This ID is generated based on the heading of the section. If your section does not have a heading, or if you have multiple sections with the same heading that you do not want to collapse together, you can manually specify the `id()` of that section to prevent an ID conflict:

```php
use Filament\Infolists\Components\Section;

Section::make('Cart')
    ->description('The items you have selected for purchase')
    ->schema([
        // ...
    ])
    ->collapsible()
    ->persistCollapsed()
    ->id('order-cart')
```

## Compact section styling

When nesting sections, you can use a more compact styling:

```php
use Filament\Forms\Components\Section;

Section::make('Rate limiting')
    ->description('Prevent abuse by limiting the number of requests per period')
    ->schema([
        // ...
    ])
    ->compact()
```

<AutoScreenshot name="forms/layout/section/compact" alt="Compact section" version="3.x" />

## Using grid columns within a section

You may use the `columns()` method to easily create a [grid](grid) within the section:

```php
use Filament\Forms\Components\Section;

Section::make('Heading')
    ->schema([
        // ...
    ])
    ->columns(2)
```
