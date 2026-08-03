<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class PostsTableWithNonSplitMultiColumnIndividualSearch extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->splitSearchTerms(false)
            ->columns([
                TextColumn::make('title')
                    ->searchable(['title', 'content'], isIndividual: true, isGlobal: false),
            ])
            ->filters([
                Filter::make('is_published')
                    ->query(fn (Builder $query): Builder => $query->where('is_published', true)),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
