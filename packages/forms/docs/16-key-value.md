---
title: Key-value
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

The key-value field allows you to interact with one-dimensional JSON object:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
```

<AutoScreenshot name="forms/fields/key-value/simple" alt="Key-value" version="4.x" />

If you're saving the data in Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    { 
        return [
            'meta' => 'array',
        ];
    }

    // ...
}
```

## Adding rows

An action button is displayed below the field to allow the user to add a new row.

## Setting the add action button's label

You may set a label to customize the text that should be displayed in the button for adding a row, using the `addActionLabel()` method:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->addActionLabel('Add property')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `addActionLabel()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Preventing the user from adding rows

You may prevent the user from adding rows using the `addable(false)` method:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->addable(false)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `addable()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Deleting rows

An action button is displayed on each item to allow the user to delete it.

### Preventing the user from deleting rows

You may prevent the user from deleting rows using the `deletable(false)` method:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->deletable(false)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `deletable()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Editing keys

### Customizing the key fields' label

You may customize the label for the key fields using the `keyLabel()` method:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->keyLabel('Property name')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `keyLabel()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Adding key field placeholders

You may also add placeholders for the key fields using the `keyPlaceholder()` method:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->keyPlaceholder('Property name')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `keyPlaceholder()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Preventing the user from editing keys

You may prevent the user from editing keys using the `editableKeys(false)` method:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->editableKeys(false)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `editableKeys()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Editing values

### Customizing the value fields' label

You may customize the label for the value fields using the `valueLabel()` method:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->valueLabel('Property value')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `valueLabel()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Adding value field placeholders

You may also add placeholders for the value fields using the `valuePlaceholder()` method:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->valuePlaceholder('Property value')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `valuePlaceholder()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Preventing the user from editing values

You may prevent the user from editing values using the `editableValues(false)` method:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->editableValues(false)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `editableValues()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Reordering rows

You can allow the user to reorder rows within the table using the `reorderable()` method:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->reorderable()
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `reorderable()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/key-value/reorderable" alt="Key-value with reorderable rows" version="4.x" />

## Customizing the key-value action objects

This field uses action objects for easy customization of buttons within it. You can customize these buttons by passing a function to an action registration method. The function has access to the `$action` object, which you can use to [customize it](../actions/overview). The following methods are available to customize the actions:

- `addAction()`
- `deleteAction()`
- `reorderAction()`

Here is an example of how you might customize an action:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Support\Icons\Heroicon;

KeyValue::make('meta')
    ->deleteAction(
        fn (Action $action) => $action->icon(Heroicon::XMark),
    )
```

<UtilityInjection set="formFields" version="4.x" extras="Action;;Filament\Actions\Action;;$action;;The action object to customize.">The action registration methods can inject various utilities into the function as parameters.</UtilityInjection>
