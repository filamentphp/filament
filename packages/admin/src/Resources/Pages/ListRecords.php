<?php

namespace Filament\Resources\Pages;

use Closure;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ListRecords extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament::resources.pages.list-records';

    protected ?Table $resourceTable = null;

    protected $queryString = [
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
    ];

    public function mount(): void
    {
        static::authorizeResourceAccess();
    }

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament::resources/pages/list-records.breadcrumb');
    }

    protected function getResourceTable(): Table
    {
        if (! $this->resourceTable) {
            $table = Table::make();

            $resource = static::getResource();

            $table->actions(array_merge(
                ($resource::hasPage('view') ? [$this->getViewLinkTableAction()] : []),
                ($resource::hasPage('edit') ? [$this->getEditLinkTableAction()] : []),
            ));

            if ($resource::canDeleteAny()) {
                $table->bulkActions([$this->getDeleteTableBulkAction()]);
            }

            $this->resourceTable = static::getResource()::table($table);
        }

        return $this->resourceTable;
    }

    protected function getViewLinkTableAction(): Tables\Actions\LinkAction
    {
        $resource = static::getResource();

        return Tables\Actions\LinkAction::make('view')
            ->label(__('filament::resources/pages/list-records.table.actions.view.label'))
            ->url(fn (Model $record): string => $resource::getUrl('view', ['record' => $record]))
            ->hidden(fn (Model $record): bool => ! $resource::canView($record));
    }

    protected function getEditLinkTableAction(): Tables\Actions\LinkAction
    {
        $resource = static::getResource();

        return Tables\Actions\LinkAction::make('edit')
            ->label(__('filament::resources/pages/list-records.table.actions.edit.label'))
            ->url(fn (Model $record): string => $resource::getUrl('edit', ['record' => $record]))
            ->hidden(fn (Model $record): bool => ! $resource::canEdit($record));
    }

    protected function getDeleteTableBulkAction(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('delete')
            ->label(__('filament::resources/pages/list-records.table.bulk_actions.delete.label'))
            ->action(fn (Collection $records) => $records->each->delete())
            ->requiresConfirmation()
            ->deselectRecordsAfterCompletion()
            ->color('danger')
            ->icon('heroicon-o-trash');
    }

    protected function getTitle(): string
    {
        return static::$title ?? Str::title(static::getResource()::getPluralLabel());
    }

    protected function getActions(): array
    {
        $resource = static::getResource();

        if (! $resource::canCreate()) {
            return [];
        }

        return [$this->getCreateButtonAction()];
    }

    protected function getCreateButtonAction(): ButtonAction
    {
        $resource = static::getResource();
        $label = $resource::getLabel();

        return ButtonAction::make('create')
            ->label(__('filament::resources/pages/list-records.actions.create.label', ['label' => $label]))
            ->url(fn () => $resource::getUrl('create'));
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return $this->getResourceTable()->getDefaultSortColumn();
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return $this->getResourceTable()->getDefaultSortDirection();
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

    protected function getTableHeaderActions(): array
    {
        return $this->getResourceTable()->getHeaderActions();
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Model $record): ?string {
            $resource = static::getResource();

            if ($resource::canEdit($record)) {
                return $resource::getUrl('edit', ['record' => $record]);
            }

            if ($resource::canView($record)) {
                return $resource::getUrl('view', ['record' => $record]);
            }

            return null;
        };
    }

    protected function getTableQuery(): Builder
    {
        return static::getResource()::getEloquentQuery();
    }
}
