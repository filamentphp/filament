<?php

namespace Filament\Tests\App\Fixtures\Resources\PostCategoryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Tests\App\Fixtures\Resources\PostCategoryResource;

class EditPostCategory extends EditRecord
{
    protected static string $resource = PostCategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
