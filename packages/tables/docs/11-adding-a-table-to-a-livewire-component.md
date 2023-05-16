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
use App\Http\Livewire\ListProducts;
use Illuminate\Support\Facades\Route;

Route::get('products', ListProducts::class);
```

## Adding the table

There are 3 tasks when adding a table to a Livewire component class:

1) Implement the `HasTable` interface and use the `InteractsWithTable` trait.
2) Add a `table()` method, which is where you configure the table. [Add the table's columns, filters, and actions](getting-started#columns).
3) Make sure to define the base query that will be used to fetch rows in the table. For example, if you're listing products from your `Product` model, you will want to return `Product::query()`.

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\Shop\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListProducts extends Component implements HasTable
{
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
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }
    
    public function render(): View
    {
        return view('list-products');
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

![](https://user-images.githubusercontent.com/41773797/147614478-5b40c645-107e-40ac-ba41-f0feb99dd480.png)

## Generating table Livewire components with the CLI

It's advised that you learn how to set up a Livewire component with the table builder manually, but once you are confident, you can use the CLI to generate a table for you.

```bash
php artisan make:livewire-table Products/ListProducts
```

This will ask you for the name of a prebuilt model, for example `Product`. Finally, it will generate a new `app/Http/Livewire/Products/ListProducts.php` component, which you can customize.

### Automatically generating table columns

Filament is also able to guess which table columns you want in the table, based on the model's database columns. The `doctrine/dbal` package is required to use this functionality:

```bash
composer require doctrine/dbal --dev
```

Now, you can use the `--generate` flag when generating your table:

```bash
php artisan make:livewire-table Products/ListProducts --generate
```

> Note: If your table contains ENUM columns, `doctrine/dbal` is unable to scan your table and will crash. Hence Filament is unable to generate the schema for your table if it contains an ENUM column. Read more about this issue [here](https://github.com/doctrine/dbal/issues/3819#issuecomment-573419808).
