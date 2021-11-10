<?php

namespace Filament\Resources\Pages;

use Filament\Tables;
use Filament\View\Components\Actions\ButtonAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ListRecords extends Page implements Tables\Contracts\HasTable
{
    use Concerns\UsesResourceTable;
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament::resources.pages.list-records';

    public function mount(): void
    {
        static::authorizeResourceAccess();
    }

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'List';
    }

    public static function getTitle(): string
    {
        return static::$title ?? Str::title(static::getResource()::getPluralLabel());
    }

    protected function getActions(): array
    {
        $resource = static::getResource();
        $label = $resource::getLabel();

        return [
            ButtonAction::make('create')
                ->label("New {$label}")
                ->url(fn () => $resource::getUrl('create'))
                ->hidden(! $resource::canCreate()),
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
