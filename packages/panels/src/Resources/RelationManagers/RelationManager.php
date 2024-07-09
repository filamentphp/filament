<?php

namespace Filament\Resources\RelationManagers;

use Closure;
use Filament\Actions;
use Filament\Actions\BulkAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Infolists;
use Filament\Pages\Page;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schema\Schema;
use Filament\Support\Concerns\CanBeLazy;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use Livewire\Component;

use function Filament\authorize;

class RelationManager extends Component implements Actions\Contracts\HasActions, Forms\Contracts\HasForms, Infolists\Contracts\HasInfolists, Tables\Contracts\HasTable
{
    use Actions\Concerns\InteractsWithActions;
    use CanBeLazy;
    use Forms\Concerns\InteractsWithForms;
    use Infolists\Concerns\InteractsWithInfolists;
    use InteractsWithRelationshipTable {
        InteractsWithRelationshipTable::makeTable as makeBaseRelationshipTable;
    }

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::resources.relation-manager';

    #[Locked]
    public Model $ownerRecord;

    #[Locked]
    public ?string $pageClass = null;

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected static ?string $recordTitleAttribute = null;

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected static ?string $inverseRelationship = null;

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected static ?string $label = null;

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected static ?string $pluralLabel = null;

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected static ?string $modelLabel = null;

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected static ?string $pluralModelLabel = null;

    protected static ?string $title = null;

    protected static ?string $icon = null;

    protected static IconPosition $iconPosition = IconPosition::Before;

    protected static ?string $badge = null;

    protected static ?string $badgeColor = null;

    protected static ?string $badgeTooltip = null;

    public function mount(): void
    {
        $this->loadDefaultActiveTab();
    }

    /**
     * @return array<int | string, string | Schema>
     */
    protected function getForms(): array
    {
        return [];
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public static function make(array $properties = []): RelationManagerConfiguration
    {
        return app(RelationManagerConfiguration::class, ['relationManager' => static::class, 'properties' => $properties]);
    }

    /**
     * @return array<string>
     */
    public function getRenderHookScopes(): array
    {
        return [
            static::class,
            $this->getPageClass(),
        ];
    }

    public function render(): View
    {
        return view(static::$view, $this->getViewData());
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [];
    }

    public static function getIcon(Model $ownerRecord, string $pageClass): ?string
    {
        return static::$icon;
    }

    public static function getIconPosition(Model $ownerRecord, string $pageClass): IconPosition
    {
        return static::$iconPosition;
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return static::$badge;
    }

    public static function getBadgeColor(Model $ownerRecord, string $pageClass): ?string
    {
        return static::$badgeColor;
    }

    public static function getBadgeTooltip(Model $ownerRecord, string $pageClass): ?string
    {
        return static::$badgeTooltip;
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return static::$title ?? (string) str(static::getRelationshipName())
            ->kebab()
            ->replace('-', ' ')
            ->headline();
    }

    /**
     * @return class-string<Page>
     */
    public function getPageClass(): string
    {
        return $this->pageClass;
    }

    public function getOwnerRecord(): Model
    {
        return $this->ownerRecord;
    }

    protected function configureTableAction(Actions\Action $action): void
    {
        match (true) {
            $action instanceof Actions\AssociateAction => $this->configureAssociateAction($action),
            $action instanceof Actions\AttachAction => $this->configureAttachAction($action),
            $action instanceof Actions\CreateAction => $this->configureCreateAction($action),
            $action instanceof Actions\DeleteAction => $this->configureDeleteAction($action),
            $action instanceof Actions\DetachAction => $this->configureDetachAction($action),
            $action instanceof Actions\DissociateAction => $this->configureDissociateAction($action),
            $action instanceof Actions\EditAction => $this->configureEditAction($action),
            $action instanceof Actions\ForceDeleteAction => $this->configureForceDeleteAction($action),
            $action instanceof Actions\ReplicateAction => $this->configureReplicateAction($action),
            $action instanceof Actions\RestoreAction => $this->configureRestoreAction($action),
            $action instanceof Actions\ViewAction => $this->configureViewAction($action),
            default => null,
        };
    }

    protected function configureAssociateAction(Actions\AssociateAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canAssociate());
    }

    protected function configureAttachAction(Actions\AttachAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canAttach());
    }

    protected function configureCreateAction(Actions\CreateAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canCreate())
            ->form(function (Schema $form): Schema {
                $this->configureForm($form);

                return $form;
            });

        $relatedResource = static::getRelatedResource();

        if ($relatedResource && $relatedResource::hasPage('create')) {
            $action->url(fn (): string => $relatedResource::getUrl('create', [$relatedResource::getParentResourceRegistration()->getParentRouteParameterName() => $this->getOwnerRecord()]));
        }
    }

    protected function configureDeleteAction(Actions\DeleteAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canDelete($record));
    }

    protected function configureDetachAction(Actions\DetachAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canDetach($record));
    }

    protected function configureDissociateAction(Actions\DissociateAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canDissociate($record));
    }

    protected function configureEditAction(Actions\EditAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canEdit($record))
            ->form(function (Schema $form): Schema {
                $this->configureForm($form);

                return $form;
            });

        $relatedResource = static::getRelatedResource();

        if ($relatedResource && $relatedResource::hasPage('edit')) {
            $action->url(fn (Model $record): string => $relatedResource::getUrl('edit', ['record' => $record]));
        }
    }

    protected function configureForceDeleteAction(Actions\ForceDeleteAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canForceDelete($record));
    }

    protected function configureReplicateAction(Actions\ReplicateAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canReplicate($record));
    }

    protected function configureRestoreAction(Actions\RestoreAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canRestore($record));
    }

    protected function configureViewAction(Actions\ViewAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => $livewire->canView($record))
            ->infolist(function (Schema $infolist): Schema {
                $this->configureInfolist($infolist);

                return $infolist;
            })
            ->form(function (Schema $form): Schema {
                $this->configureForm($form);

                return $form;
            });

        $relatedResource = static::getRelatedResource();

        if ($relatedResource && $relatedResource::hasPage('view')) {
            $action->url(fn (Model $record): string => $relatedResource::getUrl('view', ['record' => $record]));
        }
    }

    protected function configureTableBulkAction(BulkAction $action): void
    {
        match (true) {
            $action instanceof Actions\DeleteBulkAction => $this->configureDeleteBulkAction($action),
            $action instanceof Actions\DetachBulkAction => $this->configureDetachBulkAction($action),
            $action instanceof Actions\DissociateBulkAction => $this->configureDissociateBulkAction($action),
            $action instanceof Actions\ForceDeleteBulkAction => $this->configureForceDeleteBulkAction($action),
            $action instanceof Actions\RestoreBulkAction => $this->configureRestoreBulkAction($action),
            default => null,
        };
    }

    protected function configureDeleteBulkAction(Actions\DeleteBulkAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canDeleteAny());
    }

    protected function configureDetachBulkAction(Actions\DetachBulkAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canDetachAny());
    }

    protected function configureDissociateBulkAction(Actions\DissociateBulkAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canDissociateAny());
    }

    protected function configureForceDeleteBulkAction(Actions\ForceDeleteBulkAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canForceDeleteAny());
    }

    protected function configureRestoreBulkAction(Actions\RestoreBulkAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canRestoreAny());
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

    public function isReadOnly(): bool
    {
        if (blank($this->getPageClass())) {
            return false;
        }

        $panel = Filament::getCurrentPanel();

        if (! $panel) {
            return false;
        }

        if (! $panel->hasReadOnlyRelationManagersOnResourceViewPagesByDefault()) {
            return false;
        }

        return is_subclass_of($this->getPageClass(), ViewRecord::class);
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

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    public static function getRecordTitleAttribute(): ?string
    {
        return static::$recordTitleAttribute;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected static function getRecordLabel(): ?string
    {
        return static::$label;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected static function getModelLabel(): ?string
    {
        return static::$modelLabel ?? static::getRecordLabel();
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected static function getPluralRecordLabel(): ?string
    {
        return static::$pluralLabel;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected static function getPluralModelLabel(): ?string
    {
        return static::$pluralModelLabel ?? static::getPluralRecordLabel();
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    public function getInverseRelationshipName(): ?string
    {
        return static::$inverseRelationship;
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (static::shouldSkipAuthorization()) {
            return true;
        }

        $model = $ownerRecord->{static::getRelationshipName()}()->getQuery()->getModel()::class;

        try {
            return authorize('viewAny', $model, static::shouldCheckPolicyExistence())->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }

    protected function makeTable(): Table
    {
        return $this->makeBaseRelationshipTable()
            ->when(static::getInverseRelationshipName(), fn (Table $table, ?string $inverseRelationshipName): Table => $table->inverseRelationship($inverseRelationshipName))
            ->when(static::getModelLabel(), fn (Table $table, string $modelLabel): Table => $table->modelLabel($modelLabel))
            ->when(static::getPluralModelLabel(), fn (Table $table, string $pluralModelLabel): Table => $table->pluralModelLabel($pluralModelLabel))
            ->when(static::getRecordTitleAttribute(), fn (Table $table, string $recordTitleAttribute): Table => $table->recordTitleAttribute($recordTitleAttribute))
            ->heading($this->getTableHeading() ?? static::getTitle($this->getOwnerRecord(), $this->getPageClass()))
            ->when(
                $this->getTableRecordUrlUsing(),
                fn (Table $table, ?Closure $using) => $table->recordUrl($using),
            );
    }

    /**
     * @return array<string, mixed>
     */
    public static function getDefaultProperties(): array
    {
        $properties = [];

        if (static::isLazy()) {
            $properties['lazy'] = true;
        }

        return $properties;
    }
}
