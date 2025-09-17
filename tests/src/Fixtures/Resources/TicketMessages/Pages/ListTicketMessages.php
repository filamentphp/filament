<?php

namespace Filament\Tests\Fixtures\Resources\TicketMessages\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tests\Fixtures\Resources\TicketMessages\TicketMessageResource;

class ListTicketMessages extends ListRecords
{
    protected static string $resource = TicketMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
