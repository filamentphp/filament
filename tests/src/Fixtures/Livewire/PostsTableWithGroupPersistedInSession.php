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
use Livewire\Attributes\Url;
use Livewire\Component;

class PostsTableWithGroupPersistedInSession extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    #[Url(as: 'grouping')]
    public ?string $tableGrouping = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->groups(static fn () => [
                Tables\Grouping\Group::make('title'),
                Tables\Grouping\Group::make('author.name'),
            ])
            ->defaultGroup('title')
            ->persistGroupInSession()
            ->columns([
                Tables\Columns\TextColumn::make('title')->sortable(),
                Tables\Columns\TextColumn::make('author.name')->sortable(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
