<?php

namespace Filament\Resources\Pages;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Schema\ComponentContainer;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Url;

use function Filament\authorize;

class ManageRelatedRecords extends Page implements Tables\Contracts\HasTable
{
    use Concerns\HasRelationManagers;
    use Concerns\InteractsWithRecord {
        configureAction as configureActionRecord;
    }
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

    public static function getNavigationIcon(): string | Htmlable | null
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve('panels::resources.pages.manage-related-records.navigation-item')
            ?? 'heroicon-o-rectangle-stack';
    }

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->previousUrl = url()->previous();

        $this->loadDefaultActiveTab();
    }

    protected function authorizeAccess(): void
    {
        abort_unless(static::canAccess(['record' => $this->getRecord()]), 403);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;

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

    protected function configureAction(Action $action): void
    {
        $this->configureActionRecord($action);
    }

    protected function configureTableAction(Action $action): void
    {
        match (true) {
            $action instanceof \Filament\Actions\AssociateAction => $this->configureAssociateAction($action),
            $action instanceof \Filament\Actions\AttachAction => $this->configureAttachAction($action),
            $action instanceof \Filament\Actions\CreateAction => $this->configureCreateAction($action),
            $action instanceof \Filament\Actions\DeleteAction => $this->configureDeleteAction($action),
            $action instanceof \Filament\Actions\DetachAction => $this->configureDetachAction($action),
            $action instanceof \Filament\Actions\DissociateAction => $this->configureDissociateAction($action),
            $action instanceof \Filament\Actions\EditAction => $this->configureEditAction($action),
            $action instanceof \Filament\Actions\ForceDeleteAction => $this->configureForceDeleteAction($action),
            $action instanceof \Filament\Actions\ReplicateAction => $this->configureReplicateAction($action),
            $action instanceof \Filament\Actions\RestoreAction => $this->configureRestoreAction($action),
            $action instanceof \Filament\Actions\ViewAction => $this->configureViewAction($action),
            default => null,
        };
    }

    protected function configureAssociateAction(\Filament\Actions\AssociateAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canAssociate());
    }

    protected function configureAttachAction(\Filament\Actions\AttachAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canAttach());
    }

    protected function configureCreateAction(\Filament\Actions\CreateAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canCreate())
            ->form(fn (ComponentContainer $form): ComponentContainer => $this->form($form->columns(2)));
    }

    protected function configureDeleteAction(\Filament\Actions\DeleteAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canDelete($record));
    }

    protected function configureDetachAction(\Filament\Actions\DetachAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canDetach($record));
    }

    protected function configureDissociateAction(\Filament\Actions\DissociateAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canDissociate($record));
    }

    protected function configureEditAction(\Filament\Actions\EditAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canEdit($record))
            ->form(fn (ComponentContainer $form): ComponentContainer => $this->form($form->columns(2)));
    }

    protected function configureForceDeleteAction(\Filament\Actions\ForceDeleteAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canForceDelete($record));
    }

    protected function configureReplicateAction(\Filament\Actions\ReplicateAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canReplicate($record));
    }

    protected function configureRestoreAction(\Filament\Actions\RestoreAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canRestore($record));
    }

    protected function configureViewAction(\Filament\Actions\ViewAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canView($record))
            ->infolist(fn (ComponentContainer $infolist): ComponentContainer => $this->infolist($infolist->columns(2)))
            ->form(fn (ComponentContainer $form): ComponentContainer => $this->form($form->columns(2)));
    }

    protected function configureTableBulkAction(BulkAction $action): void
    {
        match (true) {
            $action instanceof \Filament\Actions\DeleteBulkAction => $this->configureDeleteBulkAction($action),
            $action instanceof \Filament\Actions\DetachBulkAction => $this->configureDetachBulkAction($action),
            $action instanceof \Filament\Actions\DissociateBulkAction => $this->configureDissociateBulkAction($action),
            $action instanceof \Filament\Actions\ForceDeleteBulkAction => $this->configureForceDeleteBulkAction($action),
            $action instanceof \Filament\Actions\RestoreBulkAction => $this->configureRestoreBulkAction($action),
            default => null,
        };
    }

    protected function configureDeleteBulkAction(\Filament\Actions\DeleteBulkAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canDeleteAny());
    }

    protected function configureDetachBulkAction(\Filament\Actions\DetachBulkAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canDetachAny());
    }

    protected function configureDissociateBulkAction(\Filament\Actions\DissociateBulkAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canDissociateAny());
    }

    protected function configureForceDeleteBulkAction(\Filament\Actions\ForceDeleteBulkAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canForceDeleteAny());
    }

    protected function configureRestoreBulkAction(\Filament\Actions\RestoreBulkAction $action): void
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

    public function form(ComponentContainer $form): ComponentContainer
    {
        return $form;
    }

    public function infolist(ComponentContainer $infolist): ComponentContainer
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
     * @return array<int | string, string | ComponentContainer>
     */
    protected function getForms(): array
    {
        return [];
    }
}
