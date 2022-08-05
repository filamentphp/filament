<?php

namespace Filament\Tests\Admin\Fixtures\Resources\UserResource\Pages;

use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tests\Admin\Fixtures\Resources\UserResource;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
