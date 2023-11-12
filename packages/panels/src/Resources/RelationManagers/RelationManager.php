<?php

namespace Filament\Resources\RelationManagers;

use Closure;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
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

    protected static bool $isLazy = true;

    public function mount(): void
    {
        $this->loadDefaultActiveTab();
    }

    /**
     * @return array<int | string, string | Form>
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

    public function placeholder(): View
    {
        return view('filament::components.loading-section');
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
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canAssociate());
    }

    protected function configureAttachAction(Tables\Actions\AttachAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canAttach());
    }

    protected function configureCreateAction(Tables\Actions\CreateAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canCreate())
            ->form(fn (Form $form): Form => $this->form($form->columns(2)));
    }

    protected function configureDeleteAction(Tables\Actions\DeleteAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canDelete($record));
    }

    protected function configureDetachAction(Tables\Actions\DetachAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canDetach($record));
    }

    protected function configureDissociateAction(Tables\Actions\DissociateAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canDissociate($record));
    }

    protected function configureEditAction(Tables\Actions\EditAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canEdit($record))
            ->form(fn (Form $form): Form => $this->form($form->columns(2)));
    }

    protected function configureForceDeleteAction(Tables\Actions\ForceDeleteAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canForceDelete($record));
    }

    protected function configureReplicateAction(Tables\Actions\ReplicateAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canReplicate($record));
    }

    protected function configureRestoreAction(Tables\Actions\RestoreAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canRestore($record));
    }

    protected function configureViewAction(Tables\Actions\ViewAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => $livewire->canView($record))
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
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canDeleteAny());
    }

    protected function configureDetachBulkAction(Tables\Actions\DetachBulkAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canDetachAny());
    }

    protected function configureDissociateBulkAction(Tables\Actions\DissociateBulkAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canDissociateAny());
    }

    protected function configureForceDeleteBulkAction(Tables\Actions\ForceDeleteBulkAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire): bool => (! $livewire->isReadOnly()) && $livewire->canForceDeleteAny());
    }

    protected function configureRestoreBulkAction(Tables\Actions\RestoreBulkAction $action): void
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

    public function form(Form $form): Form
    {
        return $form;
    }

    public function infolist(Infolist $infolist): Infolist
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
            ->query($this->getTableQuery())
            ->inverseRelationship(static::getInverseRelationshipName())
            ->modelLabel(static::getModelLabel())
            ->pluralModelLabel(static::getPluralModelLabel())
            ->recordTitleAttribute(static::getRecordTitleAttribute())
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

    public static function isLazy(): bool
    {
        return static::$isLazy;
    }
}
