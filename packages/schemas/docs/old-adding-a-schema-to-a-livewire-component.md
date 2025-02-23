---
title: Adding an schema to a Livewire component
---

## Setting up the Livewire component

First, generate a new Livewire component:

```bash
php artisan make:livewire ViewProduct
```

Then, render your Livewire component on the page:

```blade
@livewire('view-product')
```

Alternatively, you can use a full-page Livewire component:

```php
use App\Livewire\ViewProduct;
use Illuminate\Support\Facades\Route;

Route::get('products/{product}', ViewProduct::class);
```

You must use the `InteractsWithSchemas` trait, and implement the `HasSchemas` interface on your Livewire component class:

```php
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Component;

class ViewProduct extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    // ...
}
```

## Adding the schema

Next, add a method to the Livewire component which accepts a `$schema` object, modifies it, and returns it:

```php
use Filament\Schemas\Schema;

public function productSchema(Schema $schema): Schema
{
    return $schema
        ->schema([
            // ...
        ]);
}
```

Finally, render the infolist in the Livewire component's view:

```blade
{{ $this->productSchema }}
```
