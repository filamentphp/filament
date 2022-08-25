<?php

namespace Filament\Tests\Tables\Fixtures;

use Filament\Forms\ComponentContainer;
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
            Tables\Columns\TextColumn::make('visible'),
            Tables\Columns\TextColumn::make('hidden')
                ->hidden(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\Filter::make('is_published')
                ->query(fn (Builder $query) => $query->where('is_published', true)),
            Tables\Filters\SelectFilter::make('author')
                ->relationship('author', 'name'),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Tables\Actions\Action::make('data')
                ->mountUsing(fn (ComponentContainer $form) => $form->fill(['foo' => 'bar']))
                ->form([
                    TextInput::make('payload')->required(),
                ])
                ->action(function (array $data) {
                    $this->emit('data-called', $data);
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
            Tables\Actions\Action::make('visible'),
            Tables\Actions\Action::make('hidden')
                ->hidden(),
            Tables\Actions\Action::make('enabled'),
            Tables\Actions\Action::make('disabled')
                ->disabled(),
            Tables\Actions\Action::make('has-icon')
                ->icon('heroicon-s-pencil'),
            Tables\Actions\Action::make('has-label')
                ->label('My Action'),
            Tables\Actions\Action::make('has-color')
                ->color('primary'),
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
            Tables\Actions\BulkAction::make('data')
                ->mountUsing(fn (ComponentContainer $form) => $form->fill(['foo' => 'bar']))
                ->form([
                    TextInput::make('payload')->required(),
                ])
                ->action(function (array $data) {
                    $this->emit('data-called', $data);
                }),
            Tables\Actions\BulkAction::make('arguments')
                ->requiresConfirmation()
                ->action(function (array $arguments) {
                    $this->emit('arguments-called', $arguments);
                }),
            Tables\Actions\BulkAction::make('hold')
                ->requiresConfirmation()
                ->action(function (Tables\Actions\BulkAction $action) {
                    $this->emit('hold-called');

                    $action->hold();
                }),
            Tables\Actions\BulkAction::make('visible'),
            Tables\Actions\BulkAction::make('hidden')
                ->hidden(),
            Tables\Actions\BulkAction::make('enabled'),
            Tables\Actions\BulkAction::make('disabled')
                ->disabled(),
            Tables\Actions\BulkAction::make('has-icon')
                ->icon('heroicon-s-pencil'),
            Tables\Actions\BulkAction::make('has-label')
                ->label('My Action'),
            Tables\Actions\BulkAction::make('has-color')
                ->color('primary'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Post::query();
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
