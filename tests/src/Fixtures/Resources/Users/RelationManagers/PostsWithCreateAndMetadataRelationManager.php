<?php

namespace Filament\Tests\Fixtures\Resources\Users\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostsWithCreateAndMetadataRelationManager extends RelationManager
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
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required(),
                TextInput::make('rating')->numeric()->required(),
                Group::make([
                    TextInput::make('seo_title'),
                ])->relationship('metadata'),
            ]);
    }
}
