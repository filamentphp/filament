<?php

namespace Filament\Resources\Concerns;

use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

use function Filament\get_authorization_response;

trait InteractsWithRelationshipTable
{
    use HasTabs;
    use Tables\Concerns\InteractsWithTable {
        makeTable as makeBaseTable;
    }

    protected static string $relationship;

    protected static bool $shouldCheckPolicyExistence = true;

    protected static bool $shouldSkipAuthorization = false;

    protected static ?string $relatedResource = null;

    protected static ?string $relationshipTitle = null;

    public static function getRelatedResource(): ?string
    {
        return static::$relatedResource;
    }

    public static function checkPolicyExistence(bool $condition = true): void
    {
        static::$shouldCheckPolicyExistence = $condition;
    }

    public static function skipAuthorization(bool $condition = true): void
    {
        static::$shouldSkipAuthorization = $condition;
    }

    public static function shouldCheckPolicyExistence(): bool
    {
        return static::$shouldCheckPolicyExistence;
    }

    public static function shouldSkipAuthorization(): bool
    {
        return static::$shouldSkipAuthorization;
    }

    public function getRelationship(): Relation | Builder
    {
        return $this->getOwnerRecord()->{static::getRelationshipName()}();
    }

    public static function getRelationshipName(): string
    {
        if (isset(static::$relationship)) {
            return static::$relationship;
        }

        return static::getRelatedResource()::getParentResourceRegistration()->getRelationshipName();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->columns(2);
    }

    public function form(Schema $schema): Schema
    {
        if (static::getRelatedResource()) {
            static::getRelatedResource()::form($schema);
        }

        return $schema;
    }

    public function defaultInfolist(Schema $schema): Schema
    {
        return $schema->columns(2);
    }

    public function infolist(Schema $schema): Schema
    {
        if (static::getRelatedResource()) {
            static::getRelatedResource()::infolist($schema);
        }

        return $schema;
    }

    protected function makeTable(): Table
    {
        $table = $this->makeBaseTable()
            ->relationship(fn (): Relation | Builder => $this->getRelationship())
            ->modifyQueryUsing($this->modifyQueryWithActiveTab(...))
            ->queryStringIdentifier(Str::lcfirst(class_basename(static::class)))
            ->recordAction(function (Model $record, Table $table): ?string {
                foreach (['view', 'edit'] as $action) {
                    $action = $table->getAction($action);

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
            })
            ->recordUrl(function (Model $record, Table $table): ?string {
                foreach (['view', 'edit'] as $action) {
                    $action = $table->getAction($action);

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
            })
            ->authorizeReorder(fn (): bool => $this->canReorder());

        if ($relatedResource = static::getRelatedResource()) {
            $relatedResource::configureTable($table);
        }

        return $table;
    }

    protected function getAssociateAuthorizationResponse(): Response
    {
        return $this->getAuthorizationResponse('associate');
    }

    protected function getAttachAuthorizationResponse(): Response
    {
        return $this->getAuthorizationResponse('attach');
    }

    protected function getCreateAuthorizationResponse(): Response
    {
        return $this->getAuthorizationResponse('create');
    }

    protected function getDeleteAuthorizationResponse(Model $record): Response
    {
        return $this->getAuthorizationResponse('delete', $record);
    }

    protected function getDeleteAnyAuthorizationResponse(): Response
    {
        return $this->getAuthorizationResponse('deleteAny');
    }

    protected function getDetachAuthorizationResponse(Model $record): Response
    {
        return $this->getAuthorizationResponse('detach', $record);
    }

    protected function getDetachAnyAuthorizationResponse(): Response
    {
        return $this->getAuthorizationResponse('detachAny');
    }

    protected function getDissociateAuthorizationResponse(Model $record): Response
    {
        return $this->getAuthorizationResponse('dissociate', $record);
    }

    protected function getDissociateAnyAuthorizationResponse(): Response
    {
        return $this->getAuthorizationResponse('dissociateAny');
    }

    protected function getEditAuthorizationResponse(Model $record): Response
    {
        return $this->getAuthorizationResponse('update', $record);
    }

    protected function getForceDeleteAuthorizationResponse(Model $record): Response
    {
        return $this->getAuthorizationResponse('forceDelete', $record);
    }

    protected function getForceDeleteAnyAuthorizationResponse(): Response
    {
        return $this->getAuthorizationResponse('forceDeleteAny');
    }

    protected function getReorderAuthorizationResponse(): Response
    {
        return $this->getAuthorizationResponse('reorder');
    }

    protected function getReplicateAuthorizationResponse(Model $record): Response
    {
        return $this->getAuthorizationResponse('replicate', $record);
    }

    protected function getRestoreAuthorizationResponse(Model $record): Response
    {
        return $this->getAuthorizationResponse('restore', $record);
    }

    protected function getRestoreAnyAuthorizationResponse(): Response
    {
        return $this->getAuthorizationResponse('restoreAny');
    }

    protected function getViewAnyAuthorizationResponse(): Response
    {
        return $this->getAuthorizationResponse('viewAny');
    }

    protected function getViewAuthorizationResponse(Model $record): Response
    {
        return $this->getAuthorizationResponse('view', $record);
    }

    protected function canAssociate(): bool
    {
        return $this->getAssociateAuthorizationResponse()->allowed();
    }

    protected function canAttach(): bool
    {
        return $this->getAttachAuthorizationResponse()->allowed();
    }

    protected function canCreate(): bool
    {
        return $this->getCreateAuthorizationResponse()->allowed();
    }

    protected function canDelete(Model $record): bool
    {
        return $this->getDeleteAuthorizationResponse($record)->allowed();
    }

    protected function canDeleteAny(): bool
    {
        return $this->getDeleteAnyAuthorizationResponse()->allowed();
    }

    protected function canDetach(Model $record): bool
    {
        return $this->getDetachAuthorizationResponse($record)->allowed();
    }

    protected function canDetachAny(): bool
    {
        return $this->getDetachAnyAuthorizationResponse()->allowed();
    }

    protected function canDissociate(Model $record): bool
    {
        return $this->getDissociateAuthorizationResponse($record)->allowed();
    }

    protected function canDissociateAny(): bool
    {
        return $this->getDissociateAnyAuthorizationResponse()->allowed();
    }

    protected function canEdit(Model $record): bool
    {
        return $this->getEditAuthorizationResponse($record)->allowed();
    }

    protected function canForceDelete(Model $record): bool
    {
        return $this->getForceDeleteAuthorizationResponse($record)->allowed();
    }

    protected function canForceDeleteAny(): bool
    {
        return $this->getForceDeleteAnyAuthorizationResponse()->allowed();
    }

    protected function canReorder(): bool
    {
        return $this->getReorderAuthorizationResponse()->allowed();
    }

    protected function canReplicate(Model $record): bool
    {
        return $this->getReplicateAuthorizationResponse($record)->allowed();
    }

    protected function canRestore(Model $record): bool
    {
        return $this->getRestoreAuthorizationResponse($record)->allowed();
    }

    protected function canRestoreAny(): bool
    {
        return $this->getRestoreAnyAuthorizationResponse()->allowed();
    }

    protected function canViewAny(): bool
    {
        return $this->getViewAnyAuthorizationResponse()->allowed();
    }

    protected function canView(Model $record): bool
    {
        return $this->getViewAuthorizationResponse($record)->allowed();
    }

    public function getAuthorizationResponse(string $action, ?Model $record = null): Response
    {
        if (static::shouldSkipAuthorization()) {
            return Response::allow();
        }

        if (
            ($relatedResource = static::getRelatedResource()) &&
            (blank($record) || ($record::class === $relatedResource::getModel()))
        ) {
            $method = 'get' . Str::lcfirst($action) . 'AuthorizationResponse';

            return method_exists($relatedResource, $method)
                ? $relatedResource::{$method}($record)
                : $relatedResource::getAuthorizationResponse($action, $record);
        }

        return get_authorization_response($action, $record ?? $this->getTable()->getModel(), static::shouldCheckPolicyExistence());
    }

    protected function can(string $action, ?Model $record = null): bool
    {
        return $this->getAuthorizationResponse($action, $record)->allowed();
    }

    public static function getRelationshipTitle(): string
    {
        if (filled(static::$relationshipTitle)) {
            return static::$relationshipTitle;
        }

        if ($relatedResource = static::getRelatedResource()) {
            return $relatedResource::getTitleCasePluralModelLabel();
        }

        return (string) str(static::getRelationshipName())
            ->kebab()
            ->replace('-', ' ')
            ->ucwords();
    }
}
