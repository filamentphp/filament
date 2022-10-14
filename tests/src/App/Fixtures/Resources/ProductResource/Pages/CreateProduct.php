<?php

namespace Filament\Tests\App\Fixtures\Resources\ProductResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Tests\App\Fixtures\Resources\ProductResource;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}
