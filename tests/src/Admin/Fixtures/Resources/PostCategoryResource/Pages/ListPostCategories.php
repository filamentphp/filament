<?php

namespace Filament\Tests\Admin\Fixtures\Resources\PostCategoryResource\Pages;

use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tests\Admin\Fixtures\Resources\PostCategoryResource;

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
