---
title: Fields
---

Field classes can be found in the `Filament\Form\Components` namespace.

Fields may be created using the static `make()` method, passing its name. The name of the field should correspond to a property on your Livewire component. You may use [Livewire's "dot syntax"](https://laravel-livewire.com/docs/properties#binding-nested-data) to bind fields to nested properties such as arrays and Eloquent models.

```php
Field::make('name')
```

By default, the label of the field will be automatically determined based on its name. To override the field's label, you may use the `label()` method. Customizing the label in this way is useful if you wish to use a [translation string for localization](https://laravel.com/docs/localization#retrieving-translation-strings):

```php
Field::make('name')->label(__('fields.name'))
```

In the same way as labels, field IDs are also automatically determined based on their names. To override a field ID, use the `id()` method:

```php
Field::make('name')->id('name-field')
```

Fields may have a default value. This will be filled if the [form's `fill()` method](building-forms#default-data) is called without any arguments. To define a default value, use the `default()` method:

```php
Field::make('name')->default('Dan')
```

Sometimes, you may wish to provide extra information for the user of the form. For this purpose, you may use helper messages and hints.

Help messages are displayed below the field. The `helpMessage()` method supports Markdown formatting:

```php
Field::make('name')->helpMessage('Your full name here, including any middle names.')
```

Hints can be used to display text adjacent to its label:

```php
Field::make('password')->hint('[Forgotten your password?](forgotten-password)')
```

The HTML of fields can be customised even further, by passing an array of `extraAttributes()`:

```php
Field::make('name')->extraAttributes(['step' => 10])
```

You may disable a field to prevent it from being edited:

```php
Field::make('name')->disabled()
```

## Builder

## Checkbox

## Date picker

## File upload

## Hidden

## Markdown editor

## Repeater

## Rich editor

## Select

## Tags input

## Text input

## Toggle

## View

## Building custom fields
