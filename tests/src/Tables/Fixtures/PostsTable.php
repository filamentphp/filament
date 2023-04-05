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
                ->searchable()
                ->action(fn () => $this->emit('title-action-called')),
            Tables\Columns\TextColumn::make('content')
                ->words(10)
                ->searchable(isIndividual: true, isGlobal: false),
            Tables\Columns\TextColumn::make('author.name')
                ->sortable()
                ->searchable()
                ->action(
                    Tables\Actions\Action::make('column-action-object')
                        ->action(fn () => $this->emit('column-action-object-called')),
                ),
            Tables\Columns\TextColumn::make('author.email')
                ->searchable(isIndividual: true, isGlobal: false),
            Tables\Columns\IconColumn::make('is_published')->boolean(),
            Tables\Columns\TextColumn::make('visible'),
            Tables\Columns\TextColumn::make('hidden')
                ->hidden(),
            Tables\Columns\TextColumn::make('with_state')
                ->getStateUsing(fn () => 'correct state'),
            Tables\Columns\TextColumn::make('formatted_state')
                ->formatStateUsing(fn () => 'formatted state'),
            Tables\Columns\TextColumn::make('extra_attributes')
                ->extraAttributes([
                    'class' => 'text-danger-500',
                ]),
            Tables\Columns\TextColumn::make('with_description')
                ->description('description below')
                ->description('description above', 'above'),
            Tables\Columns\SelectColumn::make('with_options')
                ->options([
                    'red' => 'Red',
                    'blue' => 'Blue',
                ]),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\Filter::make('is_published')
                ->query(fn (Builder $query) => $query->where('is_published', true)),
            Tables\Filters\SelectFilter::make('author')
                ->relationship('author', 'name'),
            Tables\Filters\SelectFilter::make('select_filter_attribute')
                ->options([
                    true => 'Published',
                    false => 'Not Published',
                ])
                ->attribute('is_published'),
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
            Tables\Actions\Action::make('halt')
                ->requiresConfirmation()
                ->action(function (Tables\Actions\Action $action) {
                    $this->emit('halt-called');

                    $action->halt();
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
            Tables\Actions\Action::make('exists'),
            Tables\Actions\Action::make('exists-in-order'),
            Tables\Actions\Action::make('url')
                ->url('https://filamentphp.com'),
            Tables\Actions\Action::make('url_in_new_tab')
                ->url('https://filamentphp.com', true),
            Tables\Actions\Action::make('url_not_in_new_tab')
                ->url('https://filamentphp.com'),
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
            Tables\Actions\BulkAction::make('halt')
                ->requiresConfirmation()
                ->action(function (Tables\Actions\BulkAction $action) {
                    $this->emit('halt-called');

                    $action->halt();
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
            Tables\Actions\BulkAction::make('exists'),
            Tables\Actions\BulkAction::make('exists_in_order'),
        ];
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Tables\Actions\Action::make('empty-exists'),
            Tables\Actions\Action::make('empty-exists-in-order'),
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
