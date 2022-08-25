<?php

namespace Filament\Resources\RelationManagers;

use Closure;
use Filament\Facades\Filament;
use Filament\Http\Livewire\Concerns\CanNotify;
use function Filament\locale_has_pluralization;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;

class RelationManager extends Component implements Tables\Contracts\HasRelationshipTable, Tables\Contracts\HasTable
{
    use CanNotify;
    use Tables\Concerns\InteractsWithTable;

    public Model $ownerRecord;

    public ?string $pageClass = null;

    protected static ?string $recordTitleAttribute = null;

    protected static string $relationship;

    protected static ?string $inverseRelationship = null;

    protected static string $view = 'filament::resources.relation-manager';

    /**
     * @deprecated Use `$modelLabel` instead.
     */
    protected static ?string $label = null;

    /**
     * @deprecated Use `$pluralModelLabel` instead.
     */
    protected static ?string $pluralLabel = null;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $title = null;

    protected function getTableQueryStringIdentifier(): ?string
    {
        return lcfirst(class_basename(static::class));
    }

    protected function getResourceForm(?int $columns = null, bool $isDisabled = false): Form
    {
        return static::form(
            $this->getBaseResourceForm(
                columns: $columns,
                isDisabled: $isDisabled,
            ),
        );
    }

    protected function getBaseResourceForm(?int $columns = null, bool $isDisabled = false): Form
    {
        return Form::make()
            ->columns($columns)
            ->disabled($isDisabled);
    }

    protected function configureTableAction(Tables\Actions\Action $action): void
    {
        match (true) {
            $action instanceof Tables\Actions\AssociateAction => $this->configureAssociateAction($action),
            $action instanceof Tables\Actions\AttachAction => $this->configureAttachAction($action),
            $action instanceof Tables\Actions\CreateAction => $this->configureCreateAction($action),
            $action instanceof Tables\Actions\DeleteAction => $this->configureDeleteAction($action),
            $action instanceof Tables\Actions\DetachAction => $this->configureDetachAction($action),
            $action instanceof Tables\Actions\DissociateAction => $this->configureDissociateAction($action),
            $action instanceof Tables\Actions\EditAction => $this->configureEditAction($action),
            $action instanceof Tables\Actions\ForceDeleteAction => $this->configureForceDeleteAction($action),
            $action instanceof Tables\Actions\ReplicateAction => $this->configureReplicateAction($action),
            $action instanceof Tables\Actions\RestoreAction => $this->configureRestoreAction($action),
            $action instanceof Tables\Actions\ViewAction => $this->configureViewAction($action),
            default => null,
        };
    }

    protected function configureAssociateAction(Tables\Actions\AssociateAction $action): void
    {
        $action
            ->authorize($this->canAssociate())
            ->recordTitleAttribute(static::getRecordTitleAttribute());
    }

    protected function configureAttachAction(Tables\Actions\AttachAction $action): void
    {
        $action
            ->authorize($this->canAttach())
            ->recordTitleAttribute(static::getRecordTitleAttribute());
    }

    protected function getCreateFormSchema(): array
    {
        return $this->getResourceForm(columns: 2)->getSchema();
    }

    protected function configureCreateAction(Tables\Actions\CreateAction $action): void
    {
        $action
            ->authorize($this->canCreate())
            ->form($this->getCreateFormSchema());
    }

    protected function configureDeleteAction(Tables\Actions\DeleteAction $action): void
    {
        $action
            ->authorize(fn (Model $record): bool => $this->canDelete($record));
    }

    protected function configureDetachAction(Tables\Actions\DetachAction $action): void
    {
        $action
            ->authorize(fn (Model $record): bool => $this->canDetach($record));
    }

    protected function configureDissociateAction(Tables\Actions\DissociateAction $action): void
    {
        $action
            ->authorize(fn (Model $record): bool => $this->canDissociate($record));
    }

    protected function getEditFormSchema(): array
    {
        return $this->getResourceForm(columns: 2)->getSchema();
    }

    protected function configureEditAction(Tables\Actions\EditAction $action): void
    {
        $action
            ->authorize(fn (Model $record): bool => $this->canEdit($record))
            ->form($this->getEditFormSchema());
    }

    protected function configureForceDeleteAction(Tables\Actions\ForceDeleteAction $action): void
    {
        $action
            ->authorize(fn (Model $record): bool => $this->canForceDelete($record));
    }

    protected function configureReplicateAction(Tables\Actions\ReplicateAction $action): void
    {
        $action
            ->authorize(fn (Model $record): bool => $this->canReplicate($record));
    }

    protected function configureRestoreAction(Tables\Actions\RestoreAction $action): void
    {
        $action
            ->authorize(fn (Model $record): bool => $this->canRestore($record));
    }

    protected function getViewFormSchema(): array
    {
        return $this->getResourceForm(columns: 2, isDisabled: true)->getSchema();
    }

    protected function configureViewAction(Tables\Actions\ViewAction $action): void
    {
        $action
            ->authorize(fn (Model $record): bool => $this->canView($record))
            ->form($this->getViewFormSchema());
    }

    protected function configureTableBulkAction(BulkAction $action): void
    {
        match (true) {
            $action instanceof Tables\Actions\DeleteBulkAction => $this->configureDeleteBulkAction($action),
            $action instanceof Tables\Actions\DetachBulkAction => $this->configureDetachBulkAction($action),
            $action instanceof Tables\Actions\DissociateBulkAction => $this->configureDissociateBulkAction($action),
            $action instanceof Tables\Actions\ForceDeleteBulkAction => $this->configureForceDeleteBulkAction($action),
            $action instanceof Tables\Actions\RestoreBulkAction => $this->configureRestoreBulkAction($action),
            default => null,
        };
    }

    protected function configureDeleteBulkAction(Tables\Actions\DeleteBulkAction $action): void
    {
        $action
            ->authorize($this->canDeleteAny());
    }

    protected function configureDetachBulkAction(Tables\Actions\DetachBulkAction $action): void
    {
        $action
            ->authorize($this->canDetachAny());
    }

    protected function configureDissociateBulkAction(Tables\Actions\DissociateBulkAction $action): void
    {
        $action
            ->authorize($this->canDissociateAny());
    }

    protected function configureForceDeleteBulkAction(Tables\Actions\ForceDeleteBulkAction $action): void
    {
        $action
            ->authorize($this->canForceDeleteAny());
    }

    protected function configureRestoreBulkAction(Tables\Actions\RestoreBulkAction $action): void
    {
        $action
            ->authorize($this->canRestoreAny());
    }

    protected function callHook(string $hook): void
    {
        if (! method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}();
    }

    protected function can(string $action, ?Model $record = null): bool
    {
        $policy = Gate::getPolicyFor($model = $this->getRelatedModel());

        if ($policy === null || (! method_exists($policy, $action))) {
            return true;
        }

        return Gate::forUser(Filament::auth()->user())->check($action, $record ?? $model);
    }

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        $model = $ownerRecord->{static::getRelationshipName()}()->getQuery()->getModel()::class;

        $policy = Gate::getPolicyFor($model);
        $action = 'viewAny';

        if ($policy === null || (! method_exists($policy, $action))) {
            return true;
        }

        return Gate::forUser(Filament::auth()->user())->check($action, $model);
    }

    public static function form(Form $form): Form
    {
        return $form;
    }

    public function getInverseRelationshipName(): string
    {
        return static::$inverseRelationship ?? (string) Str::of(class_basename($this->getOwnerRecord()))
            ->plural()
            ->camel();
    }

    public function getOwnerRecord(): Model
    {
        return $this->ownerRecord;
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function getRelationshipName(): string
    {
        return static::$relationship;
    }

    public static function getTitle(): string
    {
        return static::$title ?? Str::headline(static::getPluralModelLabel());
    }

    public static function getTitleForRecord(Model $ownerRecord): string
    {
        return static::getTitle();
    }

    public static function getRecordTitleAttribute(): ?string
    {
        return static::$recordTitleAttribute;
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record?->getAttributeValue(static::getRecordTitleAttribute()) ?? static::getModelLabel();
    }

    /**
     * @deprecated Use `getModelLabel()` instead.
     */
    protected static function getRecordLabel(): ?string
    {
        return static::$label;
    }

    protected static function getModelLabel(): string
    {
        return static::$modelLabel ?? static::getRecordLabel() ?? (string) Str::of(static::getRelationshipName())
            ->kebab()
            ->replace('-', ' ')
            ->singular();
    }

    /**
     * @deprecated Use `getPluralModelLabel()` instead.
     */
    protected static function getPluralRecordLabel(): ?string
    {
        return static::$pluralLabel;
    }

    protected static function getPluralModelLabel(): string
    {
        if (filled($label = static::$pluralModelLabel ?? static::getPluralRecordLabel())) {
            return $label;
        }

        if (locale_has_pluralization()) {
            return (string) Str::of(static::getRelationshipName())
                ->kebab()
                ->replace('-', ' ');
        }

        return static::getModelLabel();
    }

    protected function getRelatedModel(): string
    {
        return $this->getRelationship()->getQuery()->getModel()::class;
    }

    public function getRelationship(): Relation | Builder
    {
        return $this->getOwnerRecord()->{static::getRelationshipName()}();
    }

    protected function getInverseRelationshipFor(Model $record): Relation | Builder
    {
        return $record->{$this->getInverseRelationshipName()}();
    }

    protected function getResourceTable(): Table
    {
        return $this->table(Table::make());
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return $this->getResourceTable()->getDefaultSortColumn();
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return $this->getResourceTable()->getDefaultSortDirection();
    }

    public function getTableRecordTitle(Model $record): string
    {
        return static::getRecordTitle($record);
    }

    public function getTableModelLabel(): string
    {
        return static::getModelLabel();
    }

    public function getTablePluralModelLabel(): string
    {
        return static::getPluralModelLabel();
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

    protected function getTableReorderColumn(): ?string
    {
        return $this->getResourceTable()->getReorderColumn();
    }

    protected function isTableReorderable(): bool
    {
        return filled($this->getTableReorderColumn()) && $this->canReorder();
    }

    protected function getTablePollingInterval(): ?string
    {
        return $this->getResourceTable()->getPollingInterval();
    }

    protected function getTableHeading(): ?string
    {
        return static::getTitle();
    }

    public function render(): View
    {
        return view(static::$view, $this->getViewData());
    }

    protected function canAssociate(): bool
    {
        return $this->can('associate');
    }

    protected function canAttach(): bool
    {
        return $this->can('attach');
    }

    protected function canCreate(): bool
    {
        return $this->can('create');
    }

    protected function canDelete(Model $record): bool
    {
        return $this->can('delete', $record);
    }

    protected function canDeleteAny(): bool
    {
        return $this->can('deleteAny');
    }

    protected function canDetach(Model $record): bool
    {
        return $this->can('detach', $record);
    }

    protected function canDetachAny(): bool
    {
        return $this->can('detachAny');
    }

    protected function canDissociate(Model $record): bool
    {
        return $this->can('dissociate', $record);
    }

    protected function canDissociateAny(): bool
    {
        return $this->can('dissociateAny');
    }

    protected function canEdit(Model $record): bool
    {
        return $this->can('update', $record);
    }

    protected function canForceDelete(Model $record): bool
    {
        return $this->can('forceDelete', $record);
    }

    protected function canForceDeleteAny(): bool
    {
        return $this->can('forceDeleteAny');
    }

    protected function canReorder(): bool
    {
        return $this->can('reorder');
    }

    protected function canReplicate(Model $record): bool
    {
        return $this->can('replicate', $record);
    }

    protected function canRestore(Model $record): bool
    {
        return $this->can('restore', $record);
    }

    protected function canRestoreAny(): bool
    {
        return $this->can('restoreAny');
    }

    protected function canView(Model $record): bool
    {
        return $this->can('view', $record);
    }

    protected function getViewData(): array
    {
        return [];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Model $record): ?string {
            foreach (['view', 'edit'] as $action) {
                $action = $this->getCachedTableAction($action);

                if (! $action) {
                    continue;
                }

                $action->record($record);

                if ($action->isHidden()) {
                    continue;
                }

                $url = $action->getUrl();

                if (! $url) {
                    continue;
                }

                return $url;
            }

            return null;
        };
    }

    protected function getTableRecordActionUsing(): ?Closure
    {
        return function (Model $record): ?string {
            foreach (['view', 'edit'] as $action) {
                $action = $this->getCachedTableAction($action);

                if (! $action) {
                    continue;
                }

                $action->record($record);

                if ($action->isHidden()) {
                    continue;
                }

                if ($action->getUrl()) {
                    continue;
                }

                return $action->getName();
            }

            return null;
        };
    }
}
