---
title: Grouping actions
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

You may group actions together into a dropdown menu by using an `ActionGroup` object. Groups may contain many actions, or other groups:

```php
ActionGroup::make([
    Action::make('view'),
    Action::make('edit'),
    Action::make('delete'),
])
```

<AutoScreenshot name="actions/group/simple" alt="Action group" version="3.x" />

This page is about customizing the look of the group's trigger button and dropdown.

## Customizing the group trigger style

The button which opens the dropdown may be customized in the same way as a normal action. [All the methods available for trigger buttons](trigger-button) may be used to customize the group trigger button:

```php
use Filament\Support\Enums\ActionSize;

ActionGroup::make([
    // Array of actions
])
    ->label('More actions')
    ->icon('heroicon-m-ellipsis-vertical')
    ->size(ActionSize::Small)
    ->color('primary')
    ->button()
```

<AutoScreenshot name="actions/group/customized" alt="Action group with custom trigger style" version="3.x" />

## Setting the placement of the dropdown

The dropdown may be positioned relative to the trigger button by using the `dropdownPlacement()` method:

```php
ActionGroup::make([
    // Array of actions
])
    ->dropdownPlacement('top-start')
```

<AutoScreenshot name="actions/group/placement" alt="Action group with top placement style" version="3.x" />

## Adding dividers between actions

You may add dividers between groups of actions by using nested `ActionGroup` objects:

```php
ActionGroup::make([
    ActionGroup::make([
        // Array of actions
    ])->dropdown(false),
    // Array of actions
])
```

The `dropdown(false)` method puts the actions inside the parent dropdown, instead of a new nested dropdown.

<AutoScreenshot name="actions/group/nested" alt="Action groups nested with dividers" version="3.x" />

## Setting the width of the dropdown

The dropdown may be set to a width by using the `dropdownWidth()` method. Options correspond to [Tailwind's max-width scale](https://tailwindcss.com/docs/max-width). The options are `ExtraSmall`, `Small`, `Medium`, `Large`, `ExtraLarge`, `TwoExtraLarge`, `ThreeExtraLarge`, `FourExtraLarge`, `FiveExtraLarge`, `SixExtraLarge` and `SevenExtraLarge`:

```php
use Filament\Support\Enums\MaxWidth;

ActionGroup::make([
    // Array of actions
])
    ->dropdownWidth(MaxWidth::ExtraSmall)
```

## Controlling the maximum height of the dropdown

The dropdown content can have a maximum height using the `maxHeight()` method, so that it scrolls. You can pass a [CSS length](https://developer.mozilla.org/en-US/docs/Web/CSS/length):

```php
ActionGroup::make([
    // Array of actions
])
    ->maxHeight('400px')
```

## Controlling the dropdown offset

You may control the offset of the dropdown using the `dropdownOffset()` method, by default the offset is set to `8`.

```php
ActionGroup::make([
    // Array of actions
])
    ->dropdownOffset(16)
```
