<?php

namespace Filament\Resources\Pages;

use Closure;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\Concerns\UsesResourceForm;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ListRecords extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use UsesResourceForm;

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
                ($this->hasViewAction() ? [$this->getViewAction()] : []),
                ($this->hasEditAction() ? [$this->getEditAction()] : []),
                ($this->hasDeleteAction() ? [$this->getDeleteAction()] : []),
            ));

            if ($resource::canDeleteAny()) {
                $table->bulkActions([$this->getDeleteBulkAction()]);
            }

            $this->resourceTable = $this->table($table);
        }

        return $this->resourceTable;
    }

    protected function hasDeleteAction(): bool
    {
        return $this->getDeleteAction() !== null;
    }

    protected function hasEditAction(): bool
    {
        return static::getResource()::hasPage('edit');
    }

    protected function hasViewAction(): bool
    {
        return static::getResource()::hasPage('view');
    }

    protected function table(Table $table): Table
    {
        return static::getResource()::table($table);
    }

    protected function getViewAction(): Tables\Actions\Action
    {
        $resource = static::getResource();

        return Tables\Actions\ViewAction::make()
            ->url(fn (Model $record): string => $resource::getUrl('view', ['record' => $record]))
            ->authorize(fn (Model $record): bool => $resource::canView($record));
    }

    protected function getEditAction(): Tables\Actions\Action
    {
        $resource = static::getResource();

        return Tables\Actions\EditAction::make()
            ->url(fn (Model $record): string => $resource::getUrl('edit', ['record' => $record]))
            ->authorize(fn (Model $record): bool => $resource::canEdit($record));
    }

    protected function getDeleteAction(): ?Tables\Actions\Action
    {
        return null;
    }

    protected function getDeleteBulkAction(): Tables\Actions\BulkAction
    {
        return Tables\Actions\DeleteBulkAction::make()
            ->action(fn () => $this->bulkDelete());
    }

    /**
     * @deprecated Use `->action()` on the action instead.
     */
    public function bulkDelete(): void
    {
        $this->callHook('beforeBulkDelete');

        $this->handleRecordBulkDeletion($this->getSelectedTableRecords());

        $this->callHook('afterBulkDelete');

        if (filled($this->getBulkDeletedNotificationMessage())) {
            $this->notify('success', $this->getBulkDeletedNotificationMessage());
        }
    }

    protected function getBulkDeletedNotificationMessage(): ?string
    {
        return __('filament::resources/pages/list-records.table.bulk_actions.delete.messages.deleted');
    }

    /**
     * @deprecated Use `->using()` on the action instead.
     */
    protected function handleRecordBulkDeletion(Collection $records): void
    {
        $records->each(fn (Model $record) => $record->delete());
    }

    protected function getTitle(): string
    {
        return static::$title ?? Str::title(static::getResource()::getPluralModelLabel());
    }

    protected function getActions(): array
    {
        if (! $this->hasCreateAction()) {
            return [];
        }

        return [$this->getCreateAction()];
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function hasCreateAction(): bool
    {
        $resource = static::getResource();

        if (! $resource::hasPage('create')) {
            return false;
        }

        if (! $resource::canCreate()) {
            return false;
        }

        return true;
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getCreateAction(): Action
    {
        return CreateAction::make();
    }

    protected function getCreateFormSchema(): array
    {
        return $this->getResourceForm(columns: 2)->getSchema();
    }

    protected function configureAction(Action $action): void
    {
        if ($action instanceof CreateAction) {
            $this->configureCreateAction($action);

            return;
        }
    }

    protected function configureCreateAction(CreateAction $action): void
    {
        $resource = static::getResource();

        $action
            ->authorize($resource::canCreate())
            ->model($this->getModel())
            ->modelLabel($this->getModelLabel());

        if ($resource::hasPage('create')) {
            $action->url(fn () => $resource::getUrl('create'));
        } else {
            $action->form($this->getCreateFormSchema());
        }
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

    protected function getTableFiltersLayout(): ?string
    {
        return $this->getResourceTable()->getFiltersLayout();
    }

    protected function getTableHeaderActions(): array
    {
        return $this->getResourceTable()->getHeaderActions();
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Model $record): ?string {
            $resource = static::getResource();

            if ($resource::hasPage('view') && $resource::canView($record)) {
                return $resource::getUrl('view', ['record' => $record]);
            }

            if ($resource::hasPage('edit') && $resource::canEdit($record)) {
                return $resource::getUrl('edit', ['record' => $record]);
            }

            return null;
        };
    }

    protected function getTableQuery(): Builder
    {
        return static::getResource()::getEloquentQuery();
    }

    protected function getMountedActionFormModel(): string
    {
        return $this->getModel();
    }

    public function getTableRecordTitle(Model $record): string
    {
        $resource = static::getResource();

        return $resource::getRecordTitle($record);
    }

    public function getModelLabel(): string
    {
        return static::getResource()::getModelLabel();
    }

    public function getPluralModelLabel(): string
    {
        return static::getResource()::getPluralModelLabel();
    }

    public function getTableModelLabel(): string
    {
        return $this->getModelLabel();
    }

    public function getTablePluralModelLabel(): string
    {
        return $this->getPluralModelLabel();
    }
}
