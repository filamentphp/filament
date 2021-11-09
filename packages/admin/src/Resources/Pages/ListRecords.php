<?php

namespace Filament\Resources\Pages;

use Filament\Resources\Table;
use Filament\Tables;
use Filament\View\Components\Actions\ButtonAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ListRecords extends Page implements Tables\Contracts\HasTable
{
    use Concerns\UsesResourceTable;
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament::resources.pages.list-records';

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'List';
    }

    public static function getTitle(): string
    {
        return static::$title ?? Str::title(static::getResource()::getPluralLabel());
    }

    protected function canCreate(): bool
    {
        return true;
    }

    protected function getActions(): array
    {
        $label = static::getResource()::getLabel();

        return [
            ButtonAction::make('create')
                ->label("New {$label}")
                ->url(static::getResource()::getCreateUrl())
                ->hidden(! $this->canCreate()),
        ];
    }

    protected function getTableActions(): array
    {
        return $this->getResourceTable()->getActions();
    }

    protected function getTableBulkActions(): array
    {
        return $this->getResourceTable()->getBulkActions();
    }

    protected function getTableColumns(): array
    {
        return $this->getResourceTable()->getColumns();
    }

    protected function getTableFilters(): array
    {
        return $this->getResourceTable()->getFilters();
    }

    protected function getTableQuery(): Builder
    {
        return static::getResource()::getEloquentQuery();
    }
}
