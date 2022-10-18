<?php

namespace Filament\Tests\App\Fixtures\Resources\PostCategoryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tests\App\Fixtures\Resources\PostCategoryResource;

class ListPostCategories extends ListRecords
{
    protected static string $resource = PostCategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
