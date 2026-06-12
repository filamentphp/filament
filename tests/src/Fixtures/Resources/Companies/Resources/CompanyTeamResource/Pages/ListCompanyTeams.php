<?php

namespace Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource;

class ListCompanyTeams extends ListRecords
{
    protected static string $resource = CompanyTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
