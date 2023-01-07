---
title: Advanced
---

## Using closure customization

All configuration methods for [fields](fields) and [layout components](layout) accept closures as parameters instead of hardcoded values:

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

The package is also able to inject many utilities to use inside these closures, as parameters.

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

If you have defined a form or component Eloquent model instance, define a `$record` parameter:

```php
use Illuminate\Database\Eloquent\Model;

function (?Model $record) {
    // ...
}
```

You may also retrieve the value of another field from within a callback, using a `$get` parameter:

```php
use Filament\Forms\Get;

function (Get $get) {
    $email = $get('email'); // Store the value of the `email` field in the `$email` variable.
    //...
}
```

In a similar way to `$get`, you may also set the value of another field from within a callback, using a `$set` parameter:

```php
use Filament\Forms\Set;

function (Set $set) {
    $set('title', 'Blog Post'); // Set the `title` field to `Blog Post`.
    //...
}
```

If you're writing a form for an app framework resource or relation manager, and you wish to check if a form is `create`, `edit` or `view`, use the `$operation` parameter:

```php
function (string $operation) {
    // ...
}
```

> Outside of the app framework, you can set a form's context by using the `context()` method on your `$form` definition.

Callbacks are evaluated using Laravel's `app()->call()` under the hood, so you are able to combine multiple parameters in any order:

```php
use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Component as Livewire;

function (Livewire $livewire, Get $get, Set $set) {
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

## Dependant fields / components

You may use the techniques described in the [closure customization section](#using-closure-customization) to build completely dependent fields and components, with full control over customization based on the values of other fields in your form.

For example, you can build dependant [select](fields/select) inputs:

<iframe width="560" height="315" src="https://www.youtube.com/embed/W_eNyimRi3w" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

Sometimes, you may wish to conditionally hide any form component based on the value of a field. You may do this with a `hidden()` method:

```php
use Filament\Forms\Get;
use Filament\Forms\Components\TextInput;

TextInput::make('newPassword')
    ->password()
    ->reactive()

TextInput::make('newPasswordConfirmation')
    ->password()
    ->hidden(fn (Get $get) => $get('newPassword') !== null)
```

The field/s you're depending on should be `reactive()`, to ensure the Livewire component is reloaded when they are updated.

## Field lifecycle

### Hydration

Hydration is the process which fill fields with data. It runs when you call the [form's `fill()` method](getting-started#filling-forms-with-data). You may customize what happens after a field is hydrated using the `afterStateHydrated()`.

In this example, the `name` field will always be hydrated with the correctly capitalized name:

```php
use Closure;
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->afterStateHydrated(function (TextInput $component, $state) {
        $component->state(ucwords($state));
    })
```

### Updates

You may use the `afterStateUpdated()` method to customize what happens after a field is updated.

In this example, the `slug` field is updated with the slug version of the `title` field automatically:

```php
use Filament\Forms\Set;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

TextInput::make('title')
    ->reactive()
    ->afterStateUpdated(function (Set $set, $state) {
        $set('slug', Str::slug($state));
    })
TextInput::make('slug')
```

### Dehydration

Dehydration is the process which gets data from fields, and transforms it. It runs when you call the [form's `getState()` method](getting-started#getting-data-from-forms). You may customize how the state is dehydrated from the form by returning the transformed state from the `dehydrateStateUsing()` callback.

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

## Using form events

Forms can dispatch and listen to events, which allow the frontend and backend to communicate.

These events can be dispatched in a component view, and then listened to by a component's class.

### Dispatching events

To dispatch a form event, call the `dispatchFormEvent()` Livewire method with the event name:

```blade
<button wire:click="dispatchFormEvent('save')">
    Save
</button>
```

Usually, you will want to dispatch events for a specific component class. In this case, you should prefix the event name with the component name, and pass the component's state path as a second parameter:

```blade
<button wire:click="dispatchFormEvent('repeater::createItem', '{{ $getStatePath() }}')">
    Add item
</button>
```

You may also pass other parameters to the event:

```blade
<button wire:click="dispatchFormEvent('repeater::delete', '{{ $getStatePath() }}', '{{ $uuid }}')">
    Delete item
</button>
```

### Listening to events

You may register listeners for form events by calling the `registerListeners()` method when your component is `setUp()`:

```php
use Filament\Forms\Components\Component;

protected function setUp(): void
{
    parent::setUp();

    $this->registerListeners([
        'save' => [
            function (Component $component): void {
                // ...
            },
        ],
    ]);
}
```

If your event is component-specific, you'll want to ensure that the component is not disabled, and that the component's state path matches the event's second parameter:

```php
use Filament\Forms\Components\Component;

protected function setUp(): void
{
    parent::setUp();

    $this->registerListeners([
        'repeater::createItem' => [
            function (Component $component, string $statePath): void {
                if ($component->isDisabled()) {
                    return;
                }

                if ($statePath !== $component->getStatePath()) {
                    return;
                }

                // ...
            },
        ],
    ]);
}
```

Additionally, you may receive other parameters from the event:

```php
use Filament\Forms\Components\Component;

protected function setUp(): void
{
    parent::setUp();

    $this->registerListeners([
        'repeater::delete' => [
            function (Component $component, string $statePath, string $uuidToDelete): void {
                if ($component->isDisabled()) {
                    return;
                }

                if ($statePath !== $component->getStatePath()) {
                    return;
                }

                // Delete item with UUID `$uuidToDelete`
            },
        ],
    ]);
}
```
