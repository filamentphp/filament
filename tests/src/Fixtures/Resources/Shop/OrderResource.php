<?php

namespace Filament\Tests\Fixtures\Resources\Shop;

use Filament\Resources\Resource;
use Filament\Tests\Fixtures\Models\Shop\Order;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
}
