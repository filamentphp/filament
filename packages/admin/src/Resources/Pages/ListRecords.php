<?php

namespace Filament\Resources\Pages;

use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ListRecords extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected ?Table $resourceTable = null;

    protected static string $view = 'filament::resources.pages.list-records';

    public static function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'List';
    }

    public static function getTitle(): string
    {
        return static::$title ?? Str::title(static::getResource()::getPluralLabel());
    }

    protected function getResourceTable(): Table
    {
        if (! $this->resourceTable) {
            $this->resourceTable = static::getResource()::table(Table::make());
        }

        return $this->resourceTable;
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
