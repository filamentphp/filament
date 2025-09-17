---
title: Code quality tips
---

## Using schema and table classes

Since many Filament methods define both the UI and the functionality of the app in just one method, it can be easy to end up with giant methods and files. These can be difficult to read, even if your code has a clean and consistent style.

Filament tries to mitigate some of this by providing dedicated schema and table classes when you generate a resource. These classes have a `configure()` method that accepts a `$schema` or `$table`. You can then call the `configure()` method from anywhere you want to define a schema or table.

For example, if you have the following `app/Filament/Resources/Customers/Schemas/CustomerForm.php` file:

```php
namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                // ...
            ]);
    }
}
```

You can use this in the `form()` method of the resource:

```php
use App\Filament\Resources\Customers\Schemas\CustomerForm;
use Filament\Schemas\Schema;

public static function form(Schema $schema): Schema
{
    return CustomerForm::configure($schema);
}
```

You could do the same for the `table()`:

```php
use App\Filament\Resources\Customers\Schemas\CustomersTable;
use Filament\Tables\Table;

public static function table(Table $table): Table
{
    return CustomersTable::configure($table);
}
```

Or the `infolist()`:

```php
use App\Filament\Resources\Customers\Schemas\CustomerInfolist;
use Filament\Schemas\Schema;

public static function infolist(Schema $schema): Schema
{
    return CustomerInfolist::configure($schema);
}
```

These schema and table classes deliberately don't have a parent class or interface by default. If Filament were to enforce a method signature for the `configure()` method, you would not be able to pass your own configuration variables to the method, which could be useful if you wanted to reuse the same class in multiple places but with slight tweaks.

## Using component classes

Even if you are using [schema and table classes](#using-schema-and-table-classes) to keep the schema and table definitions in their own files, you can still end up with a very long `configure()` method. This is especially true if you are using a lot of components in your schema or table, or if the components require a lot of configuration.

You can mitigate this by creating dedicated classes for each component. For example, if you have a `TextInput` component that requires a lot of configuration, you can create a dedicated class for it:

```php
namespace App\Filament\Resources\Customers\Schemas\Components;

use Filament\Forms\Components\TextInput;

class CustomerNameInput
{
    public static function make(): TextInput
    {
        return TextInput::make('name')
            ->label('Full name')
            ->required()
            ->maxLength(255)
            ->placeholder('Enter your full name')
            ->belowContent('This is the name that will be displayed on your profile.');
    }
}
```

You can then use this class in your schema or table:

```php
use App\Filament\Resources\Customers\Schemas\Components\CustomerNameInput;
use Filament\Schemas\Schema;

public static function configure(Schema $schema): Schema
{
    return $schema
        ->components([
            CustomerNameInput::make(),
            // ...
        ]);
}
```

You could do this with a number of different types of component. There are no enforced rules as to how these components should be named or where they should be stored. However, here are some ideas:

- **Schema components**: These could live in the `Schemas/Components` directory of the resource. They could be named after the component they are wrapping, for example `CustomerNameInput` or `CustomerCountrySelect`.
- **Table columns**: These could live in the `Tables/Columns` directory of the resource. They could be named after the column followed by `Column`, for example `CustomerNameColumn` or `CustomerCountryColumn`.
- **Table filters**: These could live in the `Tables/Filters` directory of the resource. They could be named after the filter followed by `Filter`, for example `CustomerCountryFilter` or `CustomerStatusFilter`.
- **Actions**: These could live in the `Actions` directory of the resource. They could be named after the action followed by `Action` or `BulkAction`, for example `EmailCustomerAction` or `UpdateCustomerCountryBulkAction`.

As a further example, here is a potential `EmailCustomerAction` class:

```php
namespace App\Filament\Resources\Customers\Actions;

use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

class EmailCustomerAction
{
    public static function make(): Action
    {
        return Action::make('email')
            ->label('Send email')
            ->icon(Heroicon::Envelope)
            ->schema([
                TextInput::make('subject')
                    ->required()
                    ->maxLength(255),
                Textarea::make('body')
                    ->autosize()
                    ->required(),
            ])
            ->action(function (Customer $customer, array $data) {
                // ...
            });
    }
}
```

And you could use it in the `getHeaderActions()` of a page:

```php
use App\Filament\Resources\Customers\Actions\EmailCustomerAction;

protected function getHeaderActions(): array
{
    return [
        EmailCustomerAction::make(),
    ];
}
```

Or you could use it on a table row:

```php
use App\Filament\Resources\Customers\Actions\EmailCustomerAction;
use Filament\Tables\Table;

public static function configure(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->recordActions([
            EmailCustomerAction::make(),
        ]);
}
```
