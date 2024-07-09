<?php

namespace Filament\Resources\Pages;

use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Schema\Schema;
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
            $action instanceof AssociateAction => $this->configureAssociateAction($action),
            $action instanceof AttachAction => $this->configureAttachAction($action),
            $action instanceof CreateAction => $this->configureCreateAction($action),
            $action instanceof DeleteAction => $this->configureDeleteAction($action),
            $action instanceof DetachAction => $this->configureDetachAction($action),
            $action instanceof DissociateAction => $this->configureDissociateAction($action),
            $action instanceof EditAction => $this->configureEditAction($action),
            $action instanceof ForceDeleteAction => $this->configureForceDeleteAction($action),
            $action instanceof ReplicateAction => $this->configureReplicateAction($action),
            $action instanceof RestoreAction => $this->configureRestoreAction($action),
            $action instanceof ViewAction => $this->configureViewAction($action),
            default => null,
        };
    }

    protected function configureAssociateAction(AssociateAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canAssociate());
    }

    protected function configureAttachAction(AttachAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canAttach());
    }

    protected function configureCreateAction(CreateAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canCreate())
            ->form(function (Schema $form): Schema {
                $this->configureForm($form);

                return $form;
            });
    }

    protected function configureDeleteAction(DeleteAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canDelete($record));
    }

    protected function configureDetachAction(DetachAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canDetach($record));
    }

    protected function configureDissociateAction(DissociateAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canDissociate($record));
    }

    protected function configureEditAction(EditAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canEdit($record))
            ->form(function (Schema $form): Schema {
                $this->configureForm($form);

                return $form;
            });
    }

    protected function configureForceDeleteAction(ForceDeleteAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canForceDelete($record));
    }

    protected function configureReplicateAction(ReplicateAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canReplicate($record));
    }

    protected function configureRestoreAction(RestoreAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canRestore($record));
    }

    protected function configureViewAction(ViewAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire, Model $record): bool => $livewire->canView($record))
            ->infolist(function (Schema $infolist): Schema {
                $this->configureInfolist($infolist);

                return $infolist;
            })
            ->form(function (Schema $form): Schema {
                $this->configureForm($form);

                return $form;
            });
    }

    protected function configureTableBulkAction(BulkAction $action): void
    {
        match (true) {
            $action instanceof DeleteBulkAction => $this->configureDeleteBulkAction($action),
            $action instanceof DetachBulkAction => $this->configureDetachBulkAction($action),
            $action instanceof DissociateBulkAction => $this->configureDissociateBulkAction($action),
            $action instanceof ForceDeleteBulkAction => $this->configureForceDeleteBulkAction($action),
            $action instanceof RestoreBulkAction => $this->configureRestoreBulkAction($action),
            default => null,
        };
    }

    protected function configureDeleteBulkAction(DeleteBulkAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canDeleteAny());
    }

    protected function configureDetachBulkAction(DetachBulkAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canDetachAny());
    }

    protected function configureDissociateBulkAction(DissociateBulkAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canDissociateAny());
    }

    protected function configureForceDeleteBulkAction(ForceDeleteBulkAction $action): void
    {
        $action
            ->authorize(static fn (ManageRelatedRecords $livewire): bool => $livewire->canForceDeleteAny());
    }

    protected function configureRestoreBulkAction(RestoreBulkAction $action): void
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

    public function form(Schema $form): Schema
    {
        return $form;
    }

    public function infolist(Schema $infolist): Schema
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
        if ($relatedResource = static::getRelatedResource()) {
            return $relatedResource::canReorder();
        }

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
     * @return array<int | string, string | Schema>
     */
    protected function getForms(): array
    {
        return [];
    }
}
