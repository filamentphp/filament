---
title: Section
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

You may want to separate your fields into sections, each with a heading and description. To do this, you can use a section component:

```php
use Filament\Schemas\Components\Section;

Section::make('Rate limiting')
    ->description('Prevent abuse by limiting the number of requests per period')
    ->schema([
        // ...
    ])
```

<AutoScreenshot name="schemas/layout/section/simple" alt="Section" version="4.x" />

You can also use a section without a header, which just wraps the components in a simple card:

```php
use Filament\Schemas\Components\Section;

Section::make()
    ->schema([
        // ...
    ])
```

<AutoScreenshot name="schemas/layout/section/without-header" alt="Section without header" version="4.x" />

## Adding actions to the section's header or footer

Sections can have actions in their [header](#adding-actions-to-the-sections-header) or [footer](#adding-actions-to-the-sections-footer).

### Adding actions to the section's header

You may add [actions](../actions) to the section's header using the `headerActions()` method:

```php
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;

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

<AutoScreenshot name="schemas/layout/section/header/actions" alt="Section with header actions" version="4.x" />

> [Make sure the section has a heading or ID](#adding-actions-to-a-section-without-heading)

### Adding actions to the section's footer

In addition to [header actions](#adding-an-icon-to-the-sections-header), you may add [actions](../actions) to the section's footer using the `footerActions()` method:

```php
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;

Section::make('Rate limiting')
    ->schema([
        // ...
    ])
    ->footerActions([
        Action::make('test')
            ->action(function () {
                // ...
            }),
    ])
```

<AutoScreenshot name="schemas/layout/section/footer/actions" alt="Section with footer actions" version="4.x" />

> [Make sure the section has a heading or ID](#adding-actions-to-a-section-without-heading)

#### Aligning section footer actions

Footer actions are aligned to the inline start by default. You may customize the alignment using the `footerActionsAlignment()` method:

```php
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Alignment;

Section::make('Rate limiting')
    ->schema([
        // ...
    ])
    ->footerActions([
        Action::make('test')
            ->action(function () {
                // ...
            }),
    ])
    ->footerActionsAlignment(Alignment::End)
```

### Adding actions to a section without heading

If your section does not have a heading, Filament has no way of locating the action inside it. In this case, you must pass a unique `id()` to the section:

```php
use Filament\Schemas\Components\Section;

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

You may add an [icon](../../styling/icons) to the section's header using the `icon()` method:

```php
use Filament\Schemas\Components\Section;

Section::make('Cart')
    ->description('The items you have selected for purchase')
    ->icon('heroicon-m-shopping-bag')
    ->schema([
        // ...
    ])
```

<AutoScreenshot name="schemas/layout/section/icons" alt="Section with icon" version="4.x" />

## Positioning the heading and description aside

You may use the `aside()` to align heading & description on the left, and the components inside a card on the right:

```php
use Filament\Schemas\Components\Section;

Section::make('Rate limiting')
    ->description('Prevent abuse by limiting the number of requests per period')
    ->aside()
    ->schema([
        // ...
    ])
```

<AutoScreenshot name="schemas/layout/section/aside" alt="Section with heading and description aside" version="4.x" />

## Collapsing sections

Sections may be `collapsible()` to optionally hide long content:

```php
use Filament\Schemas\Components\Section;

Section::make('Cart')
    ->description('The items you have selected for purchase')
    ->schema([
        // ...
    ])
    ->collapsible()
```

Your sections may be `collapsed()` by default:

```php
use Filament\Schemas\Components\Section;

Section::make('Cart')
    ->description('The items you have selected for purchase')
    ->schema([
        // ...
    ])
    ->collapsed()
```

<AutoScreenshot name="schemas/layout/section/collapsed" alt="Collapsed section" version="4.x" />

### Persisting collapsed sections

You can persist whether a section is collapsed in local storage using the `persistCollapsed()` method, so it will remain collapsed when the user refreshes the page:

```php
use Filament\Schemas\Components\Section;

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
use Filament\Schemas\Components\Section;

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
use Filament\Schemas\Components\Section;

Section::make('Rate limiting')
    ->description('Prevent abuse by limiting the number of requests per period')
    ->schema([
        // ...
    ])
    ->compact()
```

<AutoScreenshot name="schemas/layout/section/compact" alt="Compact section" version="4.x" />

## Using grid columns within a section

You may use the `columns()` method to easily create a [grid](grid) within the section:

```php
use Filament\Schemas\Components\Section;

Section::make('Heading')
    ->schema([
        // ...
    ])
    ->columns(2)
```
