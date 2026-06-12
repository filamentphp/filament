<?php

namespace Filament\Tests\Fixtures\Resources\Companies\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Resources\Companies\CompanyResource;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource\Pages;

class CompanyTeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return CompanyResource::asParent(static::class);
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\Textarea::make('description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyTeams::route('/'),
            'create' => Pages\CreateCompanyTeam::route('/create'),
            'view' => Pages\ViewCompanyTeam::route('/{record}'),
            'edit' => Pages\EditCompanyTeam::route('/{record}/edit'),
        ];
    }
}
