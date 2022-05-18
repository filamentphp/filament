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

## Fields

The `schema()` method is used to define the structure of your form. It is an array of [fields](../../forms/fields), in the order they should appear in your form.

We have many fields available for your forms, including:

- [Text input](../../forms/fields#text-input)
- [Select](../../forms/fields#select)
- [Multi-select](../../forms/fields#multi-select)
- [Checkbox](../../forms/fields#checkbox)
- [Date-time picker](../../forms/fields#date-time-picker)
- [File upload](../../forms/fields#file-upload)
- [Rich editor](../../forms/fields#rich-editor)
- [Markdown editor](../../forms/fields#markdown-editor)
- [Repeater](../../forms/fields#repeater)

To view a full list of available form [fields](../../forms/fields), see the [Form Builder documentation](../../forms/fields).

You may also build your own completely [custom form fields](../../forms/fields#building-custom-fields).

## Layout

Form layouts are completely customizable. We have many layout components available, which can be used in any combination:

- [Grid](../../forms/layout#grid)
- [Card](../../forms/layout#card)
- [Tabs](../../forms/layout#tabs)

To view a full list of available [layout components](../../forms/layout), see the [Form Builder documentation](../../forms/layout).

You may also build your own completely [custom layout components](../../forms/layout#building-custom-layout-components).

## Hiding components based on the page

The `hiddenOn()` method of form components allows you to dynamically hide fields based on the current page.

In this example, we hide the `password` field on the `EditUser` resource page:

```php
use Livewire\Component;

Forms\Components\TextInput::make('password')
    ->password()
    ->required()
    ->hiddenOn(Pages\EditUser::class),
```

Alternatively, we have a `visibleOn()` shortcut method for only showing a field on one page:

```php
use Livewire\Component;

Forms\Components\TextInput::make('password')
    ->password()
    ->required()
    ->visibleOn(Pages\CreateUser::class),
```
