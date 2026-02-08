<?php

namespace Filament\Resources\Pages;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
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
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\View\PanelsIconAlias;
use Filament\View\PanelsRenderHook;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Attributes\Url;

use function Filament\authorize;

/**
 * @template TModel of Model = Model
 */
class ManageRelatedRecords extends Page implements Tables\Contracts\HasTable
{
    use Concerns\HasRelationManagers;
    use Concerns\InteractsWithRecord {
        getRecord as getBaseRecord;
    }
    use InteractsWithRelationshipTable;

    public ?string $previousUrl = null;

    #[Url(as: 'reordering')]
    public bool $isTableReordering = false;

    /**
     * @var array<string, mixed> | null
     */
    #[Url(as: 'filters')]
    public ?array $tableFilters = null;

    #[Url(as: 'grouping')]
    public ?string $tableGrouping = null;

    /**
     * @var ?string
     */
    #[Url(as: 'search')]
    public $tableSearch = '';

    #[Url(as: 'sort')]
    public ?string $tableSort = null;

    #[Url(as: 'tab')]
    public ?string $activeTab = null;

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return static::$navigationIcon
            ?? (filled($relatedResource = static::getRelatedResource()) ? $relatedResource::getNavigationIcon() : null)
            ?? FilamentIcon::resolve(PanelsIconAlias::RESOURCES_PAGES_MANAGE_RELATED_RECORDS_NAVIGATION_ITEM)
            ?? Heroicon::OutlinedRectangleStack;
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
        if ($relatedResource = static::getRelatedResource()) {
            return $relatedResource::canAccess();
        }

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
        return static::$breadcrumb ?? static::getRelationshipTitle();
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::getRelationshipTitle();
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

    /**
     * @return array<class-string<RelationManager> | RelationGroup | RelationManagerConfiguration>
     */
    public function getRelationManagers(): array
    {
        return [];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    $this->getTabsContentComponent(),
                    RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_MANAGE_RELATED_RECORDS_TABLE_BEFORE),
                    EmbeddedTable::make(),
                    RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_MANAGE_RELATED_RECORDS_TABLE_AFTER),
                ])->visible(! empty($this->getTable()->getColumns())),
                $this->getRelationManagersContentComponent(),
            ]);
    }

    /**
     * @return array<string>
     */
    public function getPageClasses(): array
    {
        return [
            'fi-resource-manage-related-records-page',
            'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug(Filament::getCurrentOrDefaultPanel())),
            "fi-resource-record-{$this->getRecord()->getKey()}",
        ];
    }

    public function getDefaultActionRecord(Action $action): ?Model
    {
        if ($action instanceof CreateAction) {
            return null;
        }

        if ($action->getTable()) {
            return null;
        }

        return $this->getRecord();
    }

    public function getDefaultActionRelationship(Action $action): ?Relation
    {
        if ($action instanceof CreateAction) {
            return $this->getRelationship();
        }

        return null;
    }

    /**
     * @return ?class-string<Model>
     */
    public function getDefaultActionModel(Action $action): ?string
    {
        if ($action instanceof CreateAction) {
            return $this->getTable()->getModel();
        }

        return parent::getDefaultActionModel($action);
    }

    public function getDefaultActionModelLabel(Action $action): ?string
    {
        if ($action instanceof CreateAction) {
            return $this->getTable()->getModelLabel();
        }

        return parent::getDefaultActionModelLabel($action);
    }

    public function getDefaultActionRecordTitle(Action $action): ?string
    {
        if ($action instanceof CreateAction) {
            return null;
        }

        if ($action->getTable()) {
            return null;
        }

        return $this->getRecordTitle();
    }

    public function getDefaultActionSuccessRedirectUrl(Action $action): ?string
    {
        if ($action->getTable()) {
            return null;
        }

        return parent::getDefaultActionSuccessRedirectUrl($action);
    }

    public function getDefaultActionAuthorizationResponse(Action $action): ?Response
    {
        if ($action instanceof CreateAction) {
            return $this->getCreateAuthorizationResponse();
        }

        if ($action->getTable()) {
            return match (true) {
                $action instanceof DeleteAction => $this->getDeleteAuthorizationResponse($action->getRecord()),
                $action instanceof EditAction => $this->getEditAuthorizationResponse($action->getRecord()),
                $action instanceof ForceDeleteAction => $this->getForceDeleteAuthorizationResponse($action->getRecord()),
                $action instanceof ReplicateAction => $this->getReplicateAuthorizationResponse($action->getRecord()),
                $action instanceof RestoreAction => $this->getRestoreAuthorizationResponse($action->getRecord()),
                $action instanceof ViewAction => $this->getViewAuthorizationResponse($action->getRecord()),
                $action instanceof DeleteBulkAction => $this->getDeleteAnyAuthorizationResponse(),
                $action instanceof ForceDeleteBulkAction => $this->getForceDeleteAnyAuthorizationResponse(),
                $action instanceof RestoreBulkAction => $this->getRestoreAnyAuthorizationResponse(),
                default => null,
            };
        }

        return parent::getDefaultActionAuthorizationResponse($action);
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

    public function getTitle(): string | Htmlable
    {
        if (filled(static::$title)) {
            return static::$title;
        }

        return __('filament-panels::resources/pages/manage-related-records.title', [
            'label' => $this->getRecordTitle(),
            'relationship' => static::getRelationshipTitle(),
        ]);
    }

    /**
     * @return TModel
     */
    public function getRecord(): Model
    {
        return $this->getBaseRecord();
    }
}
