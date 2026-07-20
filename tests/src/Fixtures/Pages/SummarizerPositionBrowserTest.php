<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\SummarizerPosition;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;

class SummarizerPositionBrowserTest extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static ?int $navigationSort = 9;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('rating')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make('topSum')
                            ->label('Top total')
                            ->position(SummarizerPosition::Top),
                        Tables\Columns\Summarizers\Sum::make('bottomSum')
                            ->label('Bottom total'),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }
}
