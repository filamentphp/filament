---
title: Advanced
---

## Using callback customisation

All configuration methods for [fields](fields) and [layout components](layout) accept callback functions as parameters instead of hardcoded values:

```php
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
    
DatePicker::make('date_of_birth')
    ->displayFormat(function () {
        if (auth()->user()->country_id === 'us') {
            return 'm/d/Y'
        } else {
            return 'd/m/Y'
        }
    })

Select::make('userId')
    ->options(function () {
        return User::all()->pluck('name', 'id');
    })

TextInput::make('middle_name')
    ->required(function () {
        return auth()->user()->hasMiddleName();
    })
```

This alone unlocks many customization possibilities.

The package is also able to inject many utilities to use inside these callback functions, as parameters.

If you wish to access the current state (value) of the component, define a `$state` parameter:

```php
function ($state) {
    // ...
}
```

If you wish to access the current component instance, define a `$component` parameter:

```php
use Filament\Forms\Components\Component;

function (Component $component) {
    // ...
}
```

If you wish to access the current Livewire component instance, define a `$livewire` parameter:

```php
use Livewire\Component as Livewire;

function (Livewire $livewire) {
    // ...
}
```

If you have defined a form or component Eloquent model, define a `$model` parameter:

```php
use Illuminate\Database\Eloquent\Model;

function (Model $model) {
    // ...
}
```

You may also retrieve the value of another field from within a callback, using a callable `$get` parameter:

```php
function (callable $get) {
    $email = $get('email'); // Store the value of the `email` field in the `$email` variable.
    //...
}
```

In a similar way to `$get`, you may also set the value of another field from within a callback, using a callable `$set` parameter:

```php
function (callable $set) {
    $set('title', 'Blog Post'); // Set the `title` field to `Blog Post`.
    //...
}
```

Callbacks are evaluated using Laravel's `app()->call()` under the hood, so you are able to combine multiple parameters in any order:

```php
use Livewire\Component as Livewire;

function (Livewire $livewire, callable $get, callable $set) {
    // ...
}
```

### Reloading the form when a field is updated

By default, forms are only reloaded when they are validated or submitted. You may allow a form to be reloaded when a field is changed, by using the `reactive()` method on that field:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('title')->reactive()
```

A great example to give a use case for this is when you wish to generate a slug from a title field automatically:

<iframe width="560" height="315" src="https://www.youtube.com/embed/GNsk5z7-PEs" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

## Conditionally hiding components

Sometimes, you may wish to conditionally hide any form component. You may do this with a `hidden()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('newPassword')->password()
TextInput::make('newPasswordConfirmation')
    ->password()
    ->hidden(fn (callable $get) => $get('newPassword') !== null)
```

## Field lifecycle

### Hydration

Hydration is the process which fill fields with data. It runs when you call the [form's `fill()` method](getting-started#filling-forms-with-data). You may customize what happens after a field is hydrated using the `afterStateHydrated()`.

In this example, the `name` field will always be hydrated with the correctly capitalized name:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->afterStateHydrated(function (TextInput $component, callable $set, $state) {
        $set($component, ucwords($state));
    })
```

### Updates

You may use the `afterStateHydrated()` method to customize what happens after a field is updated.

In this example, the `slug` field is updated with the slug version of the `title` field automatically:

```php
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

TextInput::make('title')
    ->reactive()
    ->afterStateUpdated(function (callable $set, $state) {
        $set('slug', Str::slug($state));
    })
TextInput::make('slug')
```

### Dehydration

Hydration is the process which gets data from fields, and transforms it. It runs when you call the [form's `getState()` method](getting-started#getting-data-from-forms). You may customize how the state is dehydrated from the form by returning the transformed state from the `afterStateHydrated()` callback.

In this example, the `name` field will always be dehydrated with the correctly capitalized name:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->dehydrateStateUsing(fn ($state) => ucwords($state))
```

You may also prevent the field from being dehydrated altogether by passing `false` to `dehydrated()`.

In this example, the `passwordConfirmation` field will not be present in the array returned from `getData()`:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('passwordConfirmation')
    ->password()
    ->dehydrated(false)
```
