---
title: Getting started
---

Field classes can be found in the `Filament\Form\Components` namespace.

Fields reside within the schema of your form, alongside any [layout components](layout).

Fields may be created using the static `make()` method, passing its name. The name of the field should correspond to a property on your Livewire component. You may use [Livewire's "dot notation"](https://laravel-livewire.com/docs/properties#binding-nested-data) to bind fields to nested properties such as arrays and Eloquent models.

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
```

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
- [Hidden](hidden)

You may also [create your own custom fields](custom) to edit data however you wish.

## Setting a label

By default, the label of the field will be automatically determined based on its name. To override the field's label, you may use the `label()` method. Customizing the label in this way is useful if you wish to use a [translation string for localization](https://laravel.com/docs/localization#retrieving-translation-strings):

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->label(__('fields.name'))
```

Optionally, you can have the label automatically translated by using the `translateLabel()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->translateLabel() // Equivalent to `label(__('Name'))`
```

## Setting an ID

In the same way as labels, field IDs are also automatically determined based on their names. To override a field ID, use the `id()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->id('name-field')
```

## Setting a default value

Fields may have a default value. This will be filled if the [form's `fill()` method](getting-started#default-data) is called without any arguments. To define a default value, use the `default()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->default('John')
```

Note that inside the app framework this only works on Create Pages, as Edit Pages will always fill the data from the model.

## Helper messages and hints

Sometimes, you may wish to provide extra information for the user of the form. For this purpose, you may use helper messages and hints.

Help messages are displayed below the field. The `helperText()` method supports Markdown formatting:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->helperText('Your full name here, including any middle names.')
```

Hints can be used to display text adjacent to its label:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('password')->hint('[Forgotten your password?](forgotten-password)')
```

Hints may also have an icon rendered next to them:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->hint('Translatable')
    ->hintIcon('heroicon-m-language')
```

Hints may have a `color()`. By default it's gray, but you may use `primary`, `success`, `warning`, or `danger`:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->hint('Translatable')
    ->hintColor('primary')
```

## Custom attributes

The HTML attributes of the field's wrapper can be customized by passing an array of `extraAttributes()`:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->extraAttributes(['title' => 'Text input'])
```

To add additional HTML attributes to the input itself, use `extraInputAttributes()`:

```php
use Filament\Forms\Components\Select;

Select::make('categories')
    ->extraInputAttributes(['multiple' => true])
```

## Disabling

You may disable a field to prevent it from being edited:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->disabled()
```

Optionally, you may pass a boolean value to control if the field should be disabled or not:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')->disabled(! auth()->user()->isAdmin())
```

Please note that disabling a field does not prevent it from being saved, and a skillful user could manipulate the HTML of the page and alter its value.

To prevent a field from being saved, use the `dehydrated(false)` method:

```php
Toggle::make('is_admin')->dehydrated(false)
```

Alternatively, you may only want to save a field conditionally, maybe if the user is an admin:

```php
Toggle::make('is_admin')
    ->disabled(! auth()->user()->isAdmin())
    ->dehydrated(auth()->user()->isAdmin())
```

If you're using the [app framework](../../app) and only want to save disabled fields on the [Create page of a resource](/docs/admin/resources):

```php
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;

TextInput::make('slug')
    ->disabled()
    ->dehydrated(fn (Page $livewire) => $livewire instanceof CreateRecord)
```

### Hiding

You may hide a field:

 ```php
 use Filament\Forms\Components\TextInput;
 
 TextInput::make('name')->hidden()
 ```

Optionally, you may pass a boolean value to control if the field should be hidden or not:

 ```php
 use Filament\Forms\Components\TextInput;
 
 TextInput::make('name')->hidden(! auth()->user()->isAdmin())
 ```

## Autofocusing

Most fields will be autofocusable. Typically, you should aim for the first significant field in your form to be autofocused for the best user experience.

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->autofocus()
```

## Setting a placeholder

Many fields will also include a placeholder value for when it has no value. You may customize this using the `placeholder()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->placeholder('John Doe')
```

## Marking as required

By default, [required fields](validation#required) will show an asterisk (*) next to their label. You may want to hide the asterisk on forms with only required fields, or where it makes sense to add a [hint](#helper-messages-and-hints) to optional fields instead:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->required()
    ->markAsRequired(false)
```

## Global settings

If you wish to change the default behaviour of a field globally, then you can call the static `configureUsing()` method inside a service provider's `boot()` method, to which you pass a Closure to modify the component using. For example, if you wish to make all checkboxes [`inline(false)`](checkbox), you can do it like so:

```php
use Filament\Forms\Components\Checkbox;

Checkbox::configureUsing(function (Checkbox $checkbox): void {
    $checkbox->inline(false);
});
```

Of course, you are still able to overwrite this on each field individually:

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('is_admin')->inline()
```
