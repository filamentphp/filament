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
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class PostsTableWithAuthorState extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public bool $eagerLoadAuthor = true;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Post::query()->when(
                $this->eagerLoadAuthor,
                fn (Builder $query): Builder => $query->with('author'),
            ))
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('author_name')
                    ->state(fn (Post $record): ?string => $record->author?->name),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
