<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ManageUserNotifications extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'notifications';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-bell';

    public static function getNavigationLabel(): string
    {
        return 'Notifications';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('type')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ]);
    }
}
