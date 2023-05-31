<?php

namespace Filament\Tests\Panels\Fixtures\Resources\PostCategoryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Tests\Panels\Fixtures\Resources\PostCategoryResource;

class EditPostCategory extends EditRecord
{
    protected static string $resource = PostCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
