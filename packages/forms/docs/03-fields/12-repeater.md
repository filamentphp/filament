---
title: Repeater
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The repeater component allows you to output a JSON array of repeated form components.

```php
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

Repeater::make('members')
    ->schema([
        TextInput::make('name')->required(),
        Select::make('role')
            ->options([
                'member' => 'Member',
                'administrator' => 'Administrator',
                'owner' => 'Owner',
            ])
            ->required(),
    ])
    ->columns(2)
```

<AutoScreenshot name="forms/fields/repeater/simple" alt="Repeater" version="3.x" />

We recommend that you store repeater data with a `JSON` column in your database. Additionally, if you're using Eloquent, make sure that column has an `array` cast.

As evident in the above example, the component schema can be defined within the `schema()` method of the component:

```php
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

Repeater::make('members')
    ->schema([
        TextInput::make('name')->required(),
        // ...
    ])
```

If you wish to define a repeater with multiple schema blocks that can be repeated in any order, please use the [builder](builder).

## Setting empty default items

Repeaters may have a certain number of empty items created by default, using the `defaultItems()` method:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('members')
    ->schema([
        // ...
    ])
    ->defaultItems(3)
```

Note that these default items are only created when the form is loaded without existing data. Inside [panel resources](../../panels/resources#resource-forms) this only works on Create Pages, as Edit Pages will always fill the data from the model.

## Adding items

An action button is displayed below the repeater to allow the user to add a new item.

## Setting the add action button's label

You may set a label to customize the text that should be displayed in the button for adding a repeater item, using the `addActionLabel()` method:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('members')
    ->schema([
        // ...
    ])
    ->addActionLabel('Add member')
```

### Preventing the user from adding items

You may prevent the user from adding items to the repeater using the `addable(false)` method:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('members')
    ->schema([
        // ...
    ])
    ->addable(false)
```

## Deleting items

An action button is displayed on each item to allow the user to delete it.

### Preventing the user from deleting items

You may prevent the user from deleting items from the repeater using the `deletable(false)` method:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('members')
    ->schema([
        // ...
    ])
    ->deletable(false)
```

## Reordering items

A button is displayed on each item to allow the user to drag and drop to reorder it in the list.

### Preventing the user from reordering items

You may prevent the user from reordering items from the repeater using the `reorderable(false)` method:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('members')
    ->schema([
        // ...
    ])
    ->reorderable(false)
```

### Reordering items with buttons instead of drag and drop

You may use the `reorderableWithButtons()` method to enable reordering items with buttons instead of drag and drop:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('members')
    ->schema([
        // ...
    ])
    ->reorderableWithButtons()
```

<AutoScreenshot name="forms/fields/repeater/reorderable-with-buttons" alt="Repeater that is reorderable with buttons" version="3.x" />

## Collapsing items

The repeater may be `collapsible()` to optionally hide content in long forms:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->schema([
        // ...
    ])
    ->collapsible()
```

You may also collapse all items by default:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->schema([
        // ...
    ])
    ->collapsed()
```

<AutoScreenshot name="forms/fields/repeater/collapsed" alt="Collapsed repeater" version="3.x" />

## Cloning items

You may allow repeater items to be duplicated using the `cloneable()` method:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->schema([
        // ...
    ])
    ->cloneable()
```

<AutoScreenshot name="forms/fields/repeater/cloneable" alt="Cloneable repeater" version="3.x" />

## Integrating with an Eloquent relationship

> If you're building a form inside your Livewire component, make sure you have set up the [form's model](../adding-a-form-to-a-livewire-component#setting-a-form-model). Otherwise, Filament doesn't know which model to use to retrieve the relationship from.

You may employ the `relationship()` method of the `Repeater` to configure a `HasMany` relationship. Filament will load the item data from the relationship, and save it back to the relationship when the form is submitted. If a custom relationship name is not passed to `relationship()`, Filament will use the field name as the relationship name:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->relationship()
    ->schema([
        // ...
    ])
```

### Reordering items in a relationship

By default, [reordering](#reordering-items) relationship repeater items is disabled. This is because your related model needs an `sort` column to store the order of related records. To enable reordering, you may use the `orderColumn()` method, passing in a name of the column on your related model to store the order in:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->relationship()
    ->schema([
        // ...
    ])
    ->orderColumn('sort')
```

If you use something like [`spatie/eloquent-sortable`](https://github.com/spatie/eloquent-sortable) with an order column such as `order_column`, you may pass this in to `orderColumn()`:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->relationship()
    ->schema([
        // ...
    ])
    ->orderColumn('order_column')
```

### Mutating related item data before filling the field

You may mutate the data for a related item before it is filled into the field using the `mutateRelationshipDataBeforeFillUsing()` method. This method accepts a closure that receives the current item's data in a `$data` variable. You must return the modified array of data:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->relationship()
    ->schema([
        // ...
    ])
    ->mutateRelationshipDataBeforeFillUsing(fn (array $data): array {
        $data['user_id'] = auth()->id();

        return $data;
    })
```

### Mutating related item data before creating

You may mutate the data for a new related item before it is created in the database using the `mutateRelationshipDataBeforeCreateUsing()` method. This method accepts a closure that receives the current item's data in a `$data` variable. You must return the modified array of data:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->relationship()
    ->schema([
        // ...
    ])
    ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array {
        $data['user_id'] = auth()->id();

        return $data;
    })
```

### Mutating related item data before saving

You may mutate the data for an existing related item before it is saved in the database using the `mutateRelationshipDataBeforeSaveUsing()` method. This method accepts a closure that receives the current item's data in a `$data` variable. You must return the modified array of data:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->relationship()
    ->schema([
        // ...
    ])
    ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array {
        $data['user_id'] = auth()->id();

        return $data;
    })
```

## Grid layout

You may organize repeater items into columns by using the `grid()` method:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->schema([
        // ...
    ])
    ->grid(2)
```

<AutoScreenshot name="forms/fields/repeater/grid" alt="Repeater with a 2 column grid of items" version="3.x" />

This method accepts the same options as the `columns()` method of the [grid](../layout/grid). This allows you to responsively customize the number of grid columns at various breakpoints.

## Adding a label to repeater items based on their content

You may add a label for repeater items using the `itemLabel()` method. This method accepts a closure that receives the current item's data in a `$state` variable. You must return a string to be used as the item label:

```php
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

Repeater::make('members')
    ->schema([
        TextInput::make('name')
            ->required()
            ->lazy(),
        Select::make('role')
            ->options([
                'member' => 'Member',
                'administrator' => 'Administrator',
                'owner' => 'Owner',
            ])
            ->required(),
    ])
    ->columns(2)
    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
```

<AutoScreenshot name="forms/fields/repeater/labelled" alt="Repeater with item labels" version="3.x" />

Any fields that you use from `$state` should be `reactive()` or `lazy()` if you wish to see the item label update live as you use the form.

## Using `$get()` to access parent field values

All form components are able to [use `$get()` and `$set()`](../advanced) to access another field's value. However, you might experience unexpected behaviour when using this inside the repeater's schema.

This is because `$get()` and `$set()`, by default, are scoped to the current repeater item. This means that you are able to interact with another field inside that repeater item easily without knowing which repeater item the current form component belongs to.

The consequence of this, is that you may be confused when you are unable to interact with a field outside the repeater. We use `../` syntax to solve this problem - `$get('../../parent_field_name')`.

Consider your form has this data structure:

```php
[
    'client_id' => 1,

    'repeater' => [
        'item1' => [
            'service_id' => 2,
        ],
    ],
]
```

You are trying to retrieve the value of `client_id` from inside the repeater item.

`$get()` is relative to the current repeater item, so `$get('client_id')` is looking for `$get('repeater.item1.client_id')`.

You can use `../` to go up a level in the data structure, so `$get('../client_id')` is `$get('repeater.client_id')` and `$get('../../client_id')` is `$get('client_id')`.

## Enabling the "inset" design

As part of Filament's design system, you can enable "inset" mode for a repeater with the `inset()`. This will give the repeater extra padding around the outside of the items, with a background color:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('members')
    ->schema([
        // ...
    ])
    ->inset()
```

<AutoScreenshot name="forms/fields/repeater/inset" alt="Repeater with inset design" version="3.x" />

## Repeater validation

As well as all rules listed on the [validation](../validation) page, there are additional rules that are specific to repeaters.

### Number of items validation

You can validate the minimum and maximum number of items that you can have in a repeater by setting the `minItems()` and `maxItems()` methods:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('members')
    ->schema([
        // ...
    ])
    ->minItems(2)
    ->maxItems(5)
```
