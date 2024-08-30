<?php

namespace Filament\Resources\Pages;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Concerns\HasTabs;
use Filament\Schema\Schema;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Attributes\Url;

class ListRecords extends Page implements Tables\Contracts\HasTable
{
    use HasTabs;
    use Tables\Concerns\InteractsWithTable {
        makeTable as makeBaseTable;
    }

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::resources.pages.list-records';

    #[Url]
    public bool $isTableReordering = false;

    /**
     * @var array<string, mixed> | null
     */
    #[Url]
    public ?array $tableFilters = null;

    #[Url]
    public ?string $tableGrouping = null;

    #[Url]
    public ?string $tableGroupingDirection = null;

    /**
     * @var ?string
     */
    #[Url]
    public $tableSearch = '';

    #[Url]
    public ?string $tableSortColumn = null;

    #[Url]
    public ?string $tableSortDirection = null;

    #[Url]
    public ?string $activeTab = null;

    public function mount(): void
    {
        $this->authorizeAccess();

        $this->loadDefaultActiveTab();
    }

    protected function authorizeAccess(): void {}

    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb ?? __('filament-panels::resources/pages/list-records.breadcrumb');
    }

    public function table(Table $table): Table
    {
        return $table;
    }

    public function getTitle(): string | Htmlable
    {
        return static::$title ?? static::getResource()::getTitleCasePluralModelLabel();
    }

    protected function configureAction(Action $action): void
    {
        match (true) {
            $action instanceof CreateAction => $this->configureCreateAction($action),
            default => null,
        };
    }

    public function form(Schema $form): Schema
    {
        return static::getResource()::form($form);
    }

    public function infolist(Schema $infolist): Schema
    {
        return static::getResource()::infolist($infolist);
    }

    protected function configureCreateAction(CreateAction $action): void
    {
        $resource = static::getResource();

        $action
            ->authorize($resource::canCreate())
            ->model($this->getModel())
            ->modelLabel($this->getModelLabel() ?? static::getResource()::getModelLabel())
            ->schema(fn (Schema $schema): Schema => $this->form($schema->columns(2)));

        if ($parentRecord = $this->getParentRecord()) {
            $action->relationship(fn (): Relation => $resource::getParentResourceRegistration()->getRelationship($parentRecord));
        } elseif (static::getResource()::isScopedToTenant()) {
            $action->relationship(($tenant = Filament::getTenant()) ? fn (): Relation => static::getResource()::getTenantRelationship($tenant) : null);
        }

        if ($resource::hasPage('create')) {
            $action->url(fn (): string => $this->getResourceUrl('create'));
        }
    }

    protected function configureTableAction(Action $action): void
    {
        match (true) {
            $action instanceof CreateAction => $this->configureCreateAction($action),
            $action instanceof DeleteAction => $this->configureDeleteAction($action),
            $action instanceof EditAction => $this->configureEditAction($action),
            $action instanceof ForceDeleteAction => $this->configureForceDeleteAction($action),
            $action instanceof ReplicateAction => $this->configureReplicateAction($action),
            $action instanceof RestoreAction => $this->configureRestoreAction($action),
            $action instanceof ViewAction => $this->configureViewAction($action),
            default => null,
        };
    }

    protected function configureDeleteAction(DeleteAction $action): void
    {
        $action
            ->authorize(fn (Model $record): bool => static::getResource()::canDelete($record))
            ->icon(FilamentIcon::resolve('actions::delete-action') ?? 'heroicon-m-trash');
    }

    protected function configureEditAction(EditAction $action): void
    {
        $resource = static::getResource();

        $action
            ->authorize(fn (Model $record): bool => $resource::canEdit($record))
            ->schema(fn (Schema $schema): Schema => $this->form($schema->columns(2)))
            ->icon(FilamentIcon::resolve('actions::edit-action') ?? 'heroicon-m-pencil-square');

        if ($resource::hasPage('edit')) {
            $action->url(fn (Model $record): string => $this->getResourceUrl('edit', ['record' => $record]));
        }
    }

    protected function configureForceDeleteAction(ForceDeleteAction $action): void
    {
        $action
            ->authorize(fn (Model $record): bool => static::getResource()::canForceDelete($record))
            ->icon(FilamentIcon::resolve('actions::force-delete-action') ?? 'heroicon-m-trash');
    }

    protected function configureReplicateAction(ReplicateAction $action): void
    {
        $action
            ->authorize(fn (Model $record): bool => static::getResource()::canReplicate($record))
            ->icon(FilamentIcon::resolve('actions::replicate-action') ?? 'heroicon-m-square-2-stack');
    }

    protected function configureRestoreAction(RestoreAction $action): void
    {
        $action
            ->authorize(fn (Model $record): bool => static::getResource()::canRestore($record))
            ->icon(FilamentIcon::resolve('actions::restore-action') ?? 'heroicon-m-arrow-uturn-left');
    }

    protected function configureViewAction(ViewAction $action): void
    {
        $resource = static::getResource();

        $action
            ->authorize(fn (Model $record): bool => $resource::canView($record))
            ->icon(FilamentIcon::resolve('actions::view-action') ?? 'heroicon-m-eye')
            ->schema(fn (Schema $schema): Schema => $this->infolist($this->form($schema->columns(2))));

        if ($resource::hasPage('view')) {
            $action->url(fn (Model $record): string => $this->getResourceUrl('view', ['record' => $record]));
        }
    }

    protected function configureTableBulkAction(BulkAction $action): void
    {
        match (true) {
            $action instanceof DeleteBulkAction => $this->configureDeleteBulkAction($action),
            $action instanceof ForceDeleteBulkAction => $this->configureForceDeleteBulkAction($action),
            $action instanceof RestoreBulkAction => $this->configureRestoreBulkAction($action),
            default => null,
        };
    }

    protected function configureDeleteBulkAction(DeleteBulkAction $action): void
    {
        $action
            ->authorize(static::getResource()::canDeleteAny());
    }

    protected function configureForceDeleteBulkAction(ForceDeleteBulkAction $action): void
    {
        $action
            ->authorize(static::getResource()::canForceDeleteAny());
    }

    protected function configureRestoreBulkAction(RestoreBulkAction $action): void
    {
        $action
            ->authorize(static::getResource()::canRestoreAny());
    }

    /**
     * @return Model|class-string<Model>|null
     */
    protected function getMountedActionSchemaModel(): Model | string | null
    {
        return $this->getModel();
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    public function getModelLabel(): ?string
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    public function getPluralModelLabel(): ?string
    {
        return null;
    }

    protected function makeTable(): Table
    {
        $table = $this->makeBaseTable()
            ->query(fn (): Builder => $this->getTableQuery())
            ->when(
                $this->getParentRecord(),
                fn (Table $table, Model $parentRecord): Table => $table->modifyQueryUsing(
                    fn (Builder $query) => static::getResource()::scopeEloquentQueryToParent($query, $parentRecord),
                ),
            )
            ->modifyQueryUsing($this->modifyQueryWithActiveTab(...))
            ->when($this->getModelLabel(), fn (Table $table, string $modelLabel): Table => $table->modelLabel($modelLabel))
            ->when($this->getPluralModelLabel(), fn (Table $table, string $pluralModelLabel): Table => $table->pluralModelLabel($pluralModelLabel))
            ->recordAction(function (Model $record, Table $table): ?string {
                foreach (['view', 'edit'] as $action) {
                    $action = $table->getAction($action);

                    if (! $action) {
                        continue;
                    }

                    $action->record($record);
                    $action->getGroup()?->record($record);

                    if ($action->isHidden()) {
                        continue;
                    }

                    if ($action->getUrl()) {
                        continue;
                    }

                    return $action->getName();
                }

                return null;
            })
            ->recordUrl($this->getTableRecordUrlUsing() ?? function (Model $record, Table $table): ?string {
                foreach (['view', 'edit'] as $action) {
                    $action = $table->getAction($action);

                    if (! $action) {
                        continue;
                    }

                    $action = clone $action;

                    $action->record($record);
                    $action->getGroup()?->record($record);

                    if ($action->isHidden()) {
                        continue;
                    }

                    $url = $action->getUrl();

                    if (! $url) {
                        continue;
                    }

                    return $url;
                }

                $resource = static::getResource();

                foreach (['view', 'edit'] as $action) {
                    if (! $resource::hasPage($action)) {
                        continue;
                    }

                    if (! $resource::{'can' . ucfirst($action)}($record)) {
                        continue;
                    }

                    return $this->getResourceUrl($action, ['record' => $record]);
                }

                return null;
            });

        static::getResource()::configureTable($table);

        return $table;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableQuery(): ?Builder
    {
        return static::getResource()::getEloquentQuery();
    }

    /**
     * @return array<int | string, string | Schema>
     */
    protected function getForms(): array
    {
        return [];
    }

    /**
     * @return array<NavigationItem | NavigationGroup>
     */
    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
