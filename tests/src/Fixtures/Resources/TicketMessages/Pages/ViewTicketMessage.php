<?php

namespace Filament\Tests\Fixtures\Resources\TicketMessages\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tests\Fixtures\Resources\TicketMessages\TicketMessageResource;

class ViewTicketMessage extends ViewRecord
{
    protected static string $resource = TicketMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
