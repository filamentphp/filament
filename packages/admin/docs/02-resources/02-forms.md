---
title: Forms
---

Resource classes contain a static `form()` method that is used to build the forms on the create and edit pages:

```php
use Filament\Forms;
use Filament\Resources\Form;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
            // ...
        ]);
}
```

### Available field types

The `schema()` method is used to define the structure of your form. It is an array of [fields](/docs/forms/fields), in the order they should appear in your form.

We have many fields available for your forms, including:

- [Text input](/docs/forms/fields#text-input)
- [Select](/docs/forms/fields#select)
- [Multi-select](/docs/forms/fields#multi-select)
- [Checkbox](/docs/forms/fields#checkbox)
- [Date-time picker](/docs/forms/fields#date-time-picker)
- [File upload](/docs/forms/fields#file-upload)
- [Rich editor](/docs/forms/fields#rich-editor)
- [Markdown editor](/docs/forms/fields#markdown-editor)
- [Repeater](/docs/forms/fields#repeater)

To view a full list of available form [fields](/docs/forms/fields) and [layout components](/docs/forms/layout), see the [Form Builder documentation](/docs/forms/fields).

You may also build your own completely [custom form fields](/docs/forms/fields#building-custom-fields) and [custom layout components](/docs/forms/layout#building-custom-layout-components).

### Automatically generating fields

If you'd like to save time, Filament can automatically generate some fields and [tables](#tables) for you, based on your model's database columns:

```bash
composer require doctrine/dbal
php artisan make:filament-resource Customer --generate
```

### Hiding components based on the page

The `hidden()` method of form components allows you to dynamically hide fields based on the current page.

To do this, you must pass a closure to the `hidden()` method which checks if the Livewire component is a certain page or not. In this example, we hide the `password` field on the `EditUser` resource page:

```php
use Livewire\Component;

Forms\Components\TextInput::make('password')
    ->password()
    ->required()
    ->hidden(fn (Component $livewire): bool => $livewire instanceof Pages\EditUser),
```

Alternatively, we have a `hiddenOn()` shortcut method for this case:

```php
use Livewire\Component;

Forms\Components\TextInput::make('password')
    ->password()
    ->required()
    ->hiddenOn(Pages\EditUser::class),
```

You may instead use the `visible` to check if a component should be visible or not:

```php
use Livewire\Component;

Forms\Components\TextInput::make('password')
    ->password()
    ->required()
    ->visible(fn (Component $livewire): bool => $livewire instanceof Pages\CreateUser),
```

Alternatively, we have a `visibleOn()` shortcut method for this case:

```php
use Livewire\Component;

Forms\Components\TextInput::make('password')
    ->password()
    ->required()
    ->visibleOn(Pages\CreateUser::class),
```

For more information about closure customization, see the [form builder documentation](/docs/forms/advanced#using-closure-customisation).
