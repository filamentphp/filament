---
title: Checkbox
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The checkbox component, similar to a [toggle](toggle), allows you to interact a boolean value.

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('is_admin')
```

<AutoScreenshot name="forms/fields/checkbox/simple" alt="Checkbox" version="3.x" />

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

## Positioning the label above

Checkbox fields have two layout modes, inline and stacked. By default, they are inline.

When the checkbox is inline, its label is adjacent to it:

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('is_admin')->inline()
```

<AutoScreenshot name="forms/fields/checkbox/inline" alt="Checkbox with its label inline" version="3.x" />

When the checkbox is stacked, its label is above it:

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('is_admin')->inline(false)
```

<AutoScreenshot name="forms/fields/checkbox/not-inline" alt="Checkbox with its label above" version="3.x" />

## Checkbox validation

As well as all rules listed on the [validation](../validation) page, there are additional rules that are specific to checkboxes.

### Accepted validation

You may ensure that the checkbox is checked using the `accepted()` method:

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('terms_of_service')
    ->accepted()
```

### Declined validation

You may ensure that the checkbox is not checked using the `declined()` method:

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('is_under_18')
    ->declined()
```
