---
title: Resources
---

Resources are static classes that describe how administrators should be able to interact with data from your app. They are associated with Eloquent models from your app.

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

Your new resource class lives in `CustomerResource.php`. Resource classes register [forms](#forms), [tables](#tables), [relations](#relations), and [pages](#pages) associated with that model.

The classes in the `Pages` directory are used to customize the pages in the admin panel that interact with your resource. They're all full-page [Livewire](https://laravel-livewire.com) components that you can customize in any way you wish.

By default, the model associated with your resource is guessed based on the class name of the resource. You may set the static `$model` property to disable this behaviour:

```php
protected static string $model = Customer::class;
```

A label for this resource is generated based on the name of the resource's model. You may customize it using the static `$label` property:

```php
protected static string $label = 'customer';
```

The plural version is generated based on the singular `$label`, which you may also override:

```php
protected static string $pluralLabel = 'customers';
```

## Forms

Resource classes contain a static `form()` method that is used to build the forms on the create and edit pages:

```php
use Filament\Forms;
use Filament\Resources\Form;

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

The `schema()` method is used to define the structure of your form. It is an array of [fields](/docs/forms/fields), in the order they should appear in your form.

To view a full list of available form [fields](/docs/forms/fields) and [layout components](/docs/forms/layout), see the [Form Builder documentation](/docs/forms/fields).

> You may also use the same form builder outside of the admin panel, by following [these installation instructions](/docs/forms/installation).

## Tables

Resource classes contain a static `table()` method that is used to build the table on the list page:

```php
use Filament\Resources\Table;
use Filament\Tables;
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
                ->apply(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
            // ...
        ]);
}
```

The `columns()` method is used to define the [columns](/docs/tables/columns) in your table. It is an array of column objects, in the order they should appear in your table.

[Filters](/docs/tables/filters) are predefined scopes that administrators can use to filter records in your table. The `filters()` method is used to register these.

To view a full list of available table [columns](/docs/tables/columns), see the [Table Builder documentation](/docs/tables/columns).

> You may also use the same table builder outside of the admin panel, by following [these installation instructions](/docs/tables/installation).

## Relations

"Relation managers" in Filament allow administrators to list, create, attach, edit, detach and delete related records without leaving the resource's edit page. Resource classes contain a static `relations()` method that is used to register relation managers for your resource.

### `HasMany` and `MorphMany`

To create a relation manager for a `HasMany` or `MorphMany` relationship, you can use:

```bash
php artisan make:filament-has-many CategoryResource posts title
```

- `CategoryResource` is the name of the resource class for the parent model.
- `posts` is the name of the relationship you want to manage.
- `title` is the name of the attribute that will be used to identify posts.

This will create a `CategoryResource/RelationManagers/PostsRelationManager.php` file. This contains a class where you are able to define a [form](/docs/forms/fields) and [table](/docs/tables/columns) for your relation manager:

```php
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Tables;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
            // ...
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('title'),
            // ...
        ]);
}
```

You must register the new relation manager in your resource's `relations()` method:

```php
public static function getRelations(): array
{
    return [
        RelationManagers\PostsRelationManager::class,
    ];
}
```

Once a table and form have been defined for the relation manager, visit the edit page of your resource to see it in action.

### `BelongsToMany`

To create a relation manager for a `BelongsToMany` relationship, you can use:

```bash
php artisan make:filament-belongs-to-many UserResource teams name
```

- `UserResource` is the name of the resource class for the parent model.
- `teams` is the name of the relationship you want to manage.
- `name` is the name of the attribute that will be used to identify teams.

This will create a `UserResource/RelationManagers/TeamsRelationManager.php` file. This contains a class where you are able to define a [form](/docs/forms/fields) and [table](/docs/tables/columns) for your relation manager:

```php
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Tables;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')->required(),
            // ...
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name'),
            // ...
        ]);
}
```

You must register the new relation manager in your resource's `relations()` method:

```php
public static function getRelations(): array
{
    return [
        RelationManagers\TeamsRelationManager::class,
    ];
}
```

Once a table and form have been defined for the relation manager, visit the edit page of your resource to see it in action.

For relationships with unconventional naming conventions, you may wish to override the `$inverseRelationship` property on the relation manager:

```php
protected static ?string $inverseRelationship = 'members'; // Since the inverse related model is `User`, this is normally `users`, not `members`.
```

#### Pivot attributes

Relation managers may also be used to edit pivot table attributes. For example, if you have a `TeamsRelationManager` for your `UserResource`, and you want to add the `role` pivot attribute to the relation manager table and form, you can use:

```php
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Tables;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('role')->required(),
            // ...
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('role'),
            // ...
        ]);
}
```

When you attach record with the `Attach` button, you may wish to define a custom form to add pivot attributes to the relationship:

```php
use Filament\Forms;
use Filament\Resources\Form;

public static function attachForm(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('role')->required(),
            // ...
        ]);
}
```

## Pages

Pages are classes that are associated with a resource. They are full-page [Livewire](https://laravel-livewire.com) components with a few extra utilities you can use with the admin panel.

Page class files are in the `/Pages` directory of your resource directory.

By default, resources are generated with three pages:

- List has a [table](#tables) for displaying, searching and deleting resource records. From here, you are able to access the create and edit pages. It is routed to `/`.
- Create has a [form](#forms) that is able to create a resource record. It is routed to `/create`.
- Edit has a [form](#forms) that is able to update a resource record, along with the [relation managers](#relations) registered to your resource. It is routed to `/{record}/edit`.

### View page

Filament also comes with a "view" page for resources, which you can enable by creating a new page in your resource's `Pages` directory:

```bash
php artisan make:filament-page ViewUser --resource=UserResource 
```

Inside the new page class, you may extend the `Filament\Resources\Pages\ViewRecord` class and remove the `$view` property:

```php
<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
}
```

You must register this new page in your resource's `getPages()` method:

```php
public static function getPages(): array
{
    return [
        'index' => Pages\ListUsers::route('/'),
        'create' => Pages\CreateUser::route('/create'),
        'view' => Pages\ViewUser::route('/{record}'),
        'edit' => Pages\EditUser::route('/{record}/edit'),
    ];
}
```

### Hooks

Hooks may be used to customize the behaviour of a default page. To set up a hook, create a protected method on the page class with the name of the hook:

```php
protected function beforeSave(): void
{
    // ...
}
```

In this example, the code in the `beforeSave()` method will be called before the data in the form is saved to the database.

There are several available hooks for the create and edit pages:

```php
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    // ...
    
    protected function beforeFill(): void
    {
        // Runs before the form fields are populated with their default values.
    }
    
    protected function afterFill(): void
    {
        // Runs after the form fields are populated with their default values.
    }
    
    protected function beforeValidate(): void
    {
        // Runs before the form fields are validated when the form is submitted.
    }
    
    protected function afterValidate(): void
    {
        // Runs after the form fields are validated when the form is submitted.
    }
    
    protected function beforeCreate(): void
    {
        // Runs before the form fields are saved to the database.
    }
    
    protected function afterCreate(): void
    {
        // Runs after the form fields are saved to the database.
    }
}
```

```php
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    // ...
    
    protected function beforeFill(): void
    {
        // Runs before the form fields are populated from the database.
    }
    
    protected function afterFill(): void
    {
        // Runs after the form fields are populated from the database.
    }
    
    protected function beforeValidate(): void
    {
        // Runs before the form fields are validated when the form is saved.
    }
    
    protected function afterValidate(): void
    {
        // Runs after the form fields are validated when the form is saved.
    }
    
    protected function beforeSave(): void
    {
        // Runs before the form fields are saved to the database.
    }
    
    protected function afterSave(): void
    {
        // Runs after the form fields are saved to the database.
    }
    
    protected function beforeDelete(): void
    {
        // Runs before the record is deleted.
    }
    
    protected function afterDelete(): void
    {
        // Runs after the record is deleted.
    }
}
```

### Custom views

For further customization opportunities, you can override the static `$view` property on any page to a custom view in your app:

```php
protected static string $view = 'filament.resources.users.pages.list-users';
```

### Custom Pages

Filament allows you to create completely custom pages for resources. To create a new page, you can use:

```bash
php artisan make:filament-page SortUsers --resource=UserResource
```

This command will create two files - a page class in the `/Pages` directory of your resource directory, and a view in the `/pages` directory of the resource views directory.

You must register custom pages to a route in the static `getPages()` method of your resource:

```php
public static function getPages(): array
{
    return [
        // ...
        'sort' => Pages\SortUsers::route('/sort'),
    ];
}
```

Any [parameters](https://laravel.com/docs/routing#route-parameters) defined in the route's path will be available to the page class, in an identical way to [Livewire](https://laravel-livewire.com/docs/rendering-components#route-params).

To generate a URL for a resource route, you may call the static `getUrl()` method on the page class:

```php
SortUsers::getUrl($parameters = [], $absolute = true);
```

## Authorization

For authorization, Filament will observe any [model policies](https://laravel.com/docs/authorization#creating-policies) that are registered in your app. The `viewAny` action may be used to completely disable resources and remove them from the navigation menu.
