---
title: Select
---
import Aside from "@components/Aside.astro"
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

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

<UtilityInjection set="formFields" version="4.x">As well as allowing a static array, the `options()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/select/simple" alt="Select" version="4.x" />

## Enabling the JavaScript select

By default, Filament uses the native HTML5 select. You may enable a more customizable JavaScript select using the `native(false)` method:

```php
use Filament\Forms\Components\Select;

Select::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
    ->native(false)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `native()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/select/javascript" alt="JavaScript select" version="4.x" />

## Searching options

You may enable a search input to allow easier access to many options, using the `searchable()` method:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->label('Author')
    ->options(User::query()->pluck('name', 'id'))
    ->searchable()
```

Optionally, you may pass a boolean value to control if the input should be searchable or not:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->label('Author')
    ->options(User::query()->pluck('name', 'id'))
    ->searchable(FeatureFlag::active())
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `searchable()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/select/searchable" alt="Searchable select" version="4.x" />

### Returning custom search results

If you have lots of options and want to populate them based on a database search or other external data source, you can use the `getSearchResultsUsing()` and `getOptionLabelUsing()` methods instead of `options()`.

The `getSearchResultsUsing()` method accepts a callback that returns search results in `$key => $value` format. The current user's search is available as `$search`, and you should use that to filter your results.

The `getOptionLabelUsing()` method accepts a callback that transforms the selected option `$value` into a label. This is used when the form is first loaded when the user has not made a search yet. Otherwise, the label used to display the currently selected option would not be available.

Both `getSearchResultsUsing()` and `getOptionLabelUsing()` must be used on the select if you want to provide custom search results:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->searchable()
    ->getSearchResultsUsing(fn (string $search): array => User::query()
        ->where('name', 'like', "%{$search}%")
        ->limit(50)
        ->pluck('name', 'id')
        ->all())
    ->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name),
```

`getOptionLabelUsing()` is crucial, since it provides Filament with the label of the selected option, so it doesn't need to execute a full search to find it. If an option is not valid, it should return `null`.

<UtilityInjection set="formFields" version="4.x" extras="Option value;;mixed;;$value;;[<code>getOptionLabelUsing()</code> only] The option value to retrieve the label for.||Option values;;array<mixed>;;$values;;[<code>getOptionLabelsUsing()</code> only] The option values to retrieve the labels for.||Search;;?string;;$search;;[<code>getSearchResultsUsing()</code> only] The current search input value, if the field is searchable.">You can inject various utilities into these functions as parameters.</UtilityInjection>

### Setting a custom loading message

When you're using a searchable select or multi-select, you may want to display a custom message while the options are loading. You can do this using the `loadingMessage()` method:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->searchable()
    ->loadingMessage('Loading authors...')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `loadingMessage()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Setting a custom no search results message

When you're using a searchable select or multi-select, you may want to display a custom message when no search results are found. You can do this using the `noSearchResultsMessage()` method:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->searchable()
    ->noSearchResultsMessage('No authors found.')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `noSearchResultsMessage()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Setting a custom no options message

When you're using a select or multi-select with `preload()` or dynamic options via `options()` closure, you may want to display a custom message when no options are available. You can do this using the `noOptionsMessage()` method:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->searchable()
    ->preload()
    ->noOptionsMessage('No authors available.')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `noOptionsMessage()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Setting a custom search prompt

When you're using a searchable select or multi-select, you may want to display a custom message when the user has not yet entered a search term. You can do this using the `searchPrompt()` method:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->searchable(['name', 'email'])
    ->searchPrompt('Search authors by their name or email address')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `searchPrompt()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Setting a custom searching message

When you're using a searchable select or multi-select, you may want to display a custom message while the search results are being loaded. You can do this using the `searchingMessage()` method:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->searchable()
    ->searchingMessage('Searching authors...')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `searchingMessage()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Tweaking the search debounce

By default, Filament will wait 1000 milliseconds (1 second) before searching for options when the user types in a searchable select or multi-select. It will also wait 1000 milliseconds between searches, if the user is continuously typing into the search input. You can change this using the `searchDebounce()` method:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->searchable()
    ->searchDebounce(500)
```

Ensure that you are not lowering the debounce too much, as this may cause the select to become slow and unresponsive due to a high number of network requests to retrieve options from server.

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `searchDebounce()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Multi-select

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

Optionally, you may pass a boolean value to control if the input should be multiple or not:

```php
use Filament\Forms\Components\Select;

Select::make('technologies')
    ->multiple(FeatureFlag::active())
    ->options([
        'tailwind' => 'Tailwind CSS',
        'alpine' => 'Alpine.js',
        'laravel' => 'Laravel',
        'livewire' => 'Laravel Livewire',
    ])
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `multiple()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/select/multiple" alt="Multi-select" version="4.x" />

These options are returned in JSON format. If you're saving them using Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'technologies' => 'array',
        ];
    }

    // ...
}
```

If you're [returning custom search results](#returning-custom-search-results), you should define `getOptionLabelsUsing()` instead of `getOptionLabelUsing()`. `$values` will be passed into the callback instead of `$value`, and you should return a `$key => $value` array of labels and their corresponding values:

```php
Select::make('technologies')
    ->multiple()
    ->searchable()
    ->getSearchResultsUsing(fn (string $search): array => Technology::query()
        ->where('name', 'like', "%{$search}%")
        ->limit(50)
        ->pluck('name', 'id')
        ->all())
    ->getOptionLabelsUsing(fn (array $values): array => Technology::query()
        ->whereIn('id', $values)
        ->pluck('name', 'id')
        ->all()),
```

`getOptionLabelsUsing()` is crucial, since it provides Filament with the labels of already-selected options, so it doesn't need to execute a full search to find them. It is also used to [validate](#valid-options-validation-in-rule) that the options that the user has selected are valid. If an option is not valid, it should not be present in the array returned by `getOptionLabelsUsing()`.

<UtilityInjection set="formFields" version="4.x" extras="Option values;;array<mixed>;;$values;;[<code>getOptionLabelsUsing()</code> only] The option values to retrieve the labels for.">The `getOptionLabelsUsing()` method can inject various utilities into the function as parameters.</UtilityInjection>

### Reordering selected options

The `reorderable()` method allows you to reorder the selected options in a multi-select:

```php
use Filament\Forms\Components\Select;

Select::make('technologies')
    ->multiple()
    ->reorderable()
    ->options([
        'tailwind' => 'Tailwind CSS',
        'alpine' => 'Alpine.js',
        'laravel' => 'Laravel',
        'livewire' => 'Laravel Livewire',
    ])
```

This is useful when the order of the selected options matters.

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `reorderable()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Grouping options

You can group options together under a label, to organize them better. To do this, you can pass an array of groups to `options()` or wherever you would normally pass an array of options. The keys of the array are used as group labels, and the values are arrays of options in that group:

```php
use Filament\Forms\Components\Select;

Select::make('status')
    ->searchable()
    ->options([
        'In Process' => [
            'draft' => 'Draft',
            'reviewing' => 'Reviewing',
        ],
        'Reviewed' => [
            'published' => 'Published',
            'rejected' => 'Rejected',
        ],
    ])
```

<AutoScreenshot name="forms/fields/select/grouped" alt="Grouped select" version="4.x" />

## Integrating with an Eloquent relationship

You may employ the `relationship()` method of the `Select` to configure a `BelongsTo` relationship to automatically retrieve options from. The `titleAttribute` is the name of a column that will be used to generate a label for each option:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
```

The `multiple()` method may be used in combination with `relationship()` to use a `BelongsToMany` relationship. Filament will load the options from the relationship, and save them back to the relationship's pivot table when the form is submitted. If a `name` is not provided, Filament will use the field name as the relationship name:

```php
use Filament\Forms\Components\Select;

Select::make('technologies')
    ->multiple()
    ->relationship(titleAttribute: 'name')
```

<Aside variant="warning">
    When using `disabled()` with `multiple()` and `relationship()`, ensure that `disabled()` is called before `relationship()`. This ensures that the `saved()` call from `disabled()` is not applied after the `relationship()` configuration:

    ```php
    use Filament\Forms\Components\Select;

    Select::make('technologies')
        ->multiple()
        ->disabled()
        ->relationship(titleAttribute: 'name')
    ```
</Aside>

### Searching relationship options across multiple columns

By default, if the select is also searchable, Filament will return search results for the relationship based on the title column of the relationship. If you'd like to search across multiple columns, you can pass an array of columns to the `searchable()` method:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->searchable(['name', 'email'])
```

### Preloading relationship options

If you'd like to populate the searchable options from the database when the page is loaded, instead of when the user searches, you can use the `preload()` method:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->searchable()
    ->preload()
```

Optionally, you may pass a boolean value to control if the input should be preloaded or not:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->searchable()
    ->preload(FeatureFlag::active())
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `preload()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Excluding the current record

When working with recursive relationships, you will likely want to remove the current record from the set of results.

This can be easily be done using the `ignoreRecord` argument:

```php
use Filament\Forms\Components\Select;

Select::make('parent_id')
    ->relationship(name: 'parent', titleAttribute: 'name', ignoreRecord: true)
```

### Customizing the relationship query

You may customize the database query that retrieves options using the third parameter of the `relationship()` method:

```php
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

Select::make('author_id')
    ->relationship(
        name: 'author',
        titleAttribute: 'name',
        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
    )
```

<UtilityInjection set="formFields" version="4.x" extras="Query;;Illuminate\Database\Eloquent\Builder;;$query;;The Eloquent query builder to modify.||Search;;?string;;$search;;The current search input value, if the field is searchable.">The `modifyQueryUsing` argument can inject various utilities into the function as parameters.</UtilityInjection>

### Customizing the relationship option labels

If you'd like to customize the label of each option, maybe to be more descriptive, or to concatenate a first and last name, you could use a virtual column in your database migration:

```php
$table->string('full_name')->virtualAs('concat(first_name, \' \', last_name)');
```

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'full_name')
```

Alternatively, you can use the `getOptionLabelFromRecordUsing()` method to transform an option's Eloquent model into a label:

```php
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

Select::make('author_id')
    ->relationship(
        name: 'author',
        modifyQueryUsing: fn (Builder $query) => $query->orderBy('first_name')->orderBy('last_name'),
    )
    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
    ->searchable(['first_name', 'last_name'])
```

<UtilityInjection set="formFields" version="4.x" extras="Eloquent record;;Illuminate\Database\Eloquent\Model;;$record;;The Eloquent record to get the option label for.">The `getOptionLabelFromRecordUsing()` method can inject various utilities into the function as parameters.</UtilityInjection>

### Saving pivot data to the relationship

If you're using a `multiple()` relationship and your pivot table has additional columns, you can use the `pivotData()` method to specify the data that should be saved in them:

```php
use Filament\Forms\Components\Select;

Select::make('primaryTechnologies')
    ->relationship(name: 'technologies', titleAttribute: 'name')
    ->multiple()
    ->pivotData([
        'is_primary' => true,
    ])
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `pivotData()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Creating a new option in a modal

You may define a custom form that can be used to create a new record and attach it to the `BelongsTo` relationship:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->createOptionForm([
        Forms\Components\TextInput::make('name')
            ->required(),
        Forms\Components\TextInput::make('email')
            ->required()
            ->email(),
    ]),
```

<UtilityInjection set="formFields" version="4.x" extras="Schema;;Filament\Schemas\Schema;;$schema;;The schema object for the form in the modal.">As well as allowing a static value, the `createOptionForm()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/select/create-option" alt="Select with create option button" version="4.x" />

The form opens in a modal, where the user can fill it with data. Upon form submission, the new record is selected by the field.

<AutoScreenshot name="forms/fields/select/create-option-modal" alt="Select with create option modal" version="4.x" />

#### Customizing new option creation

You can customize the creation process of the new option defined in the form using the `createOptionUsing()` method, which should return the primary key of the newly created record:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->createOptionForm([
       // ...
    ])
    ->createOptionUsing(function (array $data): int {
        return auth()->user()->team->members()->create($data)->getKey();
    }),
```

<UtilityInjection set="formFields" version="4.x" extras="Data;;array<string, mixed>;;$data;;The data from the form in the modal.||Schema;;Filament\Schemas\Schema;;$schema;;The schema object for the form in the modal.">The `createOptionUsing()` method can inject various utilities into the function as parameters.</UtilityInjection>

### Editing the selected option in a modal

You may define a custom form that can be used to edit the selected record and save it back to the `BelongsTo` relationship:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->editOptionForm([
        Forms\Components\TextInput::make('name')
            ->required(),
        Forms\Components\TextInput::make('email')
            ->required()
            ->email(),
    ]),
```

<UtilityInjection set="formFields" version="4.x" extras="Schema;;Filament\Schemas\Schema;;$schema;;The schema object for the form in the modal.">As well as allowing a static value, the `editOptionForm()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/select/edit-option" alt="Select with edit option button" version="4.x" />

The form opens in a modal, where the user can fill it with data. Upon form submission, the data from the form is saved back to the record.

<AutoScreenshot name="forms/fields/select/edit-option-modal" alt="Select with edit option modal" version="4.x" />

#### Customizing option updates

You can customize the update process of the selected option defined in the form using the `updateOptionUsing()` method. The current Eloquent record being edited can be retrieved using the `getRecord()` method on the schema:

```php
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->editOptionForm([
       // ...
    ])
    ->updateOptionUsing(function (array $data, Schema $schema) {
        $schema->getRecord()?->update($data);
    }),
```

<UtilityInjection set="formFields" version="4.x" extras="Data;;array<string, mixed>;;$data;;The data from the form in the modal.||Schema;;Filament\Schemas\Schema;;$schema;;The schema object for the form in the modal.">The `updateOptionUsing()` method can inject various utilities into the function as parameters.</UtilityInjection>

### Handling `MorphTo` relationships

`MorphTo` relationships are special, since they give the user the ability to select records from a range of different models. Because of this, we have a dedicated `MorphToSelect` component which is not actually a select field, rather 2 select fields inside a fieldset. The first select field allows you to select the type, and the second allows you to select the record of that type.

To use the `MorphToSelect`, you must pass `types()` into the component, which tell it how to render options for different types:

```php
use Filament\Forms\Components\MorphToSelect;

MorphToSelect::make('commentable')
    ->types([
        MorphToSelect\Type::make(Product::class)
            ->titleAttribute('name'),
        MorphToSelect\Type::make(Post::class)
            ->titleAttribute('title'),
    ])
```

<UtilityInjection set="formFields" version="4.x">The `types()` method can inject various utilities into the function as parameters.</UtilityInjection>

#### Customizing the option labels for each morphed type

The `titleAttribute()` is used to extract the titles out of each product or post. If you'd like to customize the label of each option, you can use the `getOptionLabelFromRecordUsing()` method to transform the Eloquent model into a label:

```php
use Filament\Forms\Components\MorphToSelect;

MorphToSelect::make('commentable')
    ->types([
        MorphToSelect\Type::make(Product::class)
            ->getOptionLabelFromRecordUsing(fn (Product $record): string => "{$record->name} - {$record->slug}"),
        MorphToSelect\Type::make(Post::class)
            ->titleAttribute('title'),
    ])
```

#### Customizing the relationship query for each morphed type

You may customize the database query that retrieves options using the `modifyOptionsQueryUsing()` method:

```php
use Filament\Forms\Components\MorphToSelect;
use Illuminate\Database\Eloquent\Builder;

MorphToSelect::make('commentable')
    ->types([
        MorphToSelect\Type::make(Product::class)
            ->titleAttribute('name')
            ->modifyOptionsQueryUsing(fn (Builder $query) => $query->whereBelongsTo($this->team)),
        MorphToSelect\Type::make(Post::class)
            ->titleAttribute('title')
            ->modifyOptionsQueryUsing(fn (Builder $query) => $query->whereBelongsTo($this->team)),
    ])
```

<UtilityInjection set="formFields" version="4.x" extras="Eloquent query builder;;Illuminate\Database\Eloquent\Builder;;$query;;The query builder to modify.">The `modifyOptionsQueryUsing()` method can inject various utilities into the function as parameters.</UtilityInjection>

<Aside variant="tip">
    Many of the same options in the select field are available for `MorphToSelect`, including `searchable()`, `preload()`, `native()`, `allowHtml()`, and `optionsLimit()`.
</Aside>

#### Customizing the morph select fields

You may further customize the "key" select field for a specific morph type using the `modifyKeySelectUsing()` method:

```php
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

MorphToSelect::make('commentable')
    ->types([
        MorphToSelect\Type::make(Product::class)
            ->titleAttribute('name')
            ->modifyKeySelectUsing(fn (Select $select): Select => $select
                ->createOptionForm([
                    TextInput::make('title')
                        ->required(),
                ])
                ->createOptionUsing(function (array $data): int {
                    return Product::create($data)->getKey();
                })),
        MorphToSelect\Type::make(Post::class)
            ->titleAttribute('title'),
    ])
```

This is useful if you want to customize the "key" select field for each morphed type individually. If you want to customize the key select for all types, you can use the `modifyKeySelectUsing()` method on the `MorphToSelect` component itself:

```php
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;

MorphToSelect::make('commentable')
    ->types([
        MorphToSelect\Type::make(Product::class)
            ->titleAttribute('name'),
        MorphToSelect\Type::make(Post::class)
            ->titleAttribute('title'),
    ])
    ->modifyKeySelectUsing(fn (Select $select): Select => $select->native())
```

You can also modify the "type" select field using the `modifyTypeSelectUsing()` method:

```php
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;

MorphToSelect::make('commentable')
    ->types([
        MorphToSelect\Type::make(Product::class)
            ->titleAttribute('name'),
        MorphToSelect\Type::make(Post::class)
            ->titleAttribute('title'),
    ])
    ->modifyTypeSelectUsing(fn (Select $select): Select => $select->native())
```

## Allowing HTML in the option labels

By default, Filament will escape any HTML in the option labels. If you'd like to allow HTML, you can use the `allowHtml()` method:

```php
use Filament\Forms\Components\Select;

Select::make('technology')
    ->options([
        'tailwind' => '<span class="text-blue-500">Tailwind</span>',
        'alpine' => '<span class="text-green-500">Alpine</span>',
        'laravel' => '<span class="text-red-500">Laravel</span>',
        'livewire' => '<span class="text-pink-500">Livewire</span>',
    ])
    ->searchable()
    ->allowHtml()
```

<Aside variant="danger">
    Be aware that you will need to ensure that the HTML is safe to render, otherwise your application will be vulnerable to XSS attacks.
</Aside>

Optionally, you may pass a boolean value to control if the input should allow HTML or not:

```php
use Filament\Forms\Components\Select;

Select::make('technology')
    ->options([
        'tailwind' => '<span class="text-blue-500">Tailwind</span>',
        'alpine' => '<span class="text-green-500">Alpine</span>',
        'laravel' => '<span class="text-red-500">Laravel</span>',
        'livewire' => '<span class="text-pink-500">Livewire</span>',
    ])
    ->searchable()
    ->allowHtml(FeatureFlag::active())
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `allowHtml()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Wrap or truncate option labels

When using the JavaScript select, labels that exceed the width of the select element will wrap onto multiple lines by default. Alternatively, you may choose to truncate overflowing labels.

```php
use Filament\Forms\Components\Select;

Select::make('truncate')
    ->wrapOptionLabels(false)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `wrapOptionLabels()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Disable placeholder selection

You can prevent the placeholder (null option) from being selected using the `selectablePlaceholder(false)` method:

```php
use Filament\Forms\Components\Select;

Select::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
    ->default('draft')
    ->selectablePlaceholder(false)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `selectablePlaceholder()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Disabling specific options

You can disable specific options using the `disableOptionWhen()` method. It accepts a closure, in which you can check if the option with a specific `$value` should be disabled:

```php
use Filament\Forms\Components\Select;

Select::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
    ->default('draft')
    ->disableOptionWhen(fn (string $value): bool => $value === 'published')
```

<UtilityInjection set="formFields" version="4.x" extras="Option value;;mixed;;$value;;The value of the option to disable.||Option label;;string | Illuminate\Contracts\Support\Htmlable;;$label;;The label of the option to disable.">You can inject various utilities into the function as parameters.</UtilityInjection>

## Adding affix text aside the field

You may place text before and after the input using the `prefix()` and `suffix()` methods:

```php
use Filament\Forms\Components\Select;

Select::make('domain')
    ->prefix('https://')
    ->suffix('.com')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing static values, the `prefix()` and `suffix()` methods also accept a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/select/affix" alt="Select with affixes" version="4.x" />

### Using icons as affixes

You may place an [icon](../styling/icons) before and after the input using the `prefixIcon()` and `suffixIcon()` methods:

```php
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;

Select::make('domain')
    ->suffixIcon(Heroicon::GlobeAlt)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing static values, the `prefixIcon()` and `suffixIcon()` methods also accept a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/select/suffix-icon" alt="Select with suffix icon" version="4.x" />

#### Setting the affix icon's color

Affix icons are gray by default, but you may set a different color using the `prefixIconColor()` and `suffixIconColor()` methods:

```php
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;

Select::make('domain')
    ->suffixIcon(Heroicon::CheckCircle)
    ->suffixIconColor('success')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing static values, the `prefixIconColor()` and `suffixIconColor()` methods also accept a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

## Limiting the number of options

You can limit the number of options that are displayed in a searchable select or multi-select using the `optionsLimit()` method. The default is 50:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->searchable()
    ->optionsLimit(20)
```

Ensure that you are not raising the limit too high, as this may cause the select to become slow and unresponsive due to high in-browser memory usage.

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `optionsLimit()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Boolean options

If you want a simple boolean select, with "Yes" and "No" options, you can use the `boolean()` method:

```php
use Filament\Forms\Components\Select;

Select::make('feedback')
    ->label('Like this post?')
    ->boolean()
```

To customize the "Yes" label, you can use the `trueLabel` argument on the `boolean()` method:

```php
use Filament\Forms\Components\Select;

Select::make('feedback')
    ->label('Like this post?')
    ->boolean(trueLabel: 'Absolutely!')
```

To customize the "No" label, you can use the `falseLabel` argument on the `boolean()` method:

```php
use Filament\Forms\Components\Select;

Select::make('feedback')
    ->label('Like this post?')
    ->boolean(falseLabel: 'Not at all!')
```

To customize the placeholder that shows when an option has not yet been selected, you can use the `placeholder` argument on the `boolean()` method:

```php
use Filament\Forms\Components\Select;

Select::make('feedback')
    ->label('Like this post?')
    ->boolean(placeholder: 'Make your mind up...')
```

## Selecting options from a table in a modal

You can use the `ModalTableSelect` component to open a Filament [table](../tables) in a modal, allowing users to select records from it. This is useful when you have a [relationship](#integrating-with-an-eloquent-relationship) that has a lot of records, and you want users to be able to perform advanced filtering and searching through them.

To use the `ModalTableSelect`, you must have a table configuration class for the model. You can generate one of these classes using the `make:filament-table` command:

```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('parent')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }
}
```

The class must have a `configure()` method that accepts the `Table` object and returns it. The class name needs to be passed to the `tableConfiguration()` method of the `ModalTableSelect` component:

```php
use Filament\Forms\Components\ModalTableSelect;

ModalTableSelect::make('category_id')
    ->relationship('category', 'name')
    ->tableConfiguration(CategoriesTable::class)
```

You can also use the `multiple()` method with a multiple relationship such as `BelongsToMany`:

```php
use Filament\Forms\Components\ModalTableSelect;

ModalTableSelect::make('categories')
    ->relationship('categories', 'name')
    ->multiple()
    ->tableConfiguration(CategoriesTable::class)
```

<UtilityInjection set="formFields" version="4.x">The `tableConfiguration()` method can inject various utilities into the function as parameters.</UtilityInjection>

### Customizing the modal table select actions

You can customize the "Select" button and modal using the [action](../actions) object configuration methods. Passing a function to the `selectAction()` method allows you to modify the `$action` object, for example, to change the button label and the modal heading:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\ModalTableSelect;

ModalTableSelect::make('category_id')
    ->relationship('category', 'name')
    ->tableConfiguration(CategoriesTable::class)
    ->selectAction(
        fn (Action $action) => $action
            ->label('Select a category')
            ->modalHeading('Search categories')
            ->modalSubmitActionLabel('Confirm selection'),
    )
```

<UtilityInjection set="formFields" version="4.x" extras="Action;;Filament\Actions\Action;;$action;;The action object to customize.">The `selectAction()` method can inject various utilities into the function as parameters.</UtilityInjection>

### Customizing the option labels in the modal table select

The `getOptionLabelFromRecordUsing()` method can be used to customize the label of each selected option. This is useful if you want to display a more descriptive label or concatenate two columns together:

```php
use Filament\Forms\Components\ModalTableSelect;

ModalTableSelect::make('category_id')
    ->relationship('category', 'name')
    ->tableConfiguration(CategoriesTable::class)
    ->getOptionLabelFromRecordUsing(fn (Category $record): string => "{$record->name} ({$record->slug})")
```

By default, `multiple()` options are listed in a "badge" design, and singular options are listed in plain text. The `badge()` method can be used to define whether the option label should appear inside a badge:

```php
use Filament\Forms\Components\ModalTableSelect;

ModalTableSelect::make('category_id')
    ->relationship('category', 'name')
    ->tableConfiguration(CategoriesTable::class)
    ->badge()

ModalTableSelect::make('categories')
    ->relationship('categories', 'name')
    ->multiple()
    ->tableConfiguration(CategoriesTable::class)
    ->badge(false)
```

The `badgeColor()` method can be used to set the badge [color](../styling/colors):

```php
use Filament\Forms\Components\ModalTableSelect;

ModalTableSelect::make('categories')
    ->relationship('categories', 'name')
    ->multiple()
    ->tableConfiguration(CategoriesTable::class)
    ->badgeColor('success')
```

### Passing additional arguments to the table in a modal select

You can pass arguments from your form to the table configuration class using the `tableArguments()` method. For example, this can be used to modify the table's query based on previously filled form fields:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\ModalTableSelect;
use Filament\Schemas\Components\Utilities\Get;

ModalTableSelect::make('products')
    ->relationship('products', 'name')
    ->multiple()
    ->tableConfiguration(ProductsTable::class)
    ->tableArguments(function (Get $get): array {
        return [
            'category_id' => $get('category_id'),
            'budget_limit' => $get('budget'),
        ];
    })
```

<UtilityInjection set="formFields" version="4.x">The `tableArguments()` method can inject various utilities into the function as parameters.</UtilityInjection>

In your table configuration class, you can access these arguments using the `$table->getArguments()` method:

```php
use Filament\Forms\Components\TableSelect\Livewire\TableSelectLivewireComponent;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) use ($table): Builder {
                $arguments = $table->getArguments();
            
                if ($categoryId = $arguments['category_id'] ?? null) {
                    $query->where('category_id', $categoryId);
                }
                
                if ($budgetLimit = $arguments['budget_limit'] ?? null) {
                    $query->where('price', '<=', $budgetLimit);
                }
                
                return $query;
            })
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('price')
                    ->money(),
                TextColumn::make('category.name')
                    ->hidden(filled($table->getArguments()['category_id'])),
            ]);
    }
}
```

## Select validation

As well as all rules listed on the [validation](validation) page, there are additional rules that are specific to selects.

### Valid options validation (`in()` rule)

The [`in()`](validation#in) rule ensures that users cannot select an option that is not in the list of options. This is an important rule for data integrity purposes, so Filament applies it by default to all select fields.

<Aside variant="warning">
    Selected option validation is crucial, so we strongly suggest that you [write automated tests](../testing/testing-schemas#testing-form-validation) for your forms to ensure that the validation works as expected.
</Aside>

Since there are many ways for a select field to populate its options, and in many cases the options are not all loaded into the select by default and require searching to retrieve them, Filament uses the presence of a valid "option label" to determine whether the selected value exists. It also checks if that option is [disabled](#disabling-specific-options) or not.

If you are using a custom search query to retrieve options, you should ensure that the `getOptionLabelUsing()` method is defined, so that Filament can validate the selected value against the available options:

```php
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->searchable()
    ->getSearchResultsUsing(fn (string $search): array => Author::query()
        ->where('name', 'like', "%{$search}%")
        ->limit(50)
        ->pluck('name', 'id')
        ->all())
    ->getOptionLabelUsing(fn (string $value): ?string => Author::find($value)?->name),
```

The `getOptionLabelUsing()` method should return `null` if the option is not valid, to allow Filament to determine that the selected value is not in the list of options. If the option is valid, it should return the label of the option.

If you are using a `multiple()` select or multi-select, you should define `getOptionLabelsUsing()` instead of `getOptionLabelUsing()`. `$values` will be passed into the callback instead of `$value`, and you should return a `$key => $value` array of labels and their corresponding values:

```php
use Filament\Forms\Components\Select;

Select::make('technologies')
    ->multiple()
    ->searchable()
    ->getSearchResultsUsing(fn (string $search): array => Technology::query()
        ->where('name', 'like', "%{$search}%")
        ->limit(50)
        ->pluck('name', 'id')
        ->all())
    ->getOptionLabelsUsing(fn (array $values): array => Technology::query()
        ->whereIn('id', $values)
        ->pluck('name', 'id')
        ->all()),
```

If you are using the `relationship()` method, the `getOptionLabelUsing()` or `getOptionLabelsUsing()` methods will be automatically defined for you, so you don't need to worry about them.

### Number of selected items validation

You can validate the minimum and maximum number of items that you can select in a [multi-select](#multi-select) by setting the `minItems()` and `maxItems()` methods:

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
    ->minItems(1)
    ->maxItems(3)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing static values, the `minItems()` and `maxItems()` methods also accept a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

## Customizing the select action objects

This field uses action objects for easy customization of buttons within it. You can customize these buttons by passing a function to an action registration method. The function has access to the `$action` object, which you can use to [customize it](../actions/overview) or [customize its modal](../actions/modals). The following methods are available to customize the actions:

- `createOptionAction()`
- `editOptionAction()`
- `manageOptionActions()` (for customizing both the create and edit option actions at once)

Here is an example of how you might customize an action:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\Select;

Select::make('author_id')
    ->relationship(name: 'author', titleAttribute: 'name')
    ->createOptionAction(
        fn (Action $action) => $action->modalWidth('3xl'),
    )
```

<UtilityInjection set="formFields" version="4.x" extras="Action;;Filament\Actions\Action;;$action;;The action object to customize.">The action registration methods can inject various utilities into the function as parameters.</UtilityInjection>
