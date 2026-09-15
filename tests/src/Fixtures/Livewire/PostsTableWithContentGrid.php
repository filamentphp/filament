<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostsTableWithContentGrid extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->contentGrid(['md' => 2, 'xl' => 3])
            ->columns([
                Stack::make([
                    TextColumn::make('title')
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('author.name')
                        ->sortable(),
                    TextColumn::make('rating'),
                ]),
            ]);
    }
}
