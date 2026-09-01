<?php

namespace Filament\Tests\Fixtures\Resources\Users\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostsWithPreloadedAssociateRelationManager extends RelationManager
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
            ->headerActions([
                AssociateAction::make()
                    ->preloadRecordSelect()
                    ->multiple(),
            ]);
    }
}
