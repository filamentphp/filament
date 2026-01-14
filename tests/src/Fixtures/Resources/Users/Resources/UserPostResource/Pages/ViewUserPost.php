<?php

namespace Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource;

class ViewUserPost extends ViewRecord
{
    protected static string $resource = UserPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
