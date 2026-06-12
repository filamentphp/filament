<?php

namespace Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource;

class ListUserPosts extends ListRecords
{
    protected static string $resource = UserPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
