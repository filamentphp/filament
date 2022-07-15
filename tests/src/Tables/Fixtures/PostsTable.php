<?php

namespace Filament\Tests\Tables\Fixtures;

use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tests\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class PostsTable extends Component implements Tables\Contracts\HasTable
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
            Tables\Columns\BooleanColumn::make('is_published'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\Filter::make('is_published')
                ->query(fn (Builder $query) => $query->where('is_published', true)),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Tables\Actions\Action::make('simple')
                ->action(function () {
                    $this->emit('simple-called');
                }),
            Tables\Actions\Action::make('form')
                ->form([
                    TextInput::make('payload')->required(),
                ])
                ->action(function (array $data) {
                    $this->emit('form-called', $data);
                }),
            Tables\Actions\Action::make('arguments')
                ->requiresConfirmation()
                ->action(function (array $arguments) {
                    $this->emit('arguments-called', $arguments);
                }),
            Tables\Actions\Action::make('hold')
                ->requiresConfirmation()
                ->action(function (Tables\Actions\Action $action) {
                    $this->emit('hold-called');

                    $action->hold();
                }),
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
            Tables\Actions\DeleteBulkAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Post::query();
    }

    public function render(): View
    {
        return view('tables.fixtures.table');
    }
}
