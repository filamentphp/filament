<?php

namespace Filament\Tests\App\Fixtures\Resources\ProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tests\App\Fixtures\Resources\ProductResource;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
