<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PostsTableWithColumnIndividualSearchTermSplittingDisabled extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                TextColumn::make('title')
                    ->searchable(isIndividual: true, isGlobal: false),
                TextColumn::make('content')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->splitIndividualSearchTerms(false),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
