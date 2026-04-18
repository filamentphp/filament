<?php

namespace App\Filament\Resources\PostResource\Resources\CommentResource;

use App\Filament\Resources\PostResource;
use App\Models\Comment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $parentResource = PostResource::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Author'),
                TextColumn::make('body')
                    ->limit(50),
                IconColumn::make('is_approved')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
