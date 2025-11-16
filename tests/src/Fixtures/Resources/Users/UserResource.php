<?php

namespace Filament\Tests\Fixtures\Resources\Users;

use BackedEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $globalSearchSort = 5;

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('posts_exists')
                    ->label('Has Posts')
                    ->exists('posts'),
                Tables\Columns\TextColumn::make('posts_count')
                    ->label('# Posts')
                    ->counts('posts'),
                Tables\Columns\TextColumn::make('posts_avg_rating')
                    ->label('Posts Avg. Rating')
                    ->avg('posts', 'rating'),
                Tables\Columns\TextColumn::make('posts_max_rating')
                    ->label('Posts Max. Rating')
                    ->max('posts', 'rating'),
                Tables\Columns\TextColumn::make('posts_min_rating')
                    ->label('Posts Min. Rating')
                    ->min('posts', 'rating'),
                Tables\Columns\TextColumn::make('posts_sum_rating')
                    ->label('Posts Rating Sum')
                    ->sum('posts', 'rating'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
