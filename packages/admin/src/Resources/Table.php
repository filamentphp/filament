<?php

namespace Filament\Resources;

use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\LinkAction;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Table
{
    protected array $actions = [];

    protected array $bulkActions = [];

    protected array $columns = [];

    protected array $filters = [];

    public static function make(HasTable $livewire): static
    {
        $static = new static();

        $resource = $livewire::getResource();

        if ($resource::hasPage('view')) {
            $static->actions([
                LinkAction::make('view')
                    ->label('View')
                    ->url(fn (Model $record): string => $resource::getUrl('view', ['record' => $record]))
                    ->hidden(fn (Model $record): bool => ! $resource::canView($record)),
            ]);
        } elseif ($resource::hasPage('edit')) {
            $static->actions([
                LinkAction::make('edit')
                    ->label('Edit')
                    ->url(fn (Model $record): string => $resource::getUrl('edit', ['record' => $record]))
                    ->hidden(fn (Model $record): bool => ! $resource::canEdit($record)),
            ]);
        }

        $static->bulkActions([
            BulkAction::make('delete')
                ->label('Delete selected')
                ->action(fn (Collection $records) => $records->each->delete())
                ->requiresConfirmation()
                ->deselectRecordsAfterCompletion()
                ->color('danger')
                ->icon('heroicon-o-trash'),
        ]);

        return $static;
    }

    public function actions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function bulkActions(array $actions): static
    {
        $this->bulkActions = $actions;

        return $this;
    }

    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function filters(array $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    public function prependActions(array $actions): static
    {
        $this->actions = array_merge($actions, $this->actions);

        return $this;
    }

    public function prependBulkActions(array $actions): static
    {
        $this->bulkActions = array_merge($actions, $this->bulkActions);

        return $this;
    }

    public function pushActions(array $actions): static
    {
        $this->actions = array_merge($this->actions, $actions);

        return $this;
    }

    public function pushBulkActions(array $actions): static
    {
        $this->bulkActions = array_merge($this->bulkActions, $actions);

        return $this;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getBulkActions(): array
    {
        return $this->bulkActions;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
