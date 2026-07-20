<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;

class GroupHeaderSummaryBrowserTest extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static ?int $navigationSort = 9;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('rating')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make('sum')
                            ->label('Rating sum')
                            ->inGroupHeader(),
                    ]),
            ])
            ->defaultGroup(
                Tables\Grouping\Group::make('title')
                    ->collapsible(),
            );
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }
}
