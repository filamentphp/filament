<?php

namespace Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource;

class ViewCompanyTeam extends ViewRecord
{
    protected static string $resource = CompanyTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
