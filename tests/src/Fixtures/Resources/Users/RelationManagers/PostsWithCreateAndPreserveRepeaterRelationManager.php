<?php

namespace Filament\Tests\Fixtures\Resources\Users\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostsWithCreateAndPreserveRepeaterRelationManager extends RelationManager
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
                CreateAction::make()
                    ->preserveFormDataWhenCreatingAnother(['json_array_of_objects']),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required(),
                TextInput::make('rating')->numeric()->required(),
                Repeater::make('json_array_of_objects')
                    ->schema([
                        TextInput::make('name'),
                        TextInput::make('email'),
                    ]),
            ]);
    }
}
