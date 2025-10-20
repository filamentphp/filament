<?php

namespace Filament\Resources\Resource\Concerns;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

use function Filament\get_authorization_response;

trait HasAuthorization
{
    protected static bool $shouldCheckPolicyExistence = true;

    protected static bool $shouldSkipAuthorization = false;

    public static function canAccess(): bool
    {
        return static::canViewAny();
    }

    public static function getAuthorizationResponse(string $action, ?Model $record = null): Response
    {
        if (static::shouldSkipAuthorization()) {
            return Response::allow();
        }

        return get_authorization_response($action, $record ?? static::getModel(), static::shouldCheckPolicyExistence());
    }

    public static function can(string $action, ?Model $record = null): bool
    {
        return static::getAuthorizationResponse($action, $record)->allowed();
    }

    /**
     * @throws AuthorizationException
     */
    public static function authorize(string $action, ?Model $record = null): ?Response
    {
        return static::getAuthorizationResponse($action, $record)->authorize();
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

    public static function getViewAnyAuthorizationResponse(): Response
    {
        return static::getAuthorizationResponse('viewAny');
    }

    public static function getCreateAuthorizationResponse(): Response
    {
        return static::getAuthorizationResponse('create');
    }

    public static function getEditAuthorizationResponse(Model $record): Response
    {
        return static::getAuthorizationResponse('update', $record);
    }

    public static function getDeleteAuthorizationResponse(Model $record): Response
    {
        return static::getAuthorizationResponse('delete', $record);
    }

    public static function getDeleteAnyAuthorizationResponse(): Response
    {
        return static::getAuthorizationResponse('deleteAny');
    }

    public static function getForceDeleteAuthorizationResponse(Model $record): Response
    {
        return static::getAuthorizationResponse('forceDelete', $record);
    }

    public static function getForceDeleteAnyAuthorizationResponse(): Response
    {
        return static::getAuthorizationResponse('forceDeleteAny');
    }

    public static function getReorderAuthorizationResponse(): Response
    {
        return static::getAuthorizationResponse('reorder');
    }

    public static function getReplicateAuthorizationResponse(Model $record): Response
    {
        return static::getAuthorizationResponse('replicate', $record);
    }

    public static function getRestoreAuthorizationResponse(Model $record): Response
    {
        return static::getAuthorizationResponse('restore', $record);
    }

    public static function getRestoreAnyAuthorizationResponse(): Response
    {
        return static::getAuthorizationResponse('restoreAny');
    }

    public static function getViewAuthorizationResponse(Model $record): Response
    {
        return static::getAuthorizationResponse('view', $record);
    }

    public static function canViewAny(): bool
    {
        return static::getViewAnyAuthorizationResponse()->allowed();
    }

    public static function canCreate(): bool
    {
        return static::getCreateAuthorizationResponse()->allowed();
    }

    public static function canEdit(Model $record): bool
    {
        return static::getEditAuthorizationResponse($record)->allowed();
    }

    public static function canDelete(Model $record): bool
    {
        return static::getDeleteAuthorizationResponse($record)->allowed();
    }

    public static function canDeleteAny(): bool
    {
        return static::getDeleteAnyAuthorizationResponse()->allowed();
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::getForceDeleteAuthorizationResponse($record)->allowed();
    }

    public static function canForceDeleteAny(): bool
    {
        return static::getForceDeleteAnyAuthorizationResponse()->allowed();
    }

    public static function canReorder(): bool
    {
        return static::getReorderAuthorizationResponse()->allowed();
    }

    public static function canReplicate(Model $record): bool
    {
        return static::getReplicateAuthorizationResponse($record)->allowed();
    }

    public static function canRestore(Model $record): bool
    {
        return static::getRestoreAuthorizationResponse($record)->allowed();
    }

    public static function canRestoreAny(): bool
    {
        return static::getRestoreAnyAuthorizationResponse()->allowed();
    }

    public static function canView(Model $record): bool
    {
        return static::getViewAuthorizationResponse($record)->allowed();
    }

    public static function authorizeViewAny(): void
    {
        static::getViewAnyAuthorizationResponse()->authorize();
    }

    public static function authorizeCreate(): void
    {
        static::getCreateAuthorizationResponse()->authorize();
    }

    public static function authorizeEdit(Model $record): void
    {
        static::getEditAuthorizationResponse($record)->authorize();
    }

    public static function authorizeView(Model $record): void
    {
        static::getViewAuthorizationResponse($record)->authorize();
    }
}
