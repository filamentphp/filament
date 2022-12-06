---
title: Adding a table to a Livewire component
---

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

### Adding the table

There are 2 tasks when adding a table to a Livewire component class:

1) Implement the `HasTable` interface and use the `InteractsWithTable` trait.
2) Add a `table()` method, which is where you configure the table. [Add the table's columns, filters, and actions](getting-started#columns).

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListProducts extends Component implements HasTable // [tl! focus]
{
    use InteractsWithTable; // [tl! focus]
    
    public function table(Table $table): Table
    {
        return $table
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
    } // [tl! focus:start]
    
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