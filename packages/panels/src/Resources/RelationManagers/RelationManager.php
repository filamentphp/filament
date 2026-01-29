<?php

namespace Filament\Resources\RelationManagers;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\AttachAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
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
use Filament\Actions\ImportAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasRenderHookScopes;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Concerns\CanBeLazy;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use Livewire\Component;

use function Filament\authorize;

class RelationManager extends Component implements HasActions, HasRenderHookScopes, HasSchemas, HasTable
{
    use CanBeLazy;
    use InteractsWithActions;
    use InteractsWithRelationshipTable {
        InteractsWithRelationshipTable::makeTable as makeBaseRelationshipTable;
        InteractsWithRelationshipTable::canReorder as baseCanReorder;
    }
    use InteractsWithSchemas;

    /**
     * @var view-string
     */
    protected string $view = 'filament-panels::resources.relation-manager';

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

    protected static string | BackedEnum | null $icon = null;

    protected static IconPosition $iconPosition = IconPosition::Before;

    protected static ?string $badge = null;

    protected static ?string $badgeColor = null;

    protected static string | Htmlable | null $badgeTooltip = null;

    public function mount(): void
    {
        $this->loadDefaultActiveTab();
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
        return view($this->view, $this->getViewData());
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [];
    }

    public static function getTabComponent(Model $ownerRecord, string $pageClass): Tab
    {
        return Tab::make(static::class::getTitle($ownerRecord, $pageClass))
            ->badge(static::class::getBadge($ownerRecord, $pageClass))
            ->badgeColor(static::class::getBadgeColor($ownerRecord, $pageClass))
            ->badgeTooltip(static::class::getBadgeTooltip($ownerRecord, $pageClass))
            ->icon(static::class::getIcon($ownerRecord, $pageClass))
            ->iconPosition(static::class::getIconPosition($ownerRecord, $pageClass));
    }

    public static function getIcon(Model $ownerRecord, string $pageClass): string | BackedEnum | Htmlable | null
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

    public static function getBadgeTooltip(Model $ownerRecord, string $pageClass): string | Htmlable | null
    {
        return static::$badgeTooltip;
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return static::$title ?? static::getRelationshipTitle();
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

    public function isReadOnly(): bool
    {
        if (blank($this->getPageClass())) {
            return false;
        }

        $panel = Filament::getCurrentOrDefaultPanel();

        if (! $panel) {
            return false;
        }

        if (! $panel->hasReadOnlyRelationManagersOnResourceViewPagesByDefault()) {
            return false;
        }

        return is_subclass_of($this->getPageClass(), ViewRecord::class);
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

        if ($relatedResource = static::getRelatedResource()) {
            return $relatedResource::canAccess();
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
            ->heading($this->getTableHeading() ?? static::getTitle($this->getOwnerRecord(), $this->getPageClass()));
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

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getTabsContentComponent(),
                RenderHook::make(PanelsRenderHook::RESOURCE_RELATION_MANAGER_BEFORE),
                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_RELATION_MANAGER_AFTER),
            ]);
    }

    protected function canReorder(): bool
    {
        return $this->isReadOnly() ? false : $this->baseCanReorder();
    }

    public function getDefaultActionAuthorizationResponse(Action $action): ?Response
    {
        if ($action instanceof ViewAction) {
            return $this->getViewAuthorizationResponse($action->getRecord());
        }

        return match (true) {
            $action instanceof AssociateAction, $action instanceof AttachAction, $action instanceof DetachAction, $action instanceof DetachBulkAction, $action instanceof DissociateAction, $action instanceof DissociateBulkAction, $action instanceof ImportAction => $this->isReadOnly() ? Response::deny() : null,
            $action instanceof CreateAction => $this->isReadOnly() ? Response::deny() : $this->getCreateAuthorizationResponse(),
            $action instanceof DeleteAction => $this->isReadOnly() ? Response::deny() : $this->getDeleteAuthorizationResponse($action->getRecord()),
            $action instanceof DeleteBulkAction => $this->isReadOnly() ? Response::deny() : $this->getDeleteAnyAuthorizationResponse(),
            $action instanceof EditAction => $this->isReadOnly() ? Response::deny() : $this->getEditAuthorizationResponse($action->getRecord()),
            $action instanceof ForceDeleteAction => $this->isReadOnly() ? Response::deny() : $this->getForceDeleteAuthorizationResponse($action->getRecord()),
            $action instanceof ForceDeleteBulkAction => $this->isReadOnly() ? Response::deny() : $this->getForceDeleteAnyAuthorizationResponse(),
            $action instanceof ReplicateAction => $this->isReadOnly() ? Response::deny() : $this->getReplicateAuthorizationResponse($action->getRecord()),
            $action instanceof RestoreAction => $this->isReadOnly() ? Response::deny() : $this->getRestoreAuthorizationResponse($action->getRecord()),
            $action instanceof RestoreBulkAction => $this->isReadOnly() ? Response::deny() : $this->getRestoreAnyAuthorizationResponse(),
            default => null,
        };
    }

    public function getDefaultActionIndividualRecordAuthorizationResponseResolver(Action $action): ?Closure
    {
        return match (true) {
            $action instanceof DeleteBulkAction => fn (Model $record): Response => $this->getDeleteAuthorizationResponse($record),
            $action instanceof ForceDeleteBulkAction => fn (Model $record): Response => $this->getForceDeleteAuthorizationResponse($record),
            $action instanceof RestoreBulkAction => fn (Model $record): Response => $this->getRestoreAuthorizationResponse($record),
            default => null,
        };
    }

    public function getDefaultActionSchemaResolver(Action $action): ?Closure
    {
        return match (true) {
            $action instanceof CreateAction, $action instanceof EditAction => fn (Schema $schema): Schema => $this->form($this->defaultForm($schema)),
            $action instanceof ViewAction => fn (Schema $schema): Schema => $this->infolist($this->defaultInfolist($this->form($this->defaultForm($schema)))),
            default => null,
        };
    }

    public function getDefaultActionUrl(Action $action): ?string
    {
        $relatedResource = static::getRelatedResource();

        if (! $relatedResource) {
            return null;
        }

        $actionModel = $action->getModel();

        if (
            ($action instanceof CreateAction) &&
            ($relatedResource::hasPage('create')) &&
            (blank($actionModel) || ($actionModel === $relatedResource::getModel()))
        ) {
            return $relatedResource::getUrl('create', shouldGuessMissingParameters: true);
        }

        if (
            ($action instanceof EditAction) &&
            ($relatedResource::hasPage('edit')) &&
            (blank($actionModel) || ($actionModel === $relatedResource::getModel()))
        ) {
            return $relatedResource::getUrl('edit', ['record' => $action->getRecord()], shouldGuessMissingParameters: true);
        }

        if (
            ($action instanceof ViewAction) &&
            ($relatedResource::hasPage('view')) &&
            (blank($actionModel) || ($actionModel === $relatedResource::getModel()))
        ) {
            return $relatedResource::getUrl('view', ['record' => $action->getRecord()], shouldGuessMissingParameters: true);
        }

        return null;
    }
}
