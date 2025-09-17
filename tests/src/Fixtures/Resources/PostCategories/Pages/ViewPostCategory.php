<?php

namespace Filament\Tests\Fixtures\Resources\PostCategories\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tests\Fixtures\Resources\PostCategories\PostCategoryResource;

class ViewPostCategory extends ViewRecord
{
    protected static string $resource = PostCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
