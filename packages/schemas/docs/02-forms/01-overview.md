---
title: Overview
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Field classes can be found in the `Filament\Form\Components` namespace.

Fields reside within the schema of your form, alongside any [layout components](../schemas/layout).

Fields may be created using the static `make()` method, passing its unique name. The name of the field should correspond to a property on your Livewire component. You may use "dot notation" to bind fields to keys in arrays.

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
```

<AutoScreenshot name="forms/fields/simple" alt="Form field" version="4.x" />

## Available fields

Filament ships with many types of field, suitable for editing different types of data:

- [Text input](text-input)
- [Select](select)
- [Checkbox](checkbox)
- [Toggle](toggle)
- [Checkbox list](checkbox-list)
- [Radio](radio)
- [Date-time picker](date-time-picker)
- [File upload](file-upload)
- [Rich editor](rich-editor)
- [Markdown editor](markdown-editor)
- [Repeater](repeater)
- [Builder](builder)
- [Tags input](tags-input)
- [Textarea](textarea)
- [Key-value](key-value)
- [Color picker](color-picker)
- [Toggle buttons](toggle-buttons)
- [Hidden](hidden)

You may also [create your own custom fields](custom) to edit data however you wish.

## Setting a label

By default, the label of the field will be automatically determined based on its name. To override the field's label, you may use the `label()` method. Customizing the label in this way is useful if you wish to use a [translation string for localization](https://laravel.com/docs/localization#retrieving-translation-strings):

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->label(__('fields.name'))
```

Optionally, you can have the label automatically translated [using Laravel's localization features](https://laravel.com/docs/localization) with the `translateLabel()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->translateLabel() // Equivalent to `label(__('Name'))`
```

## Setting an ID

In the same way as labels, field IDs are also automatically determined based on their names. To override a field ID, use the `id()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->id('name-field')
```

## Setting a default value

Fields may have a default value. This will be filled if the form's `fill()` method is called without any arguments. To define a default value, use the `default()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->default('John')
```

Note that these defaults are only used when the form is loaded without existing data. Inside [panel resources](../../panels/resources#resource-forms) this only works on Create Pages, as Edit Pages will always fill the data from the model.

## Adding extra HTML attributes

You can pass extra HTML attributes to the field, which will be merged onto the outer DOM element. Pass an array of attributes to the `extraAttributes()` method, where the key is the attribute name and the value is the attribute value:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->extraAttributes(['title' => 'Text input'])
```

Some fields use an underlying `<input>` or `<select>` DOM element, but this is often not the outer element in the field, so the `extraAttributes()` method may not work as you wish. In this case, you may use the `extraInputAttributes()` method, which will merge the attributes onto the `<input>` or `<select>` element:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('categories')
    ->extraInputAttributes(['width' => 200])
```

You can also pass extra HTML attributes to the field wrapper which surrounds the label, entry, and any other text:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('categories')
    ->extraFieldWrapperAttributes(['class' => 'components-locked'])
```

## Disabling a field

You may disable a field to prevent it from being edited by the user:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->disabled()
```

<AutoScreenshot name="forms/fields/disabled" alt="Disabled form field" version="4.x" />

Optionally, you may pass a boolean value to control if the field should be disabled or not:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')
    ->disabled(! auth()->user()->isAdmin())
```

Disabling a field will prevent it from being saved. If you'd like it to be saved, but still not editable, use the `dehydrated()` method:

```php
Toggle::make('is_admin')
    ->disabled()
    ->dehydrated()
```

> If you choose to dehydrate the field, a skilled user could still edit the field's value by manipulating Livewire's JavaScript.

### Hiding a field

You may hide a field:

 ```php
 use Filament\Forms\Components\TextInput;

 TextInput::make('name')
    ->hidden()
 ```

Optionally, you may pass a boolean value to control if the field should be hidden or not:

 ```php
 use Filament\Forms\Components\TextInput;

 TextInput::make('name')
    ->hidden(! auth()->user()->isAdmin())
 ```

## Autofocusing a field when the form is loaded

Most fields are autofocusable. Typically, you should aim for the first significant field in your form to be autofocused for the best user experience. You can nominate a field to be autofocused using the `autofocus()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->autofocus()
```

## Setting a placeholder

Many fields will also include a placeholder value for when it has no value. This is displayed in the UI but not saved if the field is submitted with no value. You may customize this placeholder using the `placeholder()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->placeholder('John Doe')
```

<AutoScreenshot name="forms/fields/placeholder" alt="Form field with placeholder" version="4.x" />

## Marking a field as required

By default, [required fields](validation#required) will show an asterisk `*` next to their label. You may want to hide the asterisk on forms where all fields are required, or where it makes sense to add a [hint](#adding-a-hint-next-to-the-label) to optional fields instead:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->required() // Adds validation to ensure the field is required
    ->markAsRequired(false) // Removes the asterisk
```

If your field is not `required()`, but you still wish to show an asterisk `*` you can use `markAsRequired()` too:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->markAsRequired()
```

## Validating fields

In Laravel, validation rules are usually defined in arrays like `['required', 'max:255']` or a combined string like `required|max:255`. This is fine if you're exclusively working in the backend with simple form requests. But Filament is also able to give your users frontend validation, so they can fix their mistakes before any backend requests are made.

In Filament, you can add validation rules to your fields by using methods like `required()` and `maxLength()`. This is also advantageous over Laravel's validation syntax, since your IDE can autocomplete these methods:

```php
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

[
    TextInput::make('title')
        ->required()
        ->maxLength(255),
    TextInput::make('slug')
        ->required()
        ->maxLength(255),
    RichEditor::make('content')
        ->columnSpan(2)
        ->maxLength(65535),
    Section::make('Publishing')
        ->description('Settings for publishing this post.')
        ->schema([
            Select::make('status')
                ->options([
                    'draft' => 'Draft',
                    'reviewing' => 'Reviewing',
                    'published' => 'Published',
                ])
                ->required(),
            DateTimePicker::make('published_at'),
        ]),
]
```

In this example, some fields are `required()`, and some have a `maxLength()`. We have [methods for most of Laravel's validation rules](validation#available-rules), and you can even add your own [custom rules](validation#custom-rules).

## Dependant fields

Since all Filament forms are built on top of Livewire, form schemas are completely dynamic. There are so many possibilities, but here are a couple of examples of how you can use this to your advantage:

Fields can hide or show based on another field's values. In our form, we can hide the `published_at` timestamp field until the `status` field is set to `published`. This is done by passing a closure to the `hidden()` method, which allows you to dynamically hide or show a field while the form is being used. Closures have access to many useful arguments like `$get`, and you can find a [full list here](advanced#form-component-utility-injection). The field that you depend on (the `status` in this case) needs to be set to `live()`, which tells the form to reload the schema each time it gets changed.

```php
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;

[
    Select::make('status')
        ->options([
            'draft' => 'Draft',
            'reviewing' => 'Reviewing',
            'published' => 'Published',
        ])
        ->required()
        ->live(),
    DateTimePicker::make('published_at')
        ->hidden(fn (Get $get) => $get('status') !== 'published'),
]
```

It's not just `hidden()` - all Filament form methods support closures like this. You can use them to change the label, placeholder, or even the options of a field, based on another. You can even use them to add new fields to the form, or remove them. This is a powerful tool that allows you to create complex forms with minimal effort.

Fields can also write data to other fields. For example, we can set the title to automatically generate a slug when the title is changed. This is done by passing a closure to the `afterStateUpdated()` method, which gets run each time the title is changed. This closure has access to the title (`$state`) and a function (`$set`) to set the slug field's state. You can find a [full list of closure arguments here](advanced#form-component-utility-injection). The field that you depend on (the `title` in this case) needs to be set to `live()`, which tells the form to reload and set the slug each time it gets changed.

```php
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

[
    TextInput::make('title')
        ->required()
        ->maxLength(255)
        ->live()
        ->afterStateUpdated(function (Set $set, $state) {
            $set('slug', Str::slug($state));
        }),
    TextInput::make('slug')
        ->required()
        ->maxLength(255),
]
```

## Global settings

If you wish to change the default behavior of a field globally, then you can call the static `configureUsing()` method inside a service provider's `boot()` method or a middleware. Pass a closure which is able to modify the component. For example, if you wish to make all [checkboxes `inline(false)`](checkbox#positioning-the-label-above), you can do it like so:

```php
use Filament\Forms\Components\Checkbox;

Checkbox::configureUsing(function (Checkbox $checkbox): void {
    $checkbox->inline(false);
});
```

Of course, you are still able to overwrite this behavior on each field individually:

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('is_admin')
    ->inline()
```
