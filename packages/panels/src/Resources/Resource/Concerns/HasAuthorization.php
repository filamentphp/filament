<?php

namespace Filament\Resources\Resource\Concerns;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

use function Filament\authorize;

trait HasAuthorization
{
    protected static bool $shouldCheckPolicyExistence = true;

    protected static bool $shouldSkipAuthorization = false;

    public static function canAccess(): bool
    {
        return static::canViewAny();
    }

    public static function can(string $action, ?Model $record = null): bool
    {
        if (static::shouldSkipAuthorization()) {
            return true;
        }

        $model = static::getModel();

        try {
            return authorize($action, $record ?? $model, static::shouldCheckPolicyExistence())->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }

    /**
     * @throws AuthorizationException
     */
    public static function authorize(string $action, ?Model $record = null): ?Response
    {
        if (static::shouldSkipAuthorization()) {
            return null;
        }

        $model = static::getModel();

        try {
            return authorize($action, $record ?? $model, static::shouldCheckPolicyExistence());
        } catch (AuthorizationException $exception) {
            return $exception->toResponse();
        }
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

    public static function canViewAny(): bool
    {
        return static::can('viewAny');
    }

    public static function canCreate(): bool
    {
        return static::can('create');
    }

    public static function canEdit(Model $record): bool
    {
        return static::can('update', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return static::can('delete', $record);
    }

    public static function canDeleteAny(): bool
    {
        return static::can('deleteAny');
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::can('forceDelete', $record);
    }

    public static function canForceDeleteAny(): bool
    {
        return static::can('forceDeleteAny');
    }

    public static function canReorder(): bool
    {
        return static::can('reorder');
    }

    public static function canReplicate(Model $record): bool
    {
        return static::can('replicate', $record);
    }

    public static function canRestore(Model $record): bool
    {
        return static::can('restore', $record);
    }

    public static function canRestoreAny(): bool
    {
        return static::can('restoreAny');
    }

    public static function canView(Model $record): bool
    {
        return static::can('view', $record);
    }

    public static function authorizeViewAny(): void
    {
        static::authorize('viewAny');
    }

    public static function authorizeCreate(): void
    {
        static::authorize('create');
    }

    public static function authorizeEdit(Model $record): void
    {
        static::authorize('update', $record);
    }

    public static function authorizeView(Model $record): void
    {
        static::authorize('view', $record);
    }
}
