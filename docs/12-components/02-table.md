---
title: Rendering a table in a Blade view
---
import Aside from "@components/Aside.astro"

<Aside variant="warning">
    Before proceeding, make sure `filament/tables` is installed in your project. You can check by running:

    ```bash
    composer show filament/tables
    ```
    If it's not installed, consult the [installation guide](../introduction/installation#installing-the-individual-components) and configure the **individual components** according to the instructions.
</Aside>

## Setting up the Livewire component

First, generate a new Livewire component:

```bash
php artisan make:livewire ListProducts
```

Then, render your Livewire component on the page:

```blade
@livewire('list-products')
```

Alternatively, you can use a full-page Livewire component:

```php
use App\Livewire\ListProducts;
use Illuminate\Support\Facades\Route;

Route::get('products', ListProducts::class);
```

## Adding the table

There are 3 tasks when adding a table to a Livewire component class:

1) Implement the `HasTable` and `HasSchemas` interfaces, and use the `InteractsWithTable` and `InteractsWithSchemas` traits.
2) Add a `table()` method, which is where you configure the table. [Add the table's columns, filters, and actions](getting-started#columns).
3) Make sure to define the base query that will be used to fetch rows in the table. For example, if you're listing products from your `Product` model, you will want to return `Product::query()`.

```php
<?php

namespace App\Livewire;

use App\Models\Shop\Product;
use Filament\Actions\Concerns\InteractsWithActions;  
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListProducts extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;
    
    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query())
            ->columns([
                TextColumn::make('name'),
            ])
            ->filters([
                // ...
            ])
            ->recordActions([
                // ...
            ])
            ->toolbarActions([
                // ...
            ]);
    }
    
    public function render(): View
    {
        return view('livewire.list-products');
    }
}
```

Finally, in your Livewire component's view, render the table:

```blade
<div>
    {{ $this->table }}
</div>
```

Visit your Livewire component in the browser, and you should see the table.

<Aside variant="info">

    `filament/tables` also includes the following packages:
    
    - `filament/actions`
    - `filament/forms`
    - `filament/support`
    
    These packages allow you to use their components within Livewire components.
    For example, if your table uses [Actions](action#setting-up-the-livewire-component), remember to implement the `HasActions` interface and include the `InteractsWithActions` trait.
    
    If you are using any other [Filament components](overview#package-components) in your table, make sure to install and integrate the corresponding package as well.
</Aside>

## Building a table for an Eloquent relationship

If you want to build a table for an Eloquent relationship, you can use the `relationship()` and `inverseRelationship()` methods on the `$table` instead of passing a `query()`. `HasMany`, `HasManyThrough`, `BelongsToMany`, `MorphMany` and `MorphToMany` relationships are compatible:

```php
use App\Models\Category;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

public Category $category;

public function table(Table $table): Table
{
    return $table
        ->relationship(fn (): BelongsToMany => $this->category->products())
        ->inverseRelationship('categories')
        ->columns([
            TextColumn::make('name'),
        ]);
}
```

In this example, we have a `$category` property which holds a `Category` model instance. The category has a relationship named `products`. We use a function to return the relationship instance. This is a many-to-many relationship, so the inverse relationship is called `categories`, and is defined on the `Product` model. We just need to pass the name of this relationship to the `inverseRelationship()` method, not the whole instance.

Now that the table is using a relationship instead of a plain Eloquent query, all actions will be performed on the relationship instead of the query. For example, if you use a [`CreateAction`](../actions/create), the new product will be automatically attached to the category.

If your relationship uses a pivot table, you can use all pivot columns as if they were normal columns on your table, as long as they are listed in the `withPivot()` method of the relationship *and* inverse relationship definition.

Relationship tables are used in the Panel Builder as ["relation managers"](../resources/managing-relationships#creating-a-relation-manager). Most of the documented features for relation managers are also available for relationship tables. For instance, [attaching and detaching](../resources/managing-relationships#attaching-and-detaching-records) and [associating and dissociating](../resources/managing-relationships#associating-and-dissociating-records) actions.

## Generating table Livewire components with the CLI

It's advised that you learn how to set up a Livewire component with the Table Builder manually, but once you are confident, you can use the CLI to generate a table for you.

```bash
php artisan make:livewire-table Products/ListProducts
```

This will ask you for the name of a prebuilt model, for example `Product`. Finally, it will generate a new `app/Livewire/Products/ListProducts.php` component, which you can customize.

### Automatically generating table columns

Filament is also able to guess which table columns you want in the table, based on the model's database columns. You can use the `--generate` flag when generating your table:

```bash
php artisan make:livewire-table Products/ListProducts --generate
```
