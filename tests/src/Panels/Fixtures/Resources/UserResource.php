<?php

namespace Filament\Tests\Panels\Fixtures\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Models\User;
use Filament\Tests\Panels\Fixtures\Resources\UserResource\Pages;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->exists('posts')
                    ->label('Has Posts'),
                Tables\Columns\TextColumn::make('posts_count')
                    ->counts('posts')
                    ->label('# Posts'),
                Tables\Columns\TextColumn::make('posts_avg_rating')
                    ->avg('posts', 'rating')
                    ->label('Posts Avg. Rating'),
                Tables\Columns\TextColumn::make('posts_max_rating')
                    ->max('posts', 'rating')
                    ->label('Posts Max. Rating'),
                Tables\Columns\TextColumn::make('posts_min_rating')
                    ->min('posts', 'rating')
                    ->label('Posts Min. Rating'),
                Tables\Columns\TextColumn::make('posts_sum_rating')
                    ->sum('posts', 'rating')
                    ->label('Posts Rating Sum'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
