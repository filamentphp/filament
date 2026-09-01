<?php

namespace Filament\Tests\Fixtures\Resources\TicketMessages;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\TicketMessage;
use Filament\Tests\Fixtures\Resources\TicketMessages\Pages\CreateTicketMessage;
use Filament\Tests\Fixtures\Resources\TicketMessages\Pages\EditTicketMessage;
use Filament\Tests\Fixtures\Resources\TicketMessages\Pages\ListTicketMessages;
use Filament\Tests\Fixtures\Resources\TicketMessages\Pages\ViewTicketMessage;
use Filament\Tests\Fixtures\Resources\TicketMessages\Schemas\TicketMessageForm;
use Filament\Tests\Fixtures\Resources\TicketMessages\Schemas\TicketMessageInfolist;
use Filament\Tests\Fixtures\Resources\TicketMessages\Tables\TicketMessagesTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketMessageResource extends Resource
{
    protected static ?string $model = TicketMessage::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TicketMessageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TicketMessageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketMessagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTicketMessages::route('/'),
            'create' => CreateTicketMessage::route('/create'),
            'view' => ViewTicketMessage::route('/{record}'),
            'edit' => EditTicketMessage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
