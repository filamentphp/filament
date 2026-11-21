<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;

class ListPostsWithDisabledEditAction extends ListPosts
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->saved(),
            Select::make('author_id')
                ->relationship('author', 'name')
                ->saved()
                ->saveRelationshipsWhenDisabled(),
        ]);
    }

    public function table(Table $table): Table
    {
        return PostResource::table($table)
            ->recordActions([
                EditAction::make()
                    ->disabledSchema(fn (Post $record): bool => $record->is_published),
            ]);
    }
}
