<?php

namespace Filament\Tests\Panels\Fixtures\Resources\ProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Tests\Panels\Fixtures\Resources\ProductResource;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
