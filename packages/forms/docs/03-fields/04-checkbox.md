---
title: Checkbox
---

The checkbox component, similar to a [toggle](toggle), allows you to interact a boolean value.

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('is_admin')
```

![](https://user-images.githubusercontent.com/41773797/147613098-db8d3571-1835-4714-b422-619755c0b722.png)

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

![](https://user-images.githubusercontent.com/41773797/147613098-db8d3571-1835-4714-b422-619755c0b722.png)

When the checkbox is stacked, its label is above it:

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('is_admin')->inline(false)
```

![](https://user-images.githubusercontent.com/41773797/147613119-0bc731dd-fcdd-4c1a-9a26-cce2b0f589d2.png)
