<?php

namespace Filament\Tests\Admin\Fixtures\Resources\ProductResource\Pages;

use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tests\Admin\Fixtures\Resources\ProductResource;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
