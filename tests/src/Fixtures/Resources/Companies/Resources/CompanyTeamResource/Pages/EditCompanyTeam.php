<?php

namespace Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource;

class EditCompanyTeam extends EditRecord
{
    protected static string $resource = CompanyTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
