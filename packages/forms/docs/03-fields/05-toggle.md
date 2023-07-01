---
title: Toggle
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The toggle component, similar to a [checkbox](checkbox), allows you to interact a boolean value.

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')
```

<AutoScreenshot name="forms/fields/toggle/simple" alt="Toggle" version="3.x" />

If you're saving the boolean value using Eloquent, you should be sure to add a `boolean` [cast](https://laravel.com/docs/eloquent-mutators#attribute-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $casts = [
        'is_admin' => 'boolean',
    ];

    // ...
}
```

## Adding icons to the toggle button

Toggles may also use an [icon](https://blade-ui-kit.com/blade-icons?set=1#search) to represent the "on" and "off" state of the button. To add an icon to the "on" state, use the `onIcon()` method. To add an icon to the "off" state, use the `offIcon()` method:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')
    ->onIcon('heroicon-m-bolt')
    ->offIcon('heroicon-m-user')
```

<AutoScreenshot name="forms/fields/toggle/icons" alt="Toggle icons" version="3.x" />

## Customizing the color of the toggle button

You may also customize the color representing the "on" or "off" state of the toggle. These may be either `danger`, `gray`, `info`, `primary`, `success` or `warning`. To add a color to the "on" state, use the `onColor()` method. To add a color to the "off" state, use the `offColor()` method:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')
    ->onColor('success')
    ->offColor('danger')
```

<AutoScreenshot name="forms/fields/toggle/off-color" alt="Toggle off color" version="3.x" />

<AutoScreenshot name="forms/fields/toggle/on-color" alt="Toggle on color" version="3.x" />

## Positioning the label above

Toggle fields have two layout modes, inline and stacked. By default, they are inline.

When the toggle is inline, its label is adjacent to it:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')
    ->inline()
```

<AutoScreenshot name="forms/fields/toggle/inline" alt="Toggle with its label inline" version="3.x" />

When the toggle is stacked, its label is above it:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')
    ->inline(false)
```

<AutoScreenshot name="forms/fields/toggle/not-inline" alt="Toggle with its label above" version="3.x" />

## Toggle validation

As well as all rules listed on the [validation](../validation) page, there are additional rules that are specific to toggles.

### Accepted validation

You may ensure that the toggle is "on" using the `accepted()` method:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('terms_of_service')
    ->accepted()
```

### Declined validation

You may ensure that the toggle is "off" using the `declined()` method:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_under_18')
    ->declined()
```
