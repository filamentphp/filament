---
title: Getting started
---

Resources are static classes that are used to build CRUD interfaces for your Eloquent models. They describe how administrators should be able to interact with data from your app - using tables and forms.

## Creating a resource

To create a resource for the `App\Models\Customer` model:

```bash
php artisan make:filament-resource Customer
```

This will create several files in the `app/Filament/Resources` directory:

```
.
+-- CustomerResource.php
+-- CustomerResource
|   +-- Pages
|   |   +-- CreateCustomer.php
|   |   +-- EditCustomer.php
|   |   +-- ListCustomers.php
```

Your new resource class lives in `CustomerResource.php`.

The classes in the `Pages` directory are used to customize the pages in the admin panel that interact with your resource. They're all full-page [Livewire](https://laravel-livewire.com) components that you can customize in any way you wish.

> Have you created a resource, but it's not appearing in the navigation menu? If you have a [model policy](#authorization), make sure you return `true` from the `viewAny()` method.

### Simple (modal) resources

Sometimes, your models are simple enough that you only want to manage records on one page, using modals to create, edit and delete records. To generate a simple resource with modals:

```bash
php artisan make:filament-resource Customer --simple
```

Your resource will have a "Manage" page, which is a List page with modals added.

Additionally, your simple resource will have no `getRelations()` method, as [relation managers](relation-managers) are only displayed on the Edit and View pages, which are not present in simple resources. Everything else is the same.

### Automatically generating forms and tables

If you'd like to save time, Filament can automatically generate the [form](#forms) and [table](#table) for you, based on your model's database columns.

The `doctrine/dbal` package is required to use this functionality:

```bash
composer require doctrine/dbal --dev
```

When creating your resource, you may now use `--generate`:

```bash
php artisan make:filament-resource Customer --generate
```

> Note: If your table contains ENUM columns, `doctrine/dbal` is unable to scan your table and will crash. Hence Filament is unable to generate the schema for your resource if it contains an ENUM column. Read more about this issue [here](https://github.com/doctrine/dbal/issues/3819#issuecomment-573419808).

### Handling soft deletes

By default, you will not be able to interact with deleted records in the admin panel. If you'd like to add functionality to restore, force delete and filter trashed records in your resource, use the `--soft-deletes` flag when generating the resource:

```bash
php artisan make:filament-resource Customer --soft-deletes
```

You can find out more about soft deleting [here](deleting-records#handling-soft-deletes).

### Generating a View page

By default, only List, Create and Edit pages are generated for your resource. If you'd also like a [View page](viewing-records), use the `--view` flag:

```bash
php artisan make:filament-resource Customer --view
```

## Record titles

A `$recordTitleAttribute` may be set for your resource, which is the name of the column on your model that can be used to identify it from others.

For example, this could be a blog post's `title` or a customer's `name`:

```php
protected static ?string $recordTitleAttribute = 'name';
```

This is required for features like [global search](global-search) to work.

> You may specify the name of an [Eloquent accessor](https://laravel.com/docs/eloquent-mutators#defining-an-accessor) if just one column is inadequate at identifying a record.

## Forms

Resource classes contain a `form()` method that is used to build the [forms](../../forms/getting-started) on the [Create](creating-records) and [Edit](editing-records) pages:

```php
use Filament\Forms;
use Filament\Forms\Form;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
            // ...
        ]);
}
```

The `schema()` method is used to define the structure of your form. It is an array of [fields](../../forms/fields) and [layout components](../../forms/layout), in the order they should appear in your form.

Check out the Forms docs for a [list of available fields](../../forms/fields/getting-started#available-fields), and a [list of form layout components use](../../forms/layout/getting-started#available-layout-components) you can use to structure those fields.

### Hiding components contextually

The `hiddenOn()` method of form components allows you to dynamically hide fields based on the current page or action.

In this example, we hide the `password` field on the `edit` page:

```php
use Livewire\Component;

Forms\Components\TextInput::make('password')
    ->password()
    ->required()
    ->hiddenOn('edit'),
```

Alternatively, we have a `visibleOn()` shortcut method for only showing a field on one page or action:

```php
use Livewire\Component;

Forms\Components\TextInput::make('password')
    ->password()
    ->required()
    ->visibleOn('create'),
```

## Table

Resource classes contain a `table()` method that is used to build the [table](../../tables/getting-started) on the [List page](listing-records):

```php
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('email'),
            // ...
        ])
        ->filters([
            Tables\Filters\Filter::make('verified')
                ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
            // ...
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
}
```

Check out the [listing records](listing-records) docs to find out how to add table [columns](listing-records#columns), [filters](listing-records#filters), [actions](listing-records#actions), [bulk actions](listing-records#bulk-actions) and more.

## Authorization

For authorization, Filament will observe any [model policies](https://laravel.com/docs/authorization#creating-policies) that are registered in your app. The following methods are used:

- `viewAny()` is used to completely hide resources from the navigation menu, and prevents the user from accessing any pages.
- `create()` is used to control [creating new records](creating-records).
- `update()` is used to control [editing a record](editing-records).
- `view()` is used to control [viewing a record](viewing-records).
- `delete()` is used to prevent a single record from being deleted. `deleteAny()` is used to prevent records from being bulk deleted. Filament uses the `deleteAny()` method because iterating through multiple records and checking the `delete()` policy is not very performant.
- `forceDelete()` is used to prevent a single soft-deleted record from being force-deleted. `forceDeleteAny()` is used to prevent records from being bulk force-deleted. Filament uses the `forceDeleteAny()` method because iterating through multiple records and checking the `forceDelete()` policy is not very performant.
- `restore()` is used to prevent a single soft-deleted record from being restored. `restoreAny()` is used to prevent records from being bulk restored. Filament uses the `restoreAny()` method because iterating through multiple records and checking the `restore()` policy is not very performant.
- `reorder()` is used to control [reordering a record](listing-records#reordering-records).

## Model labels

Each resource has a "model label" which is automatically generated from the model name. For example, an `App\Models\Customer` model will have a `customer` label.

The label is used in several parts of the UI, and you may customize it using the `$modelLabel` property:

```php
protected static ?string $modelLabel = 'cliente';
```

Alternatively, you may use the `getModelLabel()` to define a dynamic label:

```php
public static function getModelLabel(): string
{
    return __('filament/resources/customer.label');
}
```

### Plural model label

Resources also have a "plural model label" which is automatically generated from the model label. For example, a `customer` label will be pluralized into `customers`.

You may customize the plural version of the label using the `$pluralModelLabel` property:

```php
protected static ?string $pluralModelLabel = 'clientes';
```

Alternatively, you may set a dynamic plural label in the `getPluralModelLabel()` method:

```php
public static function getPluralModelLabel(): string
{
    return __('filament/resources/customer.plural_label');
}
```

## Navigation

Filament will automatically generate a navigation menu item for your resource using the [plural label](#plural-label).

If you'd like to customize the navigation item label, you may use the `$navigationLabel` property:

```php
protected static ?string $navigationLabel = 'Mis Clientes';
```

Alternatively, you may set a dynamic navigation label in the `getNavigationLabel()` method:

```php
public static function getNavigationLabel(): string
{
    return __('filament/resources/customer.navigation_label');
}
```

### Icons

The `$navigationIcon` property supports the name of any Blade component. By default, the [Blade Heroicons v1](https://github.com/blade-ui-kit/blade-heroicons/tree/1.3.1) package is installed, so you may use the name of any [Heroicons v1](https://v1.heroicons.com) out of the box. However, you may create your own custom icon components or install an alternative library if you wish.

```php
protected static ?string $navigationIcon = 'heroicon-o-user-group';
```

Alternatively, you may set a dynamic navigation icon in the `getNavigationIcon()` method:

```php
public static function getNavigationIcon(): string
{
    return 'heroicon-o-user-group';
}
```

### Sorting navigation items

The `$navigationSort` property allows you to specify the order in which navigation items are listed:

```php
protected static ?int $navigationSort = 2;
```

Alternatively, you may set a dynamic navigation item order in the `getNavigationSort()` method:

```php
public static function getNavigationSort(): ?int
{
    return 2;
}
```

### Grouping navigation items

You may group navigation items by specifying a `$navigationGroup` property:

```php
protected static ?string $navigationGroup = 'Shop';
```

Alternatively, you may use the `getNavigationGroup()` method to set a dynamic group label:

```php
protected static function getNavigationGroup(): ?string
{
    return __('filament/navigation.groups.shop');
}
```

## Customizing the Eloquent query

Within Filament, every query to your resource model will start with the `getEloquentQuery()` method.

Because of this, it's very easy to apply your own [model scopes](https://laravel.com/docs/eloquent#query-scopes) that affect the entire resource. You can use this to implement [multi-tenancy](#multi-tenancy) easily within the admin panel.

### Disabling global scopes

By default, Filament will observe all global scopes that are registered to your model. However, this may not be ideal if you wish to access, for example, soft deleted records.

To overcome this, you may override the `getEloquentQuery()` method that Filament uses:

```php
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->withoutGlobalScopes();
}
```

Alternatively, you may remove specific global scopes:

```php
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->withoutGlobalScopes([ActiveScope::class]);
}
```

More information about removing global scopes may be found in the [Laravel documentation](https://laravel.com/docs/eloquent#removing-global-scopes).

## Customizing the URL slug

By default, Filament will generate a URL based on the name of the resource. You can customize this by setting the `$slug` property on the resource:

```php
protected static ?string $slug = 'pending-orders';
```

## Multi-tenancy

Multi-tenancy, simply, is the concept of users "owning" records, and only being able to access the records that they own within Filament.

### Simple multi-tenancy from scratch

Simple multi-tenancy is easy to set up with Filament.

First, scope the [base Eloquent query](#customizing-the-eloquent-query) for every "owned" resource by defining the `getEloquentQuery()` method:

```php
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->whereBelongsTo(auth()->user());
}
```

In this example we use `whereBelongsTo()` to scope the records to only those that belong to the currently authenticated user. However, you may use whatever Eloquent method you wish here, including a manual `where()` clause, or a [scope](https://laravel.com/docs/eloquent#local-scopes).

Finally, you need to ensure that records are attached to the current user when they are first created. The easiest way to do this is through a [model observer](https://laravel.com/docs/eloquent#observers):

```php
public function creating(Post $post): void
{
    $post->user()->associate(auth()->user());
}
```

Additionally, you may want to scope the options available in the [relation manager](relation-managers) `AttachAction` or `AssociateAction`:

```php
use Filament\Tables\Actions\AttachAction;
use Illuminate\Database\Eloquent\Builder;

AttachAction::make()
    ->recordSelectOptionsQuery(fn (Builder $query) => $query->whereBelongsTo(auth()->user())
```

### `stancl/tenancy`

To set up [`stancl/tenancy`](https://tenancyforlaravel.com/docs) to work with Filament, you just need to add the `InitializeTenancyByDomain::class` middleware to [Livewire](https://tenancyforlaravel.com/docs/v3/integrations/livewire) and the [Filament config file](../installation#publishing-configuration):

```php
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

'middleware' => [
    // ...
    'base' => [
        // ...
        InitializeTenancyByDomain::class
    ],
],
```

## Deleting pages

If you'd like to delete a page from your resource, you can just delete the page file from the `Pages` directory of your resource, and its entry in the `getPages()` method.

For example, you may have a resource with records that may not be created by anyone. Delete the `Create` page file, and then remove it from `getPages()`:

```php
public static function getPages(): array
{
    return [
        'index' => Pages\ListCustomers::route('/'),
        'edit' => Pages\EditCustomer::route('/{record}/edit'),
    ];
}
```

