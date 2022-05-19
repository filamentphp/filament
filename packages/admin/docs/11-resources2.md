---
title: Resources
---

## Pages

Pages are classes that are associated with a resource. They are full-page [Livewire](https://laravel-livewire.com) components with a few extra utilities you can use with the admin panel.

Page class files are in the `/Pages` directory of your resource directory.

By default, resources are generated with three pages:

- List has a [table](#tables) for displaying, searching and deleting resource records. From here, you are able to access the create and edit pages. It is routed to `/`.
- Create has a [form](#forms) that is able to create a resource record. It is routed to `/create`.
- Edit has a [form](#forms) that is able to update a resource record, along with the [relation managers](#relation-managers) registered to your resource. It is routed to `/{record}/edit`.

### View page

Filament also comes with a "view" page for resources. By default, this contains a form with all inputs disabled, allowing the user to access information without being able to edit it.

To create a new resource with a view page, you can use the `--view-page` flag:

```bash
php artisan make:filament-resource User --view-page
```

#### Adding a view page to an existing resource

If you want to add a view page to an existing resource, create a new page in your resource's `Pages` directory:

```bash
php artisan make:filament-page ViewUser --resource=UserResource --type=ViewRecord
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

### Customizing data before saving

On create pages, you may define a `mutateFormDataBeforeCreate()` method to modify the form data before it is saved to the database:

```php
protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['user_id'] = auth()->id();

    return $data;
}
```

On edit pages, you may do the same using the `mutateFormDataBeforeSave()` method:

```php
protected function mutateFormDataBeforeSave(array $data): array
{
    $data['last_edited_by_id'] = auth()->id();

    return $data;
}
```

### Customizing data before filling the form

On edit pages, you may define a `mutateFormDataBeforeFill()` method to modify the record data before it is filled into the form:

```php
protected function mutateFormDataBeforeFill(array $data): array
{
    $data['user_id'] = auth()->id();

    return $data;
}
```

### Customizing form redirects

You may specify a custom redirect URL for the Create and Edit pages by overriding the `getRedirectUrl()` method.

For example, the Create form can redirect back to the List page when it is submitted:

```php
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
```

### Lifecycle hooks

Hooks may be used to execute methods at various points within a page's lifecycle, like before a form is saved. To set up a hook, create a protected method on the page class with the name of the hook:

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

### Custom actions

"Actions" are buttons that are displayed on pages, which allow the user to run a Livewire method on the page or visit a URL.

On resource pages, actions are usually in 2 places: in the top right of the page, and below the form.

For example, you may add a new button action next to "Delete" on the edit page that runs the `impersonate()` Livewire method:

```php
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    // ...

    protected function getActions(): array
    {
        return array_merge(parent::getActions(), [
            Action::make('impersonate')->action('impersonate'),
        ]);
    }

    public function impersonate(): void
    {
        // ...
    }
}
```

Or, a new button next to "Save" below the form:

```php
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    // ...

    protected function getFormActions(): array
    {
        return array_merge(parent::getFormActions(), [
            Action::make('close')->action('saveAndClose'),
        ]);
    }

    public function saveAndClose(): void
    {
        // ...
    }
}
```

To view the entire actions API, please visit the [pages section](pages#actions).

### Custom views

For further customization opportunities, you can override the static `$view` property on any page to a custom view in your app:

```php
protected static string $view = 'filament.resources.users.pages.list-users';
```

### Custom Pages

Filament allows you to create completely custom pages for resources. To create a new page, you can use:

```bash
php artisan make:filament-page SortUsers --resource=UserResource --type=custom
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

### Building widgets

Filament allows you to display widgets inside pages, below the header and above the footer.

You can use an existing [dashboard widget](dashboard), or create one specifically for the resource.

To get started building a resource widget:

```bash
php artisan make:filament-widget CustomerOverview --resource=CustomerResource
```

This command will create two files - a widget class in the `app/Filament/Resources/CustomerResource/Widgets` directory, and a view in the `resources/views/filament/resources/customer-resource/widgets` directory.

You must register the new widget in your resource's `getWidgets()` method:

```php
public static function getWidgets(): array
{
    return [
        Widgets\CustomerOverview::class,
    ];
}
```

To display a widget on a resource page, use the `getHeaderWidgets()` or `getFooterWidgets()` methods for that page:

```php
<?php
 
namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;

class ListCustomers extends ListRecords
{
    public static string $resource = CustomerResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            CustomerResource\Widgets\CustomerOverview::class,
        ];
    }
}
```

If you're using a widget on an Edit or View page, you may access the current record by defining a `$record` property on the widget class:

```php
use Illuminate\Database\Eloquent\Model;

public ?Model $record = null;
```

### Deleting pages

If you'd like to delete a page from your resource, you can just delete the page file from the `Pages` directory of your resource, and its entry in the `getPages()` method.

For example, you may have a resource with records that may not be created by anyone. Delete the `Create` page, and then remove it from `getPages()`:

```php
public static function getPages(): array
{
    return [
        'index' => Pages\ListCustomers::route('/'),
        'edit' => Pages\EditCustomer::route('/{record}/edit'),
    ];
}
```
