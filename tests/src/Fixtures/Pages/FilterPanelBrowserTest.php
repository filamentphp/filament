<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\FilterPanel;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class FilterPanelBrowserTest extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static ?int $navigationSort = 10;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                TextColumn::make('title'),
            ])
            ->filters([
                FilterPanel::make(FiltersLayout::AboveContent, [
                    Filter::make('is_published')
                        ->query(fn (EloquentBuilder $query) => $query->where('is_published', true)),
                ])->columns(2),
                FilterPanel::make(FiltersLayout::Dropdown, [
                    SelectFilter::make('author')->relationship('author', 'name'),
                ]),
                FilterPanel::make(FiltersLayout::Modal, [
                    Filter::make('recent')
                        ->query(fn (EloquentBuilder $query) => $query->where('created_at', '>=', now()->subYear())),
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
