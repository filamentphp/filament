<?php

namespace Filament\Tests\Admin\Fixtures\Resources\UserResource\Pages;

use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tests\Admin\Fixtures\Resources\UserResource;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
