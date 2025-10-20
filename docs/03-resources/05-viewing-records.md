---
title: Viewing records
---

## Creating a resource with a View page

To create a new resource with a View page, you can use the `--view` flag:

```bash
php artisan make:filament-resource User --view
```

## Using an infolist instead of a disabled form

By default, the View page will display a disabled form with the record's data. If you preferred to display the record's data in an "infolist", you can define an `infolist()` method on the resource class:

```php
use Filament\Infolists;
use Filament\Schemas\Schema;

public static function infolist(Schema $schema): Schema
{
    return $schema
        ->components([
            Infolists\Components\TextEntry::make('name'),
            Infolists\Components\TextEntry::make('email'),
            Infolists\Components\TextEntry::make('notes')
                ->columnSpanFull(),
        ]);
}
```

The `components()` method is used to define the structure of your infolist. It is an array of [entries](../infolists#available-entries) and [layout components](../schemas/layout#available-layout-components), in the order they should appear in your infolist.

Check out the Infolists docs for a [guide](../infolists) on how to build infolists with Filament.

## Adding a View page to an existing resource

If you want to add a View page to an existing resource, create a new page in your resource's `Pages` directory:

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

## Viewing records in modals

If your resource is simple, you may wish to view records in modals rather than on the [View page](viewing-records). If this is the case, you can just [delete the view page](overview#deleting-resource-pages).

If your resource doesn't contain a `ViewAction`, you can add one to the `$table->recordActions()` array:

```php
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->recordActions([
            ViewAction::make(),
            // ...
        ]);
}
```

## Customizing data before filling the form

You may wish to modify the data from a record before it is filled into the form. To do this, you may define a `mutateFormDataBeforeFill()` method on the View page class to modify the `$data` array, and return the modified version before it is filled into the form:

```php
protected function mutateFormDataBeforeFill(array $data): array
{
    $data['user_id'] = auth()->id();

    return $data;
}
```

Alternatively, if you're viewing records in a modal action, check out the [Actions documentation](../actions/view#customizing-data-before-filling-the-form).

## Lifecycle hooks

Hooks may be used to execute code at various points within a page's lifecycle, like before a form is filled. To set up a hook, create a protected method on the View page class with the name of the hook:

```php
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    // ...

    protected function beforeFill(): void
    {
        // Runs before the disabled form fields are populated from the database. Not run on pages using an infolist.
    }

    protected function afterFill(): void
    {
        // Runs after the disabled form fields are populated from the database. Not run on pages using an infolist.
    }
}
```

## Authorization

For authorization, Filament will observe any [model policies](https://laravel.com/docs/authorization#creating-policies) that are registered in your app.

Users may access the View page if the `view()` method of the model policy returns `true`.

## Creating another View page

One View page may not be enough space to allow users to navigate a lot of information. You can create as many View pages for a resource as you want. This is especially useful if you are using [resource sub-navigation](overview#resource-sub-navigation), as you are then easily able to switch between the different View pages.

To create a View page, you should use the `make:filament-page` command:

```bash
php artisan make:filament-page ViewCustomerContact --resource=CustomerResource --type=ViewRecord
```

You must register this new page in your resource's `getPages()` method:

```php
public static function getPages(): array
{
    return [
        'index' => Pages\ListCustomers::route('/'),
        'create' => Pages\CreateCustomer::route('/create'),
        'view' => Pages\ViewCustomer::route('/{record}'),
        'view-contact' => Pages\ViewCustomerContact::route('/{record}/contact'),
        'edit' => Pages\EditCustomer::route('/{record}/edit'),
    ];
}
```

Now, you can define the `infolist()` or `form()` for this page, which can contain other components that are not present on the main View page:

```php
use Filament\Schemas\Schema;

public function infolist(Schema $schema): Schema
{
    return $schema
        ->components([
            // ...
        ]);
}
```

## Customizing relation managers for a specific view page

You can specify which relation managers should appear on a view page by defining a `getAllRelationManagers()` method:

```php
protected function getAllRelationManagers(): array
{
    return [
        CustomerAddressesRelationManager::class,
        CustomerContactsRelationManager::class,
    ];
}
```

This is useful when you have [multiple view pages](#creating-another-view-page) and need different relation managers on
each page:

```php
// ViewCustomer.php
protected function getAllRelationManagers(): array
{
    return [
        RelationManagers\OrdersRelationManager::class,
        RelationManagers\SubscriptionsRelationManager::class,
    ];
}

// ViewCustomerContact.php 
protected function getAllRelationManagers(): array
{
    return [
        RelationManagers\ContactsRelationManager::class,
        RelationManagers\AddressesRelationManager::class,
    ];
}
```

If `getAllRelationManagers()` isn't defined, any relation managers defined in the resource will be used.

## Adding view pages to resource sub-navigation

If you're using [resource sub-navigation](overview#resource-sub-navigation), you can register this page as normal in `getRecordSubNavigation()` of the resource:

```php
use App\Filament\Resources\Customers\Pages;
use Filament\Resources\Pages\Page;

public static function getRecordSubNavigation(Page $page): array
{
    return $page->generateNavigationItems([
        // ...
        Pages\ViewCustomerContact::class,
    ]);
}
```

## Custom page content

Each page in Filament has its own [schema](../schemas), which defines the overall structure and content. You can override the schema for the page by defining a `content()` method on it. The `content()` method for the View page contains the following components by default:

```php
use Filament\Schemas\Schema;

public function content(Schema $schema): Schema
{
    return $schema
        ->components([
            $this->hasInfolist() // This method returns `true` if the page has an infolist defined
                ? $this->getInfolistContentComponent() // This method returns a component to display the infolist that is defined in this resource
                : $this->getFormContentComponent(), // This method returns a component to display the form that is defined in this resource
            $this->getRelationManagersContentComponent(), // This method returns a component to display the relation managers that are defined in this resource
        ]);
}
```

Inside the `components()` array, you can insert any [schema component](../schemas). You can reorder the components by changing the order of the array or remove any of the components that are not needed.

### Using a custom Blade view

For further customization opportunities, you can override the static `$view` property on the page class to a custom view in your app:

```php
protected string $view = 'filament.resources.users.pages.view-user';
```

This assumes that you have created a view at `resources/views/filament/resources/users/pages/view-user.blade.php`:

```blade
<x-filament-panels::page>
    {{-- `$this->getRecord()` will return the current Eloquent record for this page --}}
    
    {{ $this->content }} {{-- This will render the content of the page defined in the `content()` method, which can be removed if you want to start from scratch --}}
</x-filament-panels::page>
```
