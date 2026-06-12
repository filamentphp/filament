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

class PostsTableWithoutSummarizers extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->groups(fn () => [
                Tables\Grouping\Group::make('author.company.name'),
                Tables\Grouping\Group::make('team.company.name'),
                Tables\Grouping\Group::make('author.setting.language.name'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('title')->sortable(),
                Tables\Columns\TextColumn::make('author.name')->sortable(),
                Tables\Columns\TextColumn::make('author.company.name')->sortable(),
                Tables\Columns\TextColumn::make('team.company.name')->sortable(),
                Tables\Columns\TextColumn::make('author.setting.language.name')->sortable(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
