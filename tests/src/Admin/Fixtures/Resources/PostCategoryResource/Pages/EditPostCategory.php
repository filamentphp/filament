<?php

namespace Filament\Tests\Admin\Fixtures\Resources\PostCategoryResource\Pages;

use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Tests\Admin\Fixtures\Resources\PostCategoryResource;

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
