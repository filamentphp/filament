<?php

namespace Filament\Tests\Panels\Fixtures\Resources\ProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tests\Panels\Fixtures\Resources\ProductResource;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
