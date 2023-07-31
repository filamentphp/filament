---
title: Fields
---

## Getting started

Field classes can be found in the `Filament\Form\Components` namespace.

Fields reside within the schema of your form, alongside any [layout components](layout).

If you're using the fields in a Livewire component, you can put them in the `getFormSchema()` method:

```php
protected function getFormSchema(): array
{
    return [
        // ...
    ];
}
```

If you're using them in admin panel resources or relation managers, you must put them in the `$form->schema()` method:

```php
public static function form(Form $form): Form
{
    return $form
        ->schema([
            // ...
        ]);
}
```

Fields may be created using the static `make()` method, passing its name. The name of the field should correspond to a property on your Livewire component. You may use [Livewire's "dot syntax"](https://laravel-livewire.com/docs/properties#binding-nested-data) to bind fields to nested properties such as arrays and Eloquent models.

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
```

### Setting a label

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

### Setting an ID

In the same way as labels, field IDs are also automatically determined based on their names. To override a field ID, use the `id()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->id('name-field')
```

### Setting a default value

Fields may have a default value. This will be filled if the [form's `fill()` method](getting-started#default-data) is called without any arguments. To define a default value, use the `default()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->default('John')
```

Note that inside the admin panel this only works on Create Pages, as Edit Pages will always fill the data from the model.

### Helper messages and hints

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
    ->hintIcon('heroicon-s-translate')
```

Hints may have a `color()`. By default it's gray, but you may use `primary`, `success`, `warning`, or `danger`:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->hint('Translatable')
    ->hintColor('primary')
```

### Custom attributes

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

### Disabling

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

If you're using the [admin panel](/docs/admin) and only want to save disabled fields on the [Create page of a resource](/docs/admin/resources):

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

### Autofocusing

Most fields will be autofocusable. Typically, you should aim for the first significant field in your form to be autofocused for the best user experience.

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->autofocus()
```

### Setting a placeholder

Many fields will also include a placeholder value for when it has no value. You may customize this using the `placeholder()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->placeholder('John Doe')
```

### Global settings

If you wish to change the default behaviour of a field globally, then you can call the static `configureUsing()` method inside a service provider's `boot()` method, to which you pass a Closure to modify the component using. For example, if you wish to make all checkboxes [`inline(false)`](#checkbox), you can do it like so:

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

## Text input

The text input allows you to interact with a string:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
```

![](https://user-images.githubusercontent.com/41773797/147612753-1d4514ea-dba9-4f5c-9efc-08f09608e90d.png)

You may set the type of string using a set of methods. Some, such as `email()`, also provide validation:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('text')
    ->email()
    ->numeric()
    ->password()
    ->tel()
    ->url()
```

You may instead use the `type()` method to pass another [HTML input type](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#input_types):

```php
use Filament\Forms\Components\TextInput;

TextInput::make('backgroundColor')->type('color')
```

You may limit the length of the input by setting the `minLength()` and `maxLength()` methods. These methods add both frontend and backend validation:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->minLength(2)
    ->maxLength(255)
```

You may also specify the exact length of the input by setting the `length()`. This method adds both frontend and backend validation:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('code')->length(8)
```

In addition, you may validate the minimum and maximum value of the input by setting the `minValue()` and `maxValue()` methods:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('number')
    ->numeric()
    ->minValue(1)
    ->maxValue(100)
```

You may set the autocomplete configuration for the text field using the `autocomplete()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('password')
    ->password()
    ->autocomplete('new-password')
```

As a shortcut for `autocomplete="off"`, you may `disableAutocomplete()`:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('password')
    ->password()
    ->disableAutocomplete()
```

For more complex autocomplete options, text inputs also support [datalists](#datalists).

#### Phone number validation

When using a `tel()` field, the value will be validated using: `/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/`.

If you wish to change that, then you can use the `telRegex()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('phone')
    ->tel()
    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
```

Alternatively, to customize the `telRegex()` across all fields, use a service provider:

```php
use Filament\Forms\Components\TextInput;

TextInput::configureUsing(function (TextInput $component): void {
    $component->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/');
});
```

### Affixes

You may place text before and after the input using the `prefix()` and `suffix()` methods:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('domain')
    ->url()
    ->prefix('https://')
    ->suffix('.com')
```

![](https://user-images.githubusercontent.com/41773797/147612784-5eb58d0f-5111-4db8-8f54-3b5c3e2cc80a.png)

You may place a icon before and after the input using the `prefixIcon()` and `suffixIcon()` methods:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('domain')
    ->url()
    ->prefixIcon('heroicon-s-external-link')
    ->suffixIcon('heroicon-s-external-link')
```

You may render an action before and after the input using the `prefixAction()` and `suffixAction()` methods:

```php
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;

TextInput::make('domain')
    ->suffixAction(fn (?string $state): Action =>
        Action::make('visit')
            ->icon('heroicon-s-external-link')
            ->url(
                filled($state) ? "https://{$state}" : null,
                shouldOpenInNewTab: true,
            ),
    )
```

### Input masking

Input masking is the practice of defining a format that the input value must conform to.

In Filament, you may interact with the `Mask` object in the `mask()` method to configure your mask:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->mask(fn (TextInput\Mask $mask) => $mask->pattern('+{7}(000)000-00-00'))
```

Under the hood, masking is powered by [`imaskjs`](https://imask.js.org). The vast majority of its masking features are also available in Filament. Reading their [guide](https://imask.js.org/guide.html) first, and then approaching the same task using Filament is probably the easiest option.

You may define and configure a [numeric mask](https://imask.js.org/guide.html#masked-number) to deal with numbers:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('number')
    ->numeric()
    ->mask(fn (TextInput\Mask $mask) => $mask
        ->numeric()
        ->decimalPlaces(2) // Set the number of digits after the decimal point.
        ->decimalSeparator(',') // Add a separator for decimal numbers.
        ->integer() // Disallow decimal numbers.
        ->mapToDecimalSeparator([',']) // Map additional characters to the decimal separator.
        ->minValue(1) // Set the minimum value that the number can be.
        ->maxValue(100) // Set the maximum value that the number can be.
        ->normalizeZeros() // Append or remove zeros at the end of the number.
        ->padFractionalZeros() // Pad zeros at the end of the number to always maintain the maximum number of decimal places.
        ->thousandsSeparator(','), // Add a separator for thousands.
    )
```

[Enum masks](https://imask.js.org/guide.html#enum) limit the options that the user can input:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('code')->mask(fn (TextInput\Mask $mask) => $mask->enum(['F1', 'G2', 'H3']))
```

[Range masks](https://imask.js.org/guide.html#masked-range) can be used to restrict input to a number range:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('code')->mask(fn (TextInput\Mask $mask) => $mask
    ->range()
    ->from(1) // Set the lower limit.
    ->to(100) // Set the upper limit.
    ->maxValue(100), // Pad zeros at the start of smaller numbers.
)
```

In addition to simple pattens, you may also define multiple [pattern blocks](https://imask.js.org/guide.html#masked-pattern):

```php
use Filament\Forms\Components\TextInput;

TextInput::make('cost')->mask(fn (TextInput\Mask $mask) => $mask
    ->patternBlocks([
        'money' => fn (Mask $mask) => $mask
            ->numeric()
            ->thousandsSeparator(',')
            ->decimalSeparator('.'),
    ])
    ->pattern('$money'),
)
```

There is also a `money()` method that is able to define easier formatting for currency inputs. This example, the symbol prefix is `€`, there is a `,` thousands separator, and two decimal places:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('cost')->mask(fn (TextInput\Mask $mask) => $mask->money(prefix: '€', thousandsSeparator: ',', decimalPlaces: 2))
```

You can also control whether the number is signed or not. While the default is to allow both negative and positive numbers, `isSigned: false` allows only positive numbers:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('cost')->mask(fn (TextInput\Mask $mask) => $mask->money(prefix: '€', thousandsSeparator: ',', decimalPlaces: 2, isSigned: false))
```

### Datalists

You may specify [datalist](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/datalist) options for a text input using the `datalist()` method:

```php
TextInput::make('manufacturer')
    ->datalist([
        'BWM',
        'Ford',
        'Mercedes-Benz',
        'Porsche',
        'Toyota',
        'Tesla',
        'Volkswagen',
    ])
```

![](https://user-images.githubusercontent.com/41773797/147612844-f46e113f-82b3-4675-9097-4d64a4315082.png)

Datalists provide autocomplete options to users when they use a text input. However, these are purely recommendations, and the user is still able to type any value into the input. If you're looking for strictly predefined options, check out [select fields](#select).

## Select

The select component allows you to select from a list of predefined options:

```php
use Filament\Forms\Components\Select;

Select::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
```

In the `options()` array, the array keys are saved, and the array values will be the label of each option in the dropdown.

![](https://user-images.githubusercontent.com/41773797/147612885-888dfd64-6256-482d-b4bc-840191306d2d.png)

You may enable a search input to allow easier access to many options, using the `searchable()` method:

```php
use Filament\Forms\Components\Select;

Select::make('authorId')
    ->label('Author')
    ->options(User::all()->pluck('name', 'id'))
    ->searchable()
```

![](https://user-images.githubusercontent.com/41773797/147613023-cb7d1907-e4d3-4a33-aa86-1c25d780c861.png)

If you have lots of options and want to populate them based on a database search or other external data source, you can use the `getSearchResultsUsing()` and `getOptionLabelUsing()` methods instead of `options()`.

The `getSearchResultsUsing()` method accepts a callback that returns search results in `$key => $value` format.

The `getOptionLabelUsing()` method accepts a callback that transforms the selected option `$value` into a label.

```php
Select::make('authorId')
    ->searchable()
    ->getSearchResultsUsing(fn (string $search) => User::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'))
    ->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name),
```

You can prevent the placeholder from being selected using the `disablePlaceholderSelection()` method:

```php
use Filament\Forms\Components\Select;

Select::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
    ->default('draft')
    ->disablePlaceholderSelection()
```

### Multi-select

The `multiple()` method on the `Select` component allows you to select multiple values from the list of options:

```php
use Filament\Forms\Components\Select;

Select::make('technologies')
    ->multiple()
    ->options([
        'tailwind' => 'Tailwind CSS',
        'alpine' => 'Alpine.js',
        'laravel' => 'Laravel',
        'livewire' => 'Laravel Livewire',
    ])
```

![](https://user-images.githubusercontent.com/41773797/147613070-cd82703a-fa05-4f29-b0ac-3eb03b542077.png)

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

Instead of `getOptionLabelUsing()`, the `getOptionLabelsUsing()` method can be used to transform the selected options' `$value`s into labels.

### Dependant selects

Commonly, you may desire "dependant" select inputs, which populate their options based on the state of another.

<iframe width="560" height="315" src="https://www.youtube.com/embed/W_eNyimRi3w" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

Some of the techniques described in the [advanced forms](advanced) section are required to create dependant selects. These techniques can be applied across all form components for many dynamic customization possibilities.

### Populating automatically from a relationship

You may employ the `relationship()` method of the `Select` to configure a `BelongsTo` relationship to automatically retrieve and save options from:

```php
use Filament\Forms\Components\Select;

Select::make('authorId')
    ->relationship('author', 'name')
```

The `multiple()` method may be used in combination with `relationship()` to automatically populate from a `BelongsToMany` relationship:

```php
use Filament\Forms\Components\Select;

Select::make('technologies')
    ->multiple()
    ->relationship('technologies', 'name')
```

> To set this functionality up, **you must also follow the instructions set out in the [field relationships](getting-started#field-relationships) section**. If you're using the [admin panel](/docs/admin), you can skip this step.

If you'd like to populate the options from the database when the page is loaded, instead of when the user searches, you can use the `preload()` method:

```php
use Filament\Forms\Components\Select;

Select::make('authorId')
    ->relationship('author', 'name')
    ->preload()
```

You may customize the database query that retrieves options using the third parameter of the `relationship()` method:

```php
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

Select::make('authorId')
    ->relationship('author', 'name', fn (Builder $query) => $query->withTrashed())
```

If you'd like to customize the label of each option, maybe to be more descriptive, or to concatenate a first and last name, you should use a virtual column in your database migration:

```php
$table->string('full_name')->virtualAs('concat(first_name, \' \', last_name)');
```

```php
use Filament\Forms\Components\Select;

Select::make('authorId')
    ->relationship('author', 'full_name')
```

Alternatively, you can use the `getOptionLabelFromRecordUsing()` method to transform the selected option's Eloquent model into a label. But please note, this is much less performant than using a virtual column:

```php
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;

Select::make('authorId')
    ->relationship('author', 'first_name')
    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
```

#### Handling `MorphTo` relationships

`MorphTo` relationships are special, since they give the user the ability to select records from a range of different models. Because of this, we have a dedicated `MorphToSelect` component which is not actually a select field, rather 2 select fields inside a fieldset. The first select field allows you to select the type, and the second allows you to select the record of that type.

To use the `MorphToSelect`, you must pass `types()` into the component, which tell it how to render options for different types:

```php
use Filament\Forms\Components\MorphToSelect;

MorphToSelect::make('commentable')
    ->types([
        MorphToSelect\Type::make(Product::class)->titleColumnName('name'),
        MorphToSelect\Type::make(Post::class)->titleColumnName('title'),
    ])
```

The `titleColumnName()` is used to extract the titles out of each product or post. You can choose to extract the option labels using `getOptionLabelFromRecordUsing` instead if you wish:

```php
use Filament\Forms\Components\MorphToSelect;

MorphToSelect::make('commentable')
    ->types([
        MorphToSelect\Type::make(Product::class)
            ->getOptionLabelFromRecordUsing(fn (Product $record): string => "{$record->name} - {$record->slug}"),
        MorphToSelect\Type::make(Post::class)->titleColumnName('title'),
    ])
```

You may customize the database query that retrieves options using the `modifyOptionsQueryUsing()` method:

```php
use Filament\Forms\Components\MorphToSelect;
use Illuminate\Database\Eloquent\Builder;

MorphToSelect::make('commentable')
    ->types([
        MorphToSelect\Type::make(Product::class)
            ->titleColumnName('name')
            ->modifyOptionsQueryUsing(fn (Builder $query) => $query->whereBelongsTo($this->team)),
        MorphToSelect\Type::make(Post::class)
            ->titleColumnName('title')
            ->modifyOptionsQueryUsing(fn (Builder $query) => $query->whereBelongsTo($this->team)),
    ])
```

> Many of the same options in the select field are available for `MorphToSelect`, including `searchable()`, `preload()`, `allowHtml()`, and `optionsLimit()`.

#### Creating new records

You may define a custom form that can be used to create a new record and attach it to the `BelongsTo` relationship:

```php
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;

Select::make('authorId')
    ->relationship('author', 'name')
    ->createOptionForm([
        Forms\Components\TextInput::make('name')
            ->required(),
        Forms\Components\TextInput::make('email')
            ->required()
            ->email(),
    ]),
```

The form opens in a modal, where the user can fill it with data. Upon form submission, the new record is selected by the field.

Since HTML does not support nested `<form>` elements, you must also render the modal outside the `<form>` in the view. If you're using the [admin panel](/docs/admin), this is included already:

```blade
<form wire:submit.prevent="submit">
    {{ $this->form }}

    <button type="submit">
        Submit
    </button>
</form>

{{ $this->modal }}
```

## Checkbox

The checkbox component, similar to a [toggle](#toggle), allows you to interact a boolean value.

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('is_admin')
```

![](https://user-images.githubusercontent.com/41773797/147613098-db8d3571-1835-4714-b422-619755c0b722.png)

Checkbox fields have two layout modes, inline and stacked. By default, they are inline.

When the checkbox is inline, its label is adjacent to it:

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('is_admin')->inline()
```

![](https://user-images.githubusercontent.com/41773797/147613098-db8d3571-1835-4714-b422-619755c0b722.png)

When the checkbox is stacked, its label is above it:

```php
use Filament\Forms\Components\Checkbox;

Checkbox::make('is_admin')->inline(false)
```

![](https://user-images.githubusercontent.com/41773797/147613119-0bc731dd-fcdd-4c1a-9a26-cce2b0f589d2.png)

If you're saving the boolean value using Eloquent, you should be sure to add a `boolean` [cast](https://laravel.com/docs/eloquent-mutators#attribute-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $casts = [
        'is_admin' => 'boolean',
    ];

    // ...
}
```

## Toggle

The toggle component, similar to a [checkbox](#checkbox), allows you to interact a boolean value.

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')
```

![](https://user-images.githubusercontent.com/41773797/147613146-f5ebde21-f72d-44dd-b5c0-5d229fcd91ef.png)

Toggle fields have two layout modes, inline and stacked. By default, they are inline.

When the toggle is inline, its label is adjacent to it:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')->inline()
```

![](https://user-images.githubusercontent.com/41773797/147613146-f5ebde21-f72d-44dd-b5c0-5d229fcd91ef.png)

When the toggle is stacked, its label is above it:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')->inline(false)
```

![](https://user-images.githubusercontent.com/41773797/147613161-43bfa094-0916-4e01-b86d-dbcf8ee63a17.png)

Toggles may also use an "on icon" and an "off icon". These are displayed on its handle and could provide a greater indication to what your field represents. The parameter to each method must contain the name of a Blade icon component:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')
    ->onIcon('heroicon-s-lightning-bolt')
    ->offIcon('heroicon-s-user')
```

You may also customize the color representing each state. These may be either `primary`, `secondary`, `success`, `warning` or `danger`:

```php
use Filament\Forms\Components\Toggle;

Toggle::make('is_admin')
    ->onColor('success')
    ->offColor('danger')
```

![](https://user-images.githubusercontent.com/41773797/147613184-9086c102-ad71-4c4e-9170-9a4201a80c66.png)

If you're saving the boolean value using Eloquent, you should be sure to add a `boolean` [cast](https://laravel.com/docs/eloquent-mutators#attribute-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $casts = [
        'is_admin' => 'boolean',
    ];

    // ...
}
```

## Checkbox list

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

You may organize options into columns by using the `columns()` method:

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

This method accepts the same options as the `columns()` method of the [grid](layout#grid). This allows you to responsively customize the number of columns at various breakpoints.

You may also allow users to toggle all checkboxes at once using the `bulkToggleable()` method:

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

### Populating automatically from a relationship

You may employ the `relationship()` method to configure a relationship to automatically retrieve and save options from:

```php
use Filament\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->relationship('technologies', 'name')
```

> To set this functionality up, **you must also follow the instructions set out in the [field relationships](getting-started#field-relationships) section**. If you're using the [admin panel](/docs/admin), you can skip this step.

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

## Radio

The radio input provides a radio button group for selecting a single value from a list of predefined options:

```php
use Filament\Forms\Components\Radio;

Radio::make('status')
    ->options([
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'published' => 'Published'
    ])
```

![](https://user-images.githubusercontent.com/41773797/147613206-644bd6a4-4814-4a99-b398-d03625179bfa.png)

You can optionally provide descriptions to each option using the `descriptions()` method:

```php
use Filament\Forms\Components\Radio;

Radio::make('status')
    ->options([
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'published' => 'Published'
    ])
    ->descriptions([
        'draft' => 'Is not visible.',
        'scheduled' => 'Will be visible.',
        'published' => 'Is visible.'
    ])
```

![](https://user-images.githubusercontent.com/41773797/147613223-0114ad7e-091c-43cf-b156-67cd0aaf5c0c.png)

Be sure to use the same `key` in the descriptions array as the `key` in the options array so the right description matches the right option.

If you want a simple boolean radio button group, with "Yes" and "No" options, you can use the `boolean()` method:

```php
Radio::make('feedback')
    ->label('Do you like this post?')
    ->boolean()
```

![](https://user-images.githubusercontent.com/41773797/147613274-04745624-3ddd-46bb-b25c-1e756d6f4958.png)

You may wish to display the options `inline()` with the label:

```php
Radio::make('feedback')
    ->label('Do you like this post?')
    ->boolean()
    ->inline()
```

![](https://user-images.githubusercontent.com/41773797/147709853-198d54fb-1bb1-4e82-87d0-3034b9152f0e.png)

## Date-time picker

The date-time picker provides an interactive interface for selecting a date and a time.

```php
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;

DateTimePicker::make('published_at')
DatePicker::make('date_of_birth')
TimePicker::make('alarm_at')
```

![](https://user-images.githubusercontent.com/41773797/147613326-004b09c8-c224-4676-a70f-cf6b7e3f0306.png)

You may restrict the minimum and maximum date that can be selected with the picker. The `minDate()` and `maxDate()` methods accept a `DateTime` instance (e.g. Carbon), or a string:

```php
use Filament\Forms\Components\DatePicker;

DatePicker::make('date_of_birth')
    ->minDate(now()->subYears(150))
    ->maxDate(now())
```

![](https://user-images.githubusercontent.com/41773797/147613432-41e22381-af01-4f5e-8d0d-0ba535d1e444.png)

You may customize the format of the field when it is saved in your database, using the `format()` method. This accepts a string date format, using [PHP date formatting tokens](https://www.php.net/manual/en/datetime.format.php):

```php
use Filament\Forms\Components\DatePicker;

DatePicker::make('date_of_birth')->format('d/m/Y')
```

You may also customize the display format of the field, separately from the format used when it is saved in your database. For this, use the `displayFormat()` method, which also accepts a string date format, using [PHP date formatting tokens](https://www.php.net/manual/en/datetime.format.php):

```php
use Filament\Forms\Components\DatePicker;

DatePicker::make('date_of_birth')->displayFormat('d/m/Y')
```

![](https://user-images.githubusercontent.com/41773797/147613473-51ffe805-2a7f-47e5-af8b-e7871e9c5a85.png)

When using the time picker, you may disable the seconds input using the `withoutSeconds()` method:

```php
use Filament\Forms\Components\DateTimePicker;

DateTimePicker::make('published_at')->withoutSeconds()
```

You may also customize the input interval for increasing the hours / minutes / seconds using the `hoursStep()` , `minutesStep()` or `secondsStep()` methods:

```php
use Filament\Forms\Components\DateTimePicker;

DateTimePicker::make('published_at')
    ->hoursStep(2)
    ->minutesStep(15)
    ->secondsStep(10)
```

![](https://user-images.githubusercontent.com/41773797/147613511-30d7b2d8-227a-42ff-a6c7-e080d22305ad.png)

In some countries, the first day of the week is not Monday. To customize the first day of the week in the date picker, use the `forms.components.date_time_picker.first_day_of_week` config option, or the `firstDayOfWeek()` method on the component. 0 to 7 are accepted values, with Monday as 1 and Sunday as 7 or 0:

```php
use Filament\Forms\Components\DateTimePicker;

DateTimePicker::make('published_at')->firstDayOfWeek(7)
```

![](https://user-images.githubusercontent.com/41773797/147613536-6c2bdc63-03f8-4dd9-92eb-9a0aca5e7263.png)

There are additionally convenient helper methods to set the first day of the week more semantically:

```php
use Filament\Forms\Components\DateTimePicker;

DateTimePicker::make('published_at')->weekStartsOnMonday()
DateTimePicker::make('published_at')->weekStartsOnSunday()
```

To disable specific dates:

```php
use Filament\Forms\Components\DateTimePicker;

DateTimePicker::make('date')
    ->label('Appointment date')
    ->minDate(now())
    ->maxDate(Carbon::now()->addDays(30))
    ->disabledDates(['2022-10-02', '2022-10-05', '2022-10-15'])
```

You may change the calendar icon using the `icon()` method:

```php
use Filament\Forms\Components\DateTimePicker;

DateTimePicker::make('date')
    ->icon('heroicon-o-calendar')
```

Alternatively, you may remove the icon altogether by passing `false`:

```php
use Filament\Forms\Components\DateTimePicker;

DateTimePicker::make('date')
    ->icon(false)
```

### Timezones

If you'd like users to be able to manage dates in their own timezone, you can use the `timezone()` method:

```php
use Filament\Forms\Components\DateTimePicker;

DateTimePicker::make('published_at')->timezone('America/New_York')
```

While dates will still be stored using the app's configured timezone, the date will now load in the new timezone, and it will be converted back when the form is saved.

## File upload

The file upload field is based on [Filepond](https://pqina.nl/filepond).

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
```

![](https://user-images.githubusercontent.com/41773797/147613556-62c62153-4d21-4801-8a71-040d528d5757.png)

By default, files will be uploaded publicly to your default storage disk.

> Please note, to correctly preview images and other files, FilePond requires files to be served from the same domain as the app, or the appropriate CORS headers need to be present. Ensure that the `APP_URL` environment variable is correct, or modify the [filesystem](https://laravel.com/docs/10.x/filesystem) driver to set the correct URL. If you're hosting files on a separate domain like S3, ensure that CORS headers are set up.

To change the disk and directory that files are saved in, and their visibility, use the `disk()`, `directory()` and `visibility` methods:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->disk('s3')
    ->directory('form-attachments')
    ->visibility('private')
```

> Please note, it is the responsibility of the developer to delete these files from the disk if they are removed, as Filament is unaware if they are depended on elsewhere. One way to do this automatically is observing a [model event](https://laravel.com/docs/eloquent#events).

By default, a random file name will be generated for newly-uploaded files. To instead preserve the original filenames of the uploaded files, use the `preserveFilenames()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')->preserveFilenames()
```

You may completely customize how file names are generated using the `getUploadedFileNameForStorageUsing()` method, and returning a string from the callback:

```php
use Livewire\TemporaryUploadedFile;

FileUpload::make('attachment')
    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
        return (string) str($file->getClientOriginalName())->prepend('custom-prefix-');
    })
```

You can keep the randomly generated file names, while still storing the original file name, using the `storeFileNamesIn()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->storeFileNamesIn('attachment_file_names')
```

`attachment_file_names` will now store the original file name/s of your uploaded files.

You may restrict the types of files that may be uploaded using the `acceptedFileTypes()` method, and passing an array of MIME types. You may also use the `image()` method as shorthand to allow all image MIME types.

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('document')->acceptedFileTypes(['application/pdf'])
FileUpload::make('image')->image()
```

You may also restrict the size of uploaded files, in kilobytes:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->minSize(512)
    ->maxSize(1024)
```

> To customize Livewire's default file upload validation rules, including the 12MB file size maximum, please refer to its [documentation](https://laravel-livewire.com/docs/file-uploads#global-validation).

Filepond allows you to crop and resize images before they are uploaded. You can customize this behaviour using the `imageResizeMode()`, `imageCropAspectRatio()`, `imageResizeTargetHeight()` and `imageResizeTargetWidth()` methods. `imageResizeMode()` should be set for the other methods to have an effect - either [`force`, `cover`, or `contain`](https://pqina.nl/filepond/docs/api/plugins/image-resize).

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('image')
    ->image()
    ->imageResizeMode('cover')
    ->imageCropAspectRatio('16:9')
    ->imageResizeTargetWidth('1920')
    ->imageResizeTargetHeight('1080')
```

You may also alter the general appearance of the Filepond component. Available options for these methods are available on the [Filepond website](https://pqina.nl/filepond/docs/api/instance/properties/#styles).

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachment')
    ->imagePreviewHeight('250')
    ->loadingIndicatorPosition('left')
    ->panelAspectRatio('2:1')
    ->panelLayout('integrated')
    ->removeUploadedFileButtonPosition('right')
    ->uploadButtonPosition('left')
    ->uploadProgressIndicatorPosition('left')
```

![](https://user-images.githubusercontent.com/41773797/147613590-9ee07ce7-a43e-46a0-bb40-7a21a3692aea.png)

You may also upload multiple files. This stores URLs in JSON:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')->multiple()
```

If you're saving the file URLs using Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $casts = [
        'attachments' => 'array',
    ];

    // ...
}
```

You may customize the number of files that may be uploaded, using the `minFiles()` and `maxFiles()` methods:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->minFiles(2)
    ->maxFiles(5)
```

You can also enable the re-ordering of uploaded files using the `enableReordering()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->enableReordering()
```

You can add a button to open each file in a new tab with the `enableOpen()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->enableOpen()
```

If you wish to add a download button to each file instead, you can use the `enableDownload()` method:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->enableDownload()
```

> Filament also supports [`spatie/laravel-medialibrary`](https://github.com/spatie/laravel-medialibrary). See our [plugin documentation](/docs/spatie-laravel-media-library-plugin) for more information.

## Rich editor

The rich editor allows you to edit and preview HTML content, as well as upload images.

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
```

![](https://user-images.githubusercontent.com/41773797/147613608-b1236c72-d5cf-40d5-aa73-70c37a5c7e4d.png)

You may enable / disable toolbar buttons using a range of convenient methods:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->toolbarButtons([
        'attachFiles',
        'blockquote',
        'bold',
        'bulletList',
        'codeBlock',
        'h2',
        'h3',
        'italic',
        'link',
        'orderedList',
        'redo',
        'strike',
        'underline',
        'undo',
    ])
RichEditor::make('content')
    ->disableToolbarButtons([
        'attachFiles',
        'codeBlock',
    ])
RichEditor::make('content')
    ->disableAllToolbarButtons()
    ->enableToolbarButtons([
        'bold',
        'bulletList',
        'italic',
        'strike',
    ])
```

You may customize how images are uploaded using configuration methods:

```php
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->fileAttachmentsDisk('s3')
    ->fileAttachmentsDirectory('attachments')
    ->fileAttachmentsVisibility('private')
```

## Markdown editor

The markdown editor allows you to edit and preview markdown content, as well as upload images using drag and drop.

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
```

![](https://user-images.githubusercontent.com/41773797/147613631-0f9254aa-0abb-4a2e-b9d7-bda1a01b8d57.png)

You may enable / disable toolbar buttons using a range of convenient methods:

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
    ->toolbarButtons([
        'attachFiles',
        'bold',
        'bulletList',
        'codeBlock',
        'edit',
        'italic',
        'link',
        'orderedList',
        'preview',
        'strike',
    ])
MarkdownEditor::make('content')
    ->disableToolbarButtons([
        'attachFiles',
        'codeBlock',
    ])
MarkdownEditor::make('content')
    ->disableAllToolbarButtons()
    ->enableToolbarButtons([
        'bold',
        'bulletList',
        'edit',
        'italic',
        'preview',
        'strike',
    ])
```

You may customize how images are uploaded using configuration methods:

```php
use Filament\Forms\Components\MarkdownEditor;

MarkdownEditor::make('content')
    ->fileAttachmentsDisk('s3')
    ->fileAttachmentsDirectory('attachments')
    ->fileAttachmentsVisibility('private')
```

## Hidden

The hidden component allows you to create a hidden field in your form that holds a value.

```php
use Filament\Forms\Components\Hidden;

Hidden::make('token')
```

## Repeater

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

![](https://user-images.githubusercontent.com/41773797/147613690-9c36779b-f3dc-4710-bdba-dc5c81ddd3cd.png)

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

If you wish to define a repeater with multiple schema blocks that can be repeated in any order, please use the [builder](#builder).

Repeaters may have a certain number of empty items created by default, using the `defaultItems()` method:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('members')
    ->schema([
        // ...
    ])
    ->defaultItems(3)
```

You may set a label to customize the text that should be displayed in the button for adding a repeater item:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('members')
    ->schema([
        // ...
    ])
    ->createItemButtonLabel('Add member')
```

![](https://user-images.githubusercontent.com/41773797/147613748-6fdf2eff-de09-4ba0-8d01-68888802b152.png)

You may also prevent the user from adding items, deleting items, or moving items inside the repeater:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('members')
    ->schema([
        // ...
    ])
    ->disableItemCreation()
    ->disableItemDeletion()
    ->disableItemMovement()
```

You may customize the number of items that may be created, using the `minItems()` and `maxItems()` methods:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('members')
    ->schema([
        // ...
    ])
    ->minItems(1)
    ->maxItems(10)
```

### Collapsible

The repeater may be `collapsible()` to optionally hide content in long forms:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->schema([
        // ...
    ])
    ->collapsible()
```

You may collapse all items by default:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->schema([
        // ...
    ])
    ->collapsed()
```

### Cloning items

You may allow repeater items to be duplicated using the `cloneable()` method:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->schema([
        // ...
    ])
    ->cloneable()
```

### Populating automatically from a relationship

You may employ the `relationship()` method of the repeater to configure a relationship to automatically retrieve and save repeater items:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->relationship()
    ->schema([
        // ...
    ])
```

> To set this functionality up, **you must also follow the instructions set out in the [field relationships](getting-started#field-relationships) section**. If you're using the [admin panel](/docs/admin), you can skip this step.

#### Ordering items

By default, ordering relationship repeater items is disabled. This is because your related model needs an `sort` column to store the order of related records. To enable ordering, you may use the `orderable()` method:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->relationship()
    ->schema([
        // ...
    ])
    ->orderable()
```

This assumes that your related model has a `sort` column.

If you use something like [`spatie/eloquent-sortable`](https://github.com/spatie/eloquent-sortable) with an order column such as `order_column`, you may pass this in to `orderable()`:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->relationship()
    ->schema([
        // ...
    ])
    ->orderable('order_column')
```

### Grid layout

You may organize repeater items into columns by using the `grid()` method:

```php
use Filament\Forms\Components\Repeater;

Repeater::make('members')
    ->schema([
        // ...
    ])
    ->grid(2)
```

This method accepts the same options as the `columns()` method of the [grid](layout#grid). This allows you to responsively customize the number of grid columns at various breakpoints.

### Item labels

You may add a label for repeater items using the `itemLabel()` method:

```php
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

Repeater::make('members')
    ->schema([
        TextInput::make('name')
            ->lazy(),
    ])
    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
```

Any fields that you use from `$state` should be `reactive()` or `lazy()` if you wish to see the item label update live as you use the form.

### Using `$get()` to access parent field values

All form components are able to [use `$get()` and `$set()`](advanced) to access another field's value. However, you might experience unexpected behaviour when using this inside the repeater's schema.

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
                TextInput::make('content')
                    ->label('Heading')
                    ->required(),
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
                MarkdownEditor::make('content')
                    ->label('Paragraph')
                    ->required(),
            ]),
        Builder\Block::make('image')
            ->schema([
                FileUpload::make('url')
                    ->label('Image')
                    ->image()
                    ->required(),
                TextInput::make('alt')
                    ->label('Alt text')
                    ->required(),
            ]),
    ])
```

![](https://user-images.githubusercontent.com/41773797/147613850-8693abe9-78c9-4f01-b40e-9cd9a567e168.png)

We recommend that you store builder data with a `JSON` column in your database. Additionally, if you're using Eloquent, make sure that column has an `array` cast.

As evident in the above example, blocks can be defined within the `blocks()` method of the component. Blocks are `Builder\Block` objects, and require a unique name, and a component schema:

```php
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;

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

Blocks may also have an icon, which is displayed next to the label. The `icon()` method accepts the name of any Blade icon component:

```php
use Filament\Forms\Components\Builder;

Builder\Block::make('heading')->icon('heroicon-o-bookmark')
```

![](https://user-images.githubusercontent.com/41773797/147614039-d9aa43dd-acfe-43b6-9cd1-1fc322aa4526.png)

You may customize the number of items that may be created, using the `minItems()` and `maxItems()` methods:

```php
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;

Builder::make('content')
    ->blocks([
        // ...
    ])
    ->minItems(1)
    ->maxItems(10)
```

### Collapsible

The builder may be `collapsible()` to optionally hide content in long forms:

```php
use Filament\Forms\Components\Builder;

Builder::make('content')
    ->blocks([
        // ...
    ])
    ->collapsible()
```

You may collapse all items by default:

```php
use Filament\Forms\Components\Builder;

Builder::make('content')
    ->blocks([
        // ...
    ])
    ->collapsed()
```

## Tags input

The tags input component allows you to interact with a list of tags.

By default, tags are stored in JSON:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')
```

![](https://user-images.githubusercontent.com/41773797/147614098-14d6eecb-2674-49b6-a5ee-e963c4d88a6e.png)

If you're saving the JSON tags using Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $casts = [
        'tags' => 'array',
    ];

    // ...
}
```

You may allow the tags to be stored in a separated string, instead of JSON. To set this up, pass the separating character to the `separator()` method:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')->separator(',')
```

Tags inputs may have autocomplete suggestions. To enable this, pass an array of suggestions to the `suggestions()` method:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')
    ->suggestions([
        'tailwindcss',
        'alpinejs',
        'laravel',
        'livewire',
    ])
```

![](https://user-images.githubusercontent.com/41773797/147614115-7570a6cb-dd91-4912-8adf-e54a51f1c567.png)

> Filament also supports [`spatie/laravel-tags`](https://github.com/spatie/laravel-tags). See our [plugin documentation](/docs/spatie-laravel-tags-plugin) for more information.

## Textarea

The textarea allows you to interact with a multi-line string:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
```

![](https://user-images.githubusercontent.com/41773797/147614131-e3db8d23-5045-4e0e-8de4-30823a4af362.png)

You may change the size of the textarea by defining the `rows()` and `cols()` methods:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->rows(10)
    ->cols(20)
```

You may limit the length of the string by setting the `minLength()` and `maxLength()` methods. These methods add both frontend and backend validation:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->minLength(50)
    ->maxLength(500)
```

## Key-value

The key-value field allows you to interact with one-dimensional JSON object:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
```

![](https://user-images.githubusercontent.com/41773797/147614182-52756a59-a9c4-4371-ac61-cd77c977808e.png)

You may customize the labels for the key and value fields using the `keyLabel()` and `valueLabel()` methods:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->keyLabel('Property name')
    ->valueLabel('Property value')
```

![](https://user-images.githubusercontent.com/41773797/147614196-27341757-b50e-45c2-8b40-728cb985d2cd.png)

You may also prevent the user from adding rows, deleting rows, or editing keys:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->disableAddingRows()
    ->disableDeletingRows()
    ->disableEditingKeys()
```

You can allow the user to reorder rows within the table:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->reorderable()
```

You may also add placeholders for the key and value fields using the `keyPlaceholder()` and `valuePlaceholder()` methods:

```php
use Filament\Forms\Components\KeyValue;

KeyValue::make('meta')
    ->keyPlaceholder('Property name')
    ->valuePlaceholder('Property value')
```

![](https://user-images.githubusercontent.com/41773797/147614208-936fa7ee-f719-466f-b7de-56ecc2558c0a.png)

## Color picker

The color picker component allows you to pick a color in a range of formats.

By default, the component uses HEX format:

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::make('color')
```

![](https://user-images.githubusercontent.com/41773797/163201755-8926ce35-0d72-42b0-bd31-8967ba40f089.png)

Alternatively, you can use a different format:

```php
use Filament\Forms\Components\ColorPicker;

ColorPicker::make('hsl_color')->hsl()
ColorPicker::make('rgb_color')->rgb()
ColorPicker::make('rgba_color')->rgba()
```

## View

Aside from [building custom fields](#building-custom-fields), you may create "view" fields which allow you to create custom fields without extra PHP classes.

```php
use Filament\Forms\Components\ViewField;

ViewField::make('notifications')->view('filament.forms.components.range-slider')
```

Inside your view, you may interact with the state of the form component using Livewire and Alpine.js.

The `$getStatePath()` closure may be used by the view to retrieve the Livewire property path of the field. You could use this to [`wire:model`](https://laravel-livewire.com/docs/properties#data-binding) a value, or [`$wire.entangle`](https://laravel-livewire.com/docs/alpine-js) it with Alpine.js.

Using [Livewire's entangle](https://laravel-livewire.com/docs/alpine-js#sharing-state) allows sharing state with Alpine.js:

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer }">
        <!-- Interact with the `state` property in Alpine.js -->
    </div>
</x-dynamic-component>
```

Or, you may bind the value to a Livewire property using [`wire:model`](https://laravel-livewire.com/docs/properties#data-binding):

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <input wire:model.defer="{{ $getStatePath() }}" />
</x-dynamic-component>
```

## Building custom fields

You may create your own custom field classes and views, which you can reuse across your project, and even release as a plugin to the community.

> If you're just creating a simple custom field to use once, you could instead use a [view field](#view) to render any custom Blade file.

To create a custom field class and view, you may use the following command:

```bash
php artisan make:form-field RangeSlider
```

This will create the following field class:

```php
use Filament\Forms\Components\Field;

class RangeSlider extends Field
{
    protected string $view = 'filament.forms.components.range-slider';
}
```

Inside your view, you may interact with the state of the form component using Livewire and Alpine.js.

The `$getStatePath()` closure may be used by the view to retrieve the Livewire property path of the field. You could use this to [`wire:model`](https://laravel-livewire.com/docs/properties#data-binding) a value, or [`$wire.entangle`](https://laravel-livewire.com/docs/alpine-js) it with Alpine.js:

```blade
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer }">
        <!-- Interact with the `state` property in Alpine.js -->
    </div>
</x-dynamic-component>
```
