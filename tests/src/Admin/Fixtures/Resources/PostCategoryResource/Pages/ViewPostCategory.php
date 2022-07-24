<?php

namespace Filament\Tests\Admin\Fixtures\Resources\PostCategoryResource\Pages;

use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tests\Admin\Fixtures\Resources\PostCategoryResource;

class ViewPostCategory extends ViewRecord
{
    protected static string $resource = PostCategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
