<?php

namespace Filament\Resources\Pages;

use Filament\Resources\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class ListRecords extends Page implements HasTable
{
    use InteractsWithTable;

    protected ?Table $resourceTable = null;

    protected static string $view = 'filament::resources.pages.list-records';

    public static function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'List';
    }

    protected static function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        return [
            $resource::getUrl() => $resource::getBreadcrumb(),
            static::getBreadcrumb(),
        ];
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
        return static::getResource()::getModel()::query();
    }
}
