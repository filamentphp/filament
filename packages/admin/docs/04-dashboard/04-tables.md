---
title: Tables
---

Filament comes with a "table" widget template, which you can use to display a table of data without needing to write a custom view.

Start by creating a widget with the command:

```bash
php artisan make:filament-widget LatestOrders --table
```

Then update the `getTableQuery()` and `getTableColumns()` methods to return the data query and columns you want to display:

```php
<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Closure;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestOrders extends BaseWidget
{
    protected function getTableQuery(): Builder
    {
        return Order::query()->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('customer.name')
                ->label('Customer'),
        ];
    }
}
```

Now, check out your widget in the dashboard.

Table widgets support all features of the [Table Builder](../../tables), including [filters](../../tables/filters) and [actions](../../tables/actions).
