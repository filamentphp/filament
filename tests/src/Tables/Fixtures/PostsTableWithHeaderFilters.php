<?php

namespace Filament\Tests\Tables\Fixtures;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class PostsTableWithHeaderFilters extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->headerFilter(
                        Tables\Filters\SelectFilter::make('author')
                            ->relationship('author', 'name'),
                    ),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->headerFilter(
                        Tables\Filters\Filter::make('is_published')
                            ->query(static fn (Builder $query) => $query->where('is_published', true)),
                    ),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
