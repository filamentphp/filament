---
title: Key-value
---

The key-value field allows you to interact with one-dimensional JSON object:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
```

![](https://user-images.githubusercontent.com/41773797/147614182-52756a59-a9c4-4371-ac61-cd77c977808e.png)

## Customizing the key and value field labels

You may customize the labels for the key and value fields using the `keyLabel()` and `valueLabel()` methods:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->keyLabel('Property name')
    ->valueLabel('Property value')
```

![](https://user-images.githubusercontent.com/41773797/147614196-27341757-b50e-45c2-8b40-728cb985d2cd.png)

## Adding key and value field placeholders

You may also add placeholders for the key and value fields using the `keyPlaceholder()` and `valuePlaceholder()` methods:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->keyPlaceholder('Property name')
    ->valuePlaceholder('Property value')
```

![](https://user-images.githubusercontent.com/41773797/147614208-936fa7ee-f719-466f-b7de-56ecc2558c0a.png)


## Disabling functionality

You may also prevent the user from adding rows, deleting rows, editing keys, or editing values:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->addable(false)
    ->deletable(false)
    ->editableKeys(false)
    ->editableValues(false)
```

## Reordering rows

You can allow the user to reorder rows within the table:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->reorderable()
```
