---
title: Checkbox list
---

The checkbox list component allows you to select multiple values from a list of predefined options:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->options([
        'tailwind' => 'Tailwind CSS',
        'alpine' => 'Alpine.js',
        'laravel' => 'Laravel',
        'livewire' => 'Laravel Livewire',
    ])
```

![](https://user-images.githubusercontent.com/41773797/147761423-bd6e99ec-104d-40c2-875a-fed1d638f2c3.png)

These options are returned in JSON format. If you're saving them using Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    protected $casts = [
        'technologies' => 'array',
    ];

    // ...
}
```

## Splitting options into columns

You may split options into columns by using the `columns()` method:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->options([
        'tailwind' => 'Tailwind CSS',
        'alpine' => 'Alpine.js',
        'laravel' => 'Laravel',
        'livewire' => 'Laravel Livewire',
    ])
    ->columns(2)
```

![](https://user-images.githubusercontent.com/41773797/147761438-558a9dcc-b81a-4fbe-a922-a6771407d928.png)

This method accepts the same options as the `columns()` method of the [grid](layout/grid). This allows you to responsively customize the number of columns at various breakpoints.

## Bulk toggling checkboxes

You may allow users to toggle all checkboxes at once using the `bulkToggleable()` method:

 ```php
 use Filament\Forms\Components\CheckboxList;
 
 CheckboxList::make('technologies')
     ->options([
         'tailwind' => 'Tailwind CSS',
         'alpine' => 'Alpine.js',
         'laravel' => 'Laravel',
         'livewire' => 'Laravel Livewire',
     ])
     ->bulkToggleable()
 ```

## Populating automatically from a relationship

You may employ the `relationship()` method to configure a relationship to automatically retrieve and save options from:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->relationship('technologies', 'name')
```

> To set this functionality up, **you must also follow the instructions set out in the [field relationships](getting-started#field-relationships) section**. If you're using the [app framework](../../app), you can skip this step.

You may customize the database query that retrieves options using the third parameter of the `relationship()` method:

```php
use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\Builder;

CheckboxList::make('technologies')
    ->relationship('technologies', 'name', fn (Builder $query) => $query->withTrashed())
```

If you'd like to customize the label of each option, maybe to be more descriptive, or to concatenate a first and last name, you should use a virtual column in your database migration:

```php
$table->string('full_name')->virtualAs('concat(first_name, \' \', last_name)');
```

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('participants')
    ->relationship('participants', 'full_name')
```

Alternatively, you can use the `getOptionLabelFromRecordUsing()` method to transform the selected option's Eloquent model into a label. But please note, this is much less performant than using a virtual column:

```php
use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\Model;

CheckboxList::make('participants')
    ->relationship('participants', 'first_name')
    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
```
