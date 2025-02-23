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
use Filament\View\PanelsRenderHook;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Url;

use function Filament\authorize;

class ManageRelatedRecords extends Page implements Tables\Contracts\HasTable
{
    use Concerns\HasRelationManagers;
    use Concerns\InteractsWithRecord;
    use InteractsWithRelationshipTable;

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

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve('panels::resources.pages.manage-related-records.navigation-item')
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
        if (filled(static::$navigationLabel)) {
            return static::$navigationLabel;
        }

        if ($relatedResource = static::getRelatedResource()) {
            return $relatedResource::getTitleCasePluralModelLabel();
        }

        return static::getRelationshipTitle();
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
            'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
            "fi-resource-record-{$this->getRecord()->getKey()}",
        ];
    }

    public function getDefaultActionRecord(Action $action): ?Model
    {
        if ($action->getTable()) {
            return null;
        }

        return $this->getRecord();
    }

    public function getDefaultActionRecordTitle(Action $action): ?string
    {
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

        if (
            ($action instanceof CreateAction) &&
            ($relatedResource::hasPage('create'))
        ) {
            return $relatedResource::getUrl('create', shouldGuessMissingParameters: true);
        }

        if (
            ($action instanceof EditAction) &&
            ($relatedResource::hasPage('edit'))
        ) {
            return $relatedResource::getUrl('edit', ['record' => $action->getRecord()], shouldGuessMissingParameters: true);
        }

        if (
            ($action instanceof ViewAction) &&
            ($relatedResource::hasPage('view'))
        ) {
            return $relatedResource::getUrl('view', ['record' => $action->getRecord()], shouldGuessMissingParameters: true);
        }

        return null;
    }
}
