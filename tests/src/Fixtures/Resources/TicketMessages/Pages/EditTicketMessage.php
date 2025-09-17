<?php

namespace Filament\Tests\Fixtures\Resources\TicketMessages\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Tests\Fixtures\Resources\TicketMessages\TicketMessageResource;

class EditTicketMessage extends EditRecord
{
    protected static string $resource = TicketMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
