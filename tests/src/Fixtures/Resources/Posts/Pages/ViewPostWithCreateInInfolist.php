<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewPostWithCreateInInfolist extends ViewPost
{
    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Post')
                ->key('postSection')
                ->headerActions([
                    CreateAction::make('createFromInfolist')
                        ->modal()
                        ->schema([
                            TextInput::make('title')->required(),
                            TextInput::make('rating')->numeric()->required(),
                            Group::make([
                                TextInput::make('seo_title'),
                            ])->relationship('metadata'),
                        ]),
                ])
                ->schema([
                    TextEntry::make('title'),
                ]),
        ]);
    }
}
