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
use Illuminate\Database\Eloquent\Builder;

class IndividualColumnSearchBrowserTest extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static ?int $navigationSort = 9;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                // `length` and `sort` collide with built-in JavaScript array properties. They are
                // not real database columns, so they use a no-op search query to avoid SQL errors.
                Tables\Columns\TextColumn::make('length')
                    ->searchable(query: fn (Builder $query): Builder => $query, isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('sort')
                    ->searchable(query: fn (Builder $query): Builder => $query, isIndividual: true, isGlobal: false),
                // `title` is a normal column name that has never collided.
                Tables\Columns\TextColumn::make('title')
                    ->searchable(isIndividual: true, isGlobal: false),
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
