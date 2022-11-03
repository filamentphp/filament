---
title: Toggle
---

The toggle component, similar to a [checkbox](checkbox), allows you to interact a boolean value.

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')
```

![](https://user-images.githubusercontent.com/41773797/147613146-f5ebde21-f72d-44dd-b5c0-5d229fcd91ef.png)

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

Toggle fields have two layout modes, inline and stacked. By default, they are inline.

When the toggle is inline, its label is adjacent to it:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')->inline()
```

![](https://user-images.githubusercontent.com/41773797/147613146-f5ebde21-f72d-44dd-b5c0-5d229fcd91ef.png)

When the toggle is stacked, its label is above it:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')->inline(false)
```

![](https://user-images.githubusercontent.com/41773797/147613161-43bfa094-0916-4e01-b86d-dbcf8ee63a17.png)

Toggles may also use an "on icon" and an "off icon". These are displayed on its handle and could provide a greater indication to what your field represents. The parameter to each method must contain the name of a Blade icon component:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')
    ->onIcon('heroicon-m-bolt')
    ->offIcon('heroicon-m-user')
```

You may also customize the color representing each state. These may be either `primary`, `secondary`, `success`, `warning` or `danger`:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')
    ->onColor('success')
    ->offColor('danger')
```

![](https://user-images.githubusercontent.com/41773797/147613184-9086c102-ad71-4c4e-9170-9a4201a80c66.png)
