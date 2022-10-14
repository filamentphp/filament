<?php

namespace Filament\Tests\App\Fixtures\Resources\Shop;

use Filament\Resources\Resource;
use Filament\Tests\Models\Shop\OrderInvoice;

class OrderInvoiceResource extends Resource
{
    protected static ?string $model = OrderInvoice::class;
}
