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

When fields fail validation, their label is used in the error message. To customise the label used in field error messages, use the `validationAttribute()` method:

```php
Field::make('name')->validationAttribute('full name')
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

Similar to a [repeater](#repeater), the builder component allows you to output a JSON array of repeated form components. Unlike the repeater, which only defines one form schema to repeat, the builder allows you to define different schema "blocks", which you can repeat in any order. This makes it useful for building more advanced array structures.

The primary use of the builder component is to build web page content using predefined blocks. The example below defines multiple blocks for different elements in the page content. On the frontend of your website, you could loop through each block in the JSON and format it how you wish.

```php
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

Builder::make('content')
    ->blocks([
        Builder\Block::make('heading')
            ->schema([
                TextInput::make('content')->required(),
                Select::make('level')
                    ->options([
                        'h1' => 'Heading 1',
                        'h2' => 'Heading 2',
                        'h3' => 'Heading 3',
                        'h4' => 'Heading 4',
                        'h5' => 'Heading 5',
                        'h6' => 'Heading 6',
                    ])
                    ->required(),
            ]),
        Builder\Block::make('paragraph')
            ->schema([
                MarkdownEditor::make('content')->required(),
            ]),
        Builder\Block::make('image')
            ->schema([
                FileUpload::make('url')
                    ->image()
                    ->required(),
                TextInput::make('alt')
                    ->label('Alt text')
                    ->required(),
            ]),
    ])
```

As evident in the above example, blocks can be defined within the `blocks()` method of the component. Blocks are `Builder\Block` objects, and require a unique name, and a component schema:

```php
use Filament\Forms\Components\Builder;

Builder::make('content')
    ->blocks([
        Builder\Block::make('heading')
            ->schema([
                TextInput::make('content')->required(),
                // ...
            ]),
        // ...
    ])
```

By default, the label of the block will be automatically determined based on its name. To override the block's label, you may use the `label()` method. Customizing the label in this way is useful if you wish to use a [translation string for localization](https://laravel.com/docs/localization#retrieving-translation-strings):

```php
use Filament\Forms\Components\Builder;

Builder\Block::make('heading')->label(__('blocks.heading'))
```

Blocks may also have an icon, which is displayed next to the label. The `icon()` method accepts the name of any Blade icon component.

```php
use Filament\Forms\Components\Builder;

Builder\Block::make('heading')->icon('heroicon-o-archive')
```

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
