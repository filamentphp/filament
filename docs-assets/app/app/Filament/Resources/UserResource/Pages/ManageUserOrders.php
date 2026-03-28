<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ManageUserOrders extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'orders';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';

    public static function getNavigationLabel(): string
    {
        return 'Orders';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('number')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->columns([
                Tables\Columns\TextColumn::make('number'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ]);
    }
}
