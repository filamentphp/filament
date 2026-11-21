<?php

namespace Filament\Tests\Panels\Fixtures\Resources\PostResource\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tests\Models\Post;
use Filament\Tests\Panels\Fixtures\Resources\PostResource;

class ListPostsWithDisabledEditAction extends ListPosts
{
    public function table(Table $table): Table
    {
        return PostResource::table($table)
            ->actions([
                EditAction::make()
                    ->disabledForm(fn (Post $record): bool => $record->is_published)
                    ->form([
                        TextInput::make('title')->dehydrated(),
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->dehydrated()
                            ->saveRelationshipsWhenDisabled(),
                    ]),
            ]);
    }
}
