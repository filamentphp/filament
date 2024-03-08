---
title: Advanced forms
---

## Overview

Filament Form Builder is designed to be flexible and customizable. Many existing form builders allow users to define a form schema, but don't provide a great interface for defining inter-field interactions, or custom logic. Since all Filament forms are built on top of [Livewire](https://livewire.laravel.com), the form can adapt dynamically to user input, even after it has been initially rendered. Developers can use [parameter injection](#form-component-utility-injection) to access many utilities in real time and build dynamic forms based on user input. The [lifecycle](#field-lifecycle) of fields is open to extension using hook functions to define custom functionality for each field. This allows developers to build complex forms with ease.

## The basics of reactivity

[Livewire](https://livewire.laravel.com) is a tool that allows Blade-rendered HTML to dynamically re-render without requiring a full page reload. Filament forms are built on top of Livewire, so they are able to re-render dynamically, allowing their layout to adapt after they are initially rendered.

By default, when a user uses a field, the form will not re-render. Since rendering requires a round-trip to the server, this is a performance optimization. However, if you wish to re-render the form after the user has interacted with a field, you can use the `live()` method:

```php
use Filament\Forms\Components\Select;

Select::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
    ->live()
```

In this example, when the user changes the value of the `status` field, the form will re-render. This allows you to then make changes to fields in the form based on the new value of the `status` field. Also, you can [hook in to the field's lifecycle](#field-updates) to perform custom logic when the field is updated.

### Reactive fields on blur

By default, when a field is set to `live()`, the form will re-render every time the field is interacted with. However, this may not be appropriate for some fields like the text input, since making network requests while the user is still typing results in suboptimal performance. You may wish to re-render the form only after the user has finished using the field, when it becomes out of focus. You can do this using the `live(onBlur: true)` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('username')
    ->live(onBlur: true)
```

### Debouncing reactive fields

You may wish to find a middle ground between `live()` and `live(onBlur: true)`, using "debouncing". Debouncing will prevent a network request from being sent until a user has finished typing for a certain period of time. You can do this using the `live(debounce: 500)` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('username')
    ->live(debounce: 500) // Wait 500ms before re-rendering the form.
```

In this example, `500` is the number of milliseconds to wait before sending a network request. You can customize this number to whatever you want, or even use a string like `'1s'`.

## Form component utility injection

The vast majority of methods used to configure [fields](fields/getting-started) and [layout components](layout/getting-started) accept functions as parameters instead of hardcoded values:

```php
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

DatePicker::make('date_of_birth')
    ->displayFormat(function (): string {
        if (auth()->user()->country_id === 'us') {
            return 'm/d/Y';
        }

        return 'd/m/Y';
    })

Select::make('user_id')
    ->options(function (): array {
        return User::all()->pluck('name', 'id')->all();
    })

TextInput::make('middle_name')
    ->required(fn (): bool => auth()->user()->hasMiddleName())
```

This alone unlocks many customization possibilities.

The package is also able to inject many utilities to use inside these functions, as parameters. All customization methods that accept functions as arguments can inject utilities.

These injected utilities require specific parameter names to be used. Otherwise, Filament doesn't know what to inject.

### Injecting the current state of a field

If you wish to access the current state (value) of the field, define a `$state` parameter:

```php
function ($state) {
    // ...
}
```

### Injecting the current form component instance

If you wish to access the current component instance, define a `$component` parameter:

```php
use Filament\Forms\Components\Component;

function (Component $component) {
    // ...
}
```

### Injecting the current Livewire component instance

If you wish to access the current Livewire component instance, define a `$livewire` parameter:

```php
use Livewire\Component as Livewire;

function (Livewire $livewire) {
    // ...
}
```

### Injecting the current form record

If your form is associated with an Eloquent model instance, define a `$record` parameter:

```php
use Illuminate\Database\Eloquent\Model;

function (?Model $record) {
    // ...
}
```

### Injecting the state of another field

You may also retrieve the state (value) of another field from within a callback, using a `$get` parameter:

```php
use Filament\Forms\Get;

function (Get $get) {
    $email = $get('email'); // Store the value of the `email` field in the `$email` variable.
    //...
}
```

### Injecting a function to set the state of another field

In a similar way to `$get`, you may also set the value of another field from within a callback, using a `$set` parameter:

```php
use Filament\Forms\Set;

function (Set $set) {
    $set('title', 'Blog Post'); // Set the `title` field to `Blog Post`.
    //...
}
```

When this function is run, the state of the `title` field will be updated, and the form will re-render with the new title. This is useful inside the [`afterStateUpdated`](#field-updates) method.

### Injecting the current form operation

If you're writing a form for a panel resource or relation manager, and you wish to check if a form is `create`, `edit` or `view`, use the `$operation` parameter:

```php
function (string $operation) {
    // ...
}
```

> Outside the panel, you can set a form's operation by using the `operation()` method on the form definition.

### Injecting multiple utilities

The parameters are injected dynamically using reflection, so you are able to combine multiple parameters in any order:

```php
use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Component as Livewire;

function (Livewire $livewire, Get $get, Set $set) {
    // ...
}
```

### Injecting dependencies from Laravel's container

You may inject anything from Laravel's container like normal, alongside utilities:

```php
use Filament\Forms\Set;
use Illuminate\Http\Request;

function (Request $request, Set $set) {
    // ...
}
```

## Field lifecycle

Each field in a form has a lifecycle, which is the process it goes through when the form is loaded, when it is interacted with by the user, and when it is submitted. You may customize what happens at each stage of this lifecycle using a function that gets run at that stage.

### Field hydration

Hydration is the process that fills fields with data. It runs when you call the form's `fill()` method. You may customize what happens after a field is hydrated using the `afterStateHydrated()` method.

In this example, the `name` field will always be hydrated with the correctly capitalized name:

```php
use Closure;
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->required()
    ->afterStateHydrated(function (TextInput $component, string $state) {
        $component->state(ucwords($state));
    })
```

As a shortcut for formatting the field's state like this when it is hydrated, you can use the `formatStateUsing()` method:

```php
use Closure;
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->formatStateUsing(fn (string $state): string => ucwords($state))
```

### Field updates

You may use the `afterStateUpdated()` method to customize what happens after a field is updated by the user. Only changes from the user on the frontend will trigger this function, not manual changes to the state from `$set()` or another PHP function.

Inside this function, you can also inject the `$old` value of the field before it was updated, using the `$old` parameter:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->afterStateUpdated(function (?string $state, ?string $old) {
        // ...
    })
```

For an example of how to use this method, learn how to [automatically generate a slug from a title](#generating-a-slug-from-a-title).

### Field dehydration

Dehydration is the process that gets data from the fields in your forms, and transforms it. It runs when you call the form's `getState()` method.

You may customize how the state is transformed when it is dehydrated using the `dehydrateStateUsing()` function. In this example, the `name` field will always be dehydrated with the correctly capitalized name:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->required()
    ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
```

#### Preventing a field from being dehydrated

You may also prevent a field from being dehydrated altogether using `dehydrated(false)`. In this example, the field will not be present in the array returned from `getState()`:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('password_confirmation')
    ->password()
    ->dehydrated(false)
```

If your form auto-saves data to the database, like in a [resource](../panels/resources/getting-started) or [table action](../tables/actions), this is useful to prevent a field from being saved to the database if it is purely used for presentational purposes.

## Reactive forms cookbook

This section contains a collection of recipes for common tasks you may need to perform when building an advanced form.

### Conditionally hiding a field

To conditionally hide or show a field, you can pass a function to the `hidden()` method, and return `true` or `false` depending on whether you want the field to be hidden or not. The function can [inject utilities](#form-component-utility-injection) as parameters, so you can do things like check the value of another field:

```php
use Filament\Forms\Get;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;

Checkbox::make('is_company')
    ->live()

TextInput::make('company_name')
    ->hidden(fn (Get $get): bool => ! $get('is_company'))
```

In this example, the `is_company` checkbox is [`live()`](#the-basics-of-reactivity). This allows the form to rerender when the value of the `is_company` field changes. You can access the value of that field from within the `hidden()` function using the [`$get()` utility](#injecting-the-current-state-of-a-field). The value of the field is inverted using `!` so that the `company_name` field is hidden when the `is_company` field is `false`.

Alternatively, you can use the `visible()` method to show a field conditionally. It does the exact inverse of `hidden()`, and could be used if you prefer the clarity of the code when written this way:

```php
use Filament\Forms\Get;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;

Checkbox::make('is_company')
    ->live()
    
TextInput::make('company_name')
    ->visible(fn (Get $get): bool => $get('is_company'))
```

### Conditionally making a field required

To conditionally make a field required, you can pass a function to the `required()` method, and return `true` or `false` depending on whether you want the field to be required or not. The function can [inject utilities](#form-component-utility-injection) as parameters, so you can do things like check the value of another field:

```php
use Filament\Forms\Get;
use Filament\Forms\Components\TextInput;

TextInput::make('company_name')
    ->live(onBlur: true)
    
TextInput::make('vat_number')
    ->required(fn (Get $get): bool => filled($get('company_name')))
```

In this example, the `company_name` field is [`live(onBlur: true)`](#reactive-fields-on-blur). This allows the form to rerender after the value of the `company_name` field changes and the user clicks away. You can access the value of that field from within the `required()` function using the [`$get()` utility](#injecting-the-current-state-of-a-field). The value of the field is checked using `filled()` so that the `vat_number` field is required when the `company_name` field is not `null` or an empty string. The result is that the `vat_number` field is only required when the `company_name` field is filled in.

Using a function is able to make any other [validation rule](validation) dynamic in a similar way.

### Generating a slug from a title

To generate a slug from a title while the user is typing, you can use the [`afterStateUpdated()` method](#field-updates) on the title field to [`$set()`](#injecting-a-function-to-set-the-state-of-another-field) the value of the slug field:

```php
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Support\Str;

TextInput::make('title')
    ->live()
    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
    
TextInput::make('slug')
```

In this example, the `title` field is [`live()`](#the-basics-of-reactivity). This allows the form to rerender when the value of the `title` field changes. The `afterStateUpdated()` method is used to run a function after the state of the `title` field is updated. The function injects the [`$set()` utility](#injecting-a-function-to-set-the-state-of-another-field) and the new state of the `title` field. The `Str::slug()` utility method is part of Laravel and is used to generate a slug from a string. The `slug` field is then updated using the `$set()` function.

One thing to note is that the user may customize the slug manually, and we don't want to overwrite their changes if the title changes. To prevent this, we can use the old version of the title to work out if the user has modified it themselves. To access the old version of the title, you can inject `$old`, and to get the current value of the slug before it gets changed, we can use the [`$get()` utility](#injecting-the-state-of-another-field):

```php
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;

TextInput::make('title')
    ->live()
    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
        if (($get('slug') ?? '') !== Str::slug($old)) {
            return;
        }
    
        $set('slug', Str::slug($state));
    })
    
TextInput::make('slug')
```

### Dependant select options

To dynamically update the options of a [select field](fields/select) based on the value of another field, you can pass a function to the `options()` method of the select field. The function can [inject utilities](#form-component-utility-injection) as parameters, so you can do things like check the value of another field using the [`$get()` utility](#injecting-the-current-state-of-a-field):

```php
use Filament\Forms\Get;
use Filament\Forms\Components\Select;

Select::make('category')
    ->options([
        'web' => 'Web development',
        'mobile' => 'Mobile development',
        'design' => 'Design',
    ])
    ->live()

Select::make('sub_category')
    ->options(fn (Get $get): array => match ($get('category')) {
        'web' => [
            'frontend_web' => 'Frontend development',
            'backend_web' => 'Backend development',
        ],
        'mobile' => [
            'ios_mobile' => 'iOS development',
            'android_mobile' => 'Android development',
        ],
        'design' => [
            'app_design' => 'Panel design',
            'marketing_website_design' => 'Marketing website design',
        ],
        default => [],
    })
```

In this example, the `category` field is [`live()`](#the-basics-of-reactivity). This allows the form to rerender when the value of the `category` field changes. You can access the value of that field from within the `options()` function using the [`$get()` utility](#injecting-the-current-state-of-a-field). The value of the field is used to determine which options should be available in the `sub_category` field. The `match ()` statement in PHP is used to return an array of options based on the value of the `category` field. The result is that the `sub_category` field will only show options relevant to the selected `category` field.

You could adapt this example to use options loaded from an Eloquent model or other data source, by querying within the function:

```php
use Filament\Forms\Get;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;

Select::make('category')
    ->options(Category::query()->pluck('name', 'id'))
    ->live()
    
Select::make('sub_category')
    ->options(fn (Get $get): Collection => SubCategory::query()
        ->where('category', $get('category'))
        ->pluck('name', 'id'))
```

### Dynamic fields based on a select option

You may wish to render a different set of fields based on the value of a field, like a select. To do this, you can pass a function to the `schema()` method of any [layout component](layout/getting-started), which checks the value of the field and returns a different schema based on that value. Also, you will need a way to initialise the new fields in the dynamic schema when they are first loaded.

```php
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;

Select::make('type')
    ->options([
        'employee' => 'Employee',
        'freelancer' => 'Freelancer',
    ])
    ->live()
    ->afterStateUpdated(fn (Select $component) => $component
        ->getContainer()
        ->getComponent('dynamicTypeFields')
        ->getChildComponentContainer()
        ->fill())
    
Grid::make(2)
    ->schema(fn (Get $get): array => match ($get('type')) {
        'employee' => [
            TextInput::make('employee_number')
                ->required(),
            FileUpload::make('badge')
                ->image()
                ->required(),
        ],
        'freelancer' => [
            TextInput::make('hourly_rate')
                ->numeric()
                ->required()
                ->prefix('€'),
            FileUpload::make('contract')
                ->required(),
        ],
        default => [],
    })
    ->key('dynamicTypeFields')
```

In this example, the `type` field is [`live()`](#the-basics-of-reactivity). This allows the form to rerender when the value of the `type` field changes. The `afterStateUpdated()` method is used to run a function after the state of the `type` field is updated. In this case, we [inject the current select field instance](#injecting-the-current-form-component-instance), which we can then use to get the form "container" instance that holds both the select and the grid components. With this container, we can target the grid component using a unique key (`dynamicTypeFields`) that we have assigned to it. With that grid component instance, we can call `fill()`, just as we do on a normal form to initialise it. The `schema()` method of the grid component is then used to return a different schema based on the value of the `type` field. This is done by using the [`$get()` utility](#injecting-the-current-state-of-a-field), and returning a different schema array dynamically.

### Auto-hashing password field

You have a password field:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('password')
    ->password()
```

And you can use a [dehydration function](#field-dehydration) to hash the password when the form is submitted:

```php
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;

TextInput::make('password')
    ->password()
    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
```

But if your form is used to change an existing password, you don't want to overwrite the existing password if the field is empty. You can [prevent the field from being dehydrated](#preventing-a-field-from-being-dehydrated) if the field is null or an empty string (using the `filled()` helper):

```php
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;

TextInput::make('password')
    ->password()
    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
    ->dehydrated(fn (?string $state): bool => filled($state))
```

However, you want to require the password to be filled when the user is being created, by [injecting the `$operation` utility](#injecting-the-current-form-operation), and then [conditionally making the field required](#conditionally-making-a-field-required):

```php
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;

TextInput::make('password')
    ->password()
    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
    ->dehydrated(fn (?string $state): bool => filled($state))
    ->required(fn (string $operation): bool => $operation === 'create')
```

## Saving data to relationships

> If you're building a form inside your Livewire component, make sure you have set up the [form's model](adding-a-form-to-a-livewire-component#setting-a-form-model). Otherwise, Filament doesn't know which model to use to retrieve the relationship from.

As well as being able to give structure to fields, [layout components](layout/getting-started) are also able to "teleport" their nested fields into a relationship. Filament will handle loading data from a `HasOne`, `BelongsTo` or `MorphOne` Eloquent relationship, and then it will save the data back to the same relationship. To set this behaviour up, you can use the `relationship()` method on any layout component:

```php
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

Fieldset::make('Metadata')
    ->relationship('metadata')
    ->schema([
        TextInput::make('title'),
        Textarea::make('description'),
        FileUpload::make('image'),
    ])
```

In this example, the `title`, `description` and `image` are automatically loaded from the `metadata` relationship, and saved again when the form is submitted. If the `metadata` record does not exist, it is automatically created.

This functionality is not just limited to fieldsets - you can use it with any layout component. For example, you could use a `Group` component which has no styling associated with it:

```php
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;

Group::make()
    ->relationship('customer')
    ->schema([
        TextInput::make('name')
            ->label('Customer')
            ->required(),
        TextInput::make('email')
            ->label('Email address')
            ->email()
            ->required(),
    ])
```

### Saving data to a `BelongsTo` relationship

Please note that if you are saving the data to a `BelongsTo` relationship, then the foreign key column in your database must be `nullable()`. This is because Filament saves the form first, before saving the relationship. Since the form is saved first, the foreign ID does not exist yet, so it must be nullable. Immediately after the form is saved, Filament saves the relationship, which will then fill in the foreign ID and save it again.

It is worth noting that if you have an observer on your form model, then you may need to adapt it to ensure that it does not depend on the relationship existing when it it created. For example, if you have an observer that sends an email to a related record when a form is created, you may need to switch to using a different hook that runs after the relationship is attached, like `updated()`.

### Conditionally saving data to a relationship

Sometimes, saving the related record may be optional. If the user fills out the customer fields, then the customer will be created / updated. Otherwise, the customer will not be created, or will be deleted if it already exists. To do this, you can pass a `condition` function as an argument to `relationship()`, which can use the `$state` of the related form to determine whether the relationship should be saved or not:

```php
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;

Group::make()
    ->relationship(
        'customer',
        condition: fn (?array $state): bool => filled($state['name']),
    )
    ->schema([
        TextInput::make('name')
            ->label('Customer'),
        TextInput::make('email')
            ->label('Email address')
            ->email()
            ->requiredWith('name'),
    ])
```

In this example, the customer's name is not `required()`, and the email address is only required when the `name` is filled. The `condition` function is used to check whether the `name` field is filled, and if it is, then the customer will be created / updated. Otherwise, the customer will not be created, or will be deleted if it already exists.

## Inserting Livewire components into a form

You may insert a Livewire component directly into a form:

```php
use Filament\Forms\Components\Livewire;
use App\Livewire\Foo;

Livewire::make(Foo::class)
```

If you are rendering multiple of the same Livewire component, please make sure to pass a unique `key()` to each:

```php
use Filament\Forms\Components\Livewire;
use App\Livewire\Foo;

Livewire::make(Foo::class)
    ->key('foo-first')

Livewire::make(Foo::class)
    ->key('foo-second')

Livewire::make(Foo::class)
    ->key('foo-third')
```

### Passing parameters to a Livewire component

You can pass an array of parameters to a Livewire component:

```php
use Filament\Forms\Components\Livewire;
use App\Livewire\Foo;

Livewire::make(Foo::class, ['bar' => 'baz'])
```

Now, those parameters will be passed to the Livewire component's `mount()` method:

```php
class Foo extends Component
{
    public function mount(string $bar): void
    {       
        // ...
    }
}
```

Alternatively, they will be available as public properties on the Livewire component:

```php
class Foo extends Component
{
    public string $bar;
}
```

#### Accessing the current record in the Livewire component

You can access the current record in the Livewire component using the `$record` parameter in the `mount()` method, or the `$record` property:

```php
use Illuminate\Database\Eloquent\Model;

class Foo extends Component
{
    public function mount(?Model $record = null): void
    {       
        // ...
    }
    
    // or
    
    public ?Model $record = null;
}
```

Please be aware that when the record has not yet been created, it will be `null`. If you'd like to hide the Livewire component when the record is `null`, you can use the `hidden()` method:

```php
use Filament\Forms\Components\Livewire;
use Illuminate\Database\Eloquent\Model;

Livewire::make(Foo::class)
    ->hidden(fn (?Model $record): bool => $record === null)
```

### Lazy loading a Livewire component

You may allow the component to [lazily load](https://livewire.laravel.com/docs/lazy#rendering-placeholder-html) using the `lazy()` method:

```php
use Filament\Forms\Components\Livewire;
use App\Livewire\Foo;

Livewire::make(Foo::class)->lazy()       
```
