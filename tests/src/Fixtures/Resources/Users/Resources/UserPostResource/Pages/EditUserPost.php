<?php

namespace Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource;

class EditUserPost extends EditRecord
{
    protected static string $resource = UserPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
