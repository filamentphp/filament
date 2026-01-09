<?php

namespace Filament\Tests\Fixtures\Resources\Users\RelationManagers;

use Filament\Actions\DissociateBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostsWithDissociateBulkActionRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->inverseRelationship('author')
            ->columns([
                TextColumn::make('title'),
            ])
            ->toolbarActions([
                DissociateBulkAction::make(),
            ]);
    }
}
