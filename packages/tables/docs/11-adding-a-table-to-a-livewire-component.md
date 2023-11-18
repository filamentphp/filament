---
title: Adding a table to a Livewire component
---

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

1) Implement the `HasTable` and `HasForms` interfaces, and use the `InteractsWithTable` and `InteractsWithForms` traits.
2) Add a `table()` method, which is where you configure the table. [Add the table's columns, filters, and actions](getting-started#columns).
3) Make sure to define the base query that will be used to fetch rows in the table. For example, if you're listing products from your `Product` model, you will want to return `Product::query()`.

```php
<?php

namespace App\Livewire;

use App\Models\Shop\Product;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListProducts extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    
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
            ->actions([
                // ...
            ])
            ->bulkActions([
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

Now that the table is using a relationship instead of a plain Eloquent query, all actions will be performed on the relationship instead of the query. For example, if you use a [`CreateAction`](../actions/prebuilt-actions/create), the new product will be automatically attached to the category.

If your relationship uses a pivot table, you can use all pivot columns as if they were normal columns on your table, as long as they are listed in the `withPivot()` method of the relationship *and* inverse relationship definition.

Relationship tables are used in the Panel Builder as ["relation managers"](../panels/resources/relation-managers#creating-a-relation-manager). Most of the documented features for relation managers are also available for relationship tables. For instance, [attaching and detaching](../panels/resources/relation-managers#attaching-and-detaching-records) and [associating and dissociating](../panels/resources/relation-managers#associating-and-dissociating-records) actions.

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

> If your table contains ENUM columns, the `doctrine/dbal` package we use is unable to scan your table and will crash. Hence, Filament is unable to generate the schema for your table if it contains an ENUM column. Read more about this issue [here](https://github.com/doctrine/dbal/issues/3819#issuecomment-573419808).
