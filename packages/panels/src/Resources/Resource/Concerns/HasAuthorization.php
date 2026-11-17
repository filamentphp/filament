<?php

namespace Filament\Resources\Resource\Concerns;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use ReflectionMethod;

use function Filament\get_authorization_response;

trait HasAuthorization
{
    // Security: Resource authorization delegates to Laravel Model Policies.
    // Standard CRUD operations (`viewAny`, `create`, `update`, `view`,
    // `delete`, `forceDelete`, `restore`, `reorder`) are checked
    // automatically. Bulk actions use `*Any()` policy methods
    // (`deleteAny`, `forceDeleteAny`, `restoreAny`) for performance —
    // use `authorizeIndividualRecords()` if per-record checks are
    // needed. Inline editable table columns only check `update` when
    // `authorize()` is used or strict authorization mode is enabled;
    // otherwise they only respect `disabled()`. Custom actions require manual
    // authorization via `authorize()`, `visible()`, or `hidden()`.

    protected static bool $shouldCheckPolicyExistence = true;

    protected static bool $shouldSkipAuthorization = false;

    /**
     * @var array<string, array{0: ?Model, 1: Response}>
     */
    protected static array $authorizationMethodOverridesInProgress = [];

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

    /**
     * @internal Never override or call this method. It exists only to preserve backwards
     * compatibility for resource `canCreate()`, `canEdit()` and `canView()` overrides
     * after page authorization switched to response methods. It intentionally does not
     * apply to other `can*()` methods and is not a general authorization hook.
     *
     * Security: Policy denials are preserved; legacy overrides can only deny otherwise
     * allowed responses. Put custom authorization in model policies or the corresponding
     * `get*AuthorizationResponse()` methods instead.
     */
    protected static function getAuthorizationResponseUsingMethodOverride(string $method, string $action, ?Model $record = null): Response
    {
        $key = static::class . '::' . $method;

        // The override may call the parent `can*()` method, which calls this
        // method again. Reuse the response computed for the same record.
        if (array_key_exists($key, static::$authorizationMethodOverridesInProgress)) {
            [$inProgressRecord, $inProgressResponse] = static::$authorizationMethodOverridesInProgress[$key];

            return ($inProgressRecord === $record) ? $inProgressResponse : static::getAuthorizationResponse($action, $record);
        }

        $response = static::getAuthorizationResponse($action, $record);

        if ($response->denied()) {
            return $response;
        }

        if ((new ReflectionMethod(static::class, $method))->getDeclaringClass()->getName() === self::class) {
            return $response;
        }

        static::$authorizationMethodOverridesInProgress[$key] = [$record, $response];

        try {
            return static::{$method}(...array_filter([$record])) ? $response : Response::deny();
        } finally {
            unset(static::$authorizationMethodOverridesInProgress[$key]);
        }
    }

    public static function checkPolicyExistence(bool $condition = true): void
    {
        static::$shouldCheckPolicyExistence = $condition;
    }

    public static function skipAuthorization(bool $condition = true): void
    {
        // Security: Disabling authorization removes all policy checks for
        // this resource. All panel users will be able to perform any
        // operation. Not recommended for production.

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
        return static::getAuthorizationResponseUsingMethodOverride('canCreate', 'create');
    }

    public static function getEditAuthorizationResponse(Model $record): Response
    {
        return static::getAuthorizationResponseUsingMethodOverride('canEdit', 'update', $record);
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
        return static::getAuthorizationResponseUsingMethodOverride('canView', 'view', $record);
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
