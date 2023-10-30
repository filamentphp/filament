<?php

namespace Filament\Resources\Pages;

use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Url;

use function Filament\authorize;

class ManageRelatedRecords extends Page implements Tables\Contracts\HasTable
{
    use Concerns\HasRelationManagers;
    use Concerns\InteractsWithRecord;
    use InteractsWithRelationshipTable;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::resources.pages.manage-related-records';

    public ?string $previousUrl = null;

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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->previousUrl = url()->previous();

        $this->loadDefaultActiveTab();
    }

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        abort_unless(static::canAccess($this->getRecord()), 403);
    }

    public static function canAccess(?Model $record = null): bool
    {
        if (! $record) {
            return false;
        }

        if (static::shouldSkipAuthorization()) {
            return true;
        }

        $model = $record->{static::getRelationshipName()}()->getQuery()->getModel()::class;

        try {
            return authorize('viewAny', $model, static::shouldCheckPolicyExistence())->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? static::getTitle();
    }

    /**
     * @return class-string<Page>
     */
    public function getPageClass(): string
    {
        return static::class;
    }

    public function getOwnerRecord(): Model
    {
        return $this->getRecord();
    }

    protected function getMountedActionFormModel(): Model
    {
        return $this->getRecord();
    }

    /**
     * @return array<string, mixed>
     */
    public function getWidgetData(): array
    {
        return [
            'record' => $this->getRecord(),
        ];
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
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canAssociate());
    }

    protected function configureAttachAction(Tables\Actions\AttachAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canAttach());
    }

    protected function configureCreateAction(Tables\Actions\CreateAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canCreate())
            ->form(fn (Form $form): Form => $this->form($form->columns(2)));
    }

    protected function configureDeleteAction(Tables\Actions\DeleteAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canDelete($record));
    }

    protected function configureDetachAction(Tables\Actions\DetachAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canDetach($record));
    }

    protected function configureDissociateAction(Tables\Actions\DissociateAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canDissociate($record));
    }

    protected function configureEditAction(Tables\Actions\EditAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canEdit($record))
            ->form(fn (Form $form): Form => $this->form($form->columns(2)));
    }

    protected function configureForceDeleteAction(Tables\Actions\ForceDeleteAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canForceDelete($record));
    }

    protected function configureReplicateAction(Tables\Actions\ReplicateAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canReplicate($record));
    }

    protected function configureRestoreAction(Tables\Actions\RestoreAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canRestore($record));
    }

    protected function configureViewAction(Tables\Actions\ViewAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canView($record))
            ->infolist(fn (Infolist $infolist): Infolist => $this->infolist($infolist->columns(2)))
            ->form(fn (Form $form): Form => $this->form($form->columns(2)));
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
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canDeleteAny());
    }

    protected function configureDetachBulkAction(Tables\Actions\DetachBulkAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canDetachAny());
    }

    protected function configureDissociateBulkAction(Tables\Actions\DissociateBulkAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canDissociateAny());
    }

    protected function configureForceDeleteBulkAction(Tables\Actions\ForceDeleteBulkAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canForceDeleteAny());
    }

    protected function configureRestoreBulkAction(Tables\Actions\RestoreBulkAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canRestoreAny());
    }

    protected function can(string $action, ?Model $record = null): bool
    {
        if (static::shouldSkipAuthorization()) {
            return true;
        }

        $model = $this->getTable()->getModel();

        try {
            return authorize($action, $record ?? $model, static::shouldCheckPolicyExistence())->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }

    public function form(Form $form): Form
    {
        return $form;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist;
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

    protected function canViewAny(): bool
    {
        return $this->can('viewAny');
    }

    protected function canView(Model $record): bool
    {
        return $this->can('view', $record);
    }

    /**
     * @return array<class-string<RelationManager> | RelationGroup | RelationManagerConfiguration>
     */
    public function getRelationManagers(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getSubNavigationParameters(): array
    {
        return [
            'record' => $this->getRecord(),
        ];
    }

    public function getSubNavigation(): array
    {
        return static::getResource()::getRecordSubNavigation($this);
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return parent::shouldRegisterNavigation($parameters) && static::canAccess($parameters['record']);
    }
}
