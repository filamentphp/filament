<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PostsTableWithReservedJsPropertyColumnSearch extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                // `length` and `sort` collide with built-in JavaScript array properties.
                Tables\Columns\TextColumn::make('length')
                    ->searchable('title', isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('sort')
                    ->searchable('title', isIndividual: true, isGlobal: false),
                // `title` is a normal column name and must not be seeded.
                Tables\Columns\TextColumn::make('title')
                    ->searchable(isIndividual: true, isGlobal: false),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
