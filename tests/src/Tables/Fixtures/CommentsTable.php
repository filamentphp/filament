<?php

namespace Filament\Tests\Tables\Fixtures;

use Filament\Tables;
use Filament\Tests\Models\Comment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class CommentsTable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('author.name')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('post.author.name')
                ->sortable()
                ->searchable(),
            Tables\Columns\IconColumn::make('is_published')->boolean(),

        ];
    }

    protected function getTableFilters(): array
    {
        return [
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Comment::query();
    }

    protected function shouldPersistTableFiltersInSession(): bool
    {
        return true;
    }

    public function render(): View
    {
        return view('tables.fixtures.table');
    }
}
