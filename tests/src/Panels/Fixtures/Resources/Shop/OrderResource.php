<?php

namespace Filament\Tests\Panels\Fixtures\Resources\Shop;

use Filament\Resources\Resource;
use Filament\Tests\Models\Shop\Order;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
}
