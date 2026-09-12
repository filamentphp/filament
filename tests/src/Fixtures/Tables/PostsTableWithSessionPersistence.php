<?php

namespace Filament\Tests\Fixtures\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;

class PostsTableWithSessionPersistence
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                TextColumn::make('title')
                    ->toggleable(),
                TextColumn::make('author_id')
                    ->label('Author'),
            ])
            ->reorderableColumns()
            ->paginationPageOptions([10, 25])
            ->defaultPaginationPageOption(50)
            ->persistInSession();
    }
}
