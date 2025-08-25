<?php

namespace Filament\Actions\Concerns;

use Closure;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Actions\Enums\ActionStatus;
use Filament\Actions\Exceptions\ActionNotResolvableException;
use Filament\Schemas\Components\Contracts\ExposesStateToActionData;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use ReflectionMethod;
use ReflectionNamedType;
use Throwable;

use function Livewire\store;

trait InteractsWithActions
{
    use WithRateLimiting;

    /**
     * @var array<array<string, mixed>> | null
     */
    public ?array $mountedActions = [];

    protected ?int $originallyMountedActionIndex = null;

    /**
     * @var mixed
     */
    #[Url(as: 'action')]
    public $defaultAction = null;

    /**
     * @var mixed
     */
    #[Url(as: 'actionArguments')]
    public $defaultActionArguments = null;

    /**
     * @var mixed
     */
    #[Url(as: 'actionContext')]
    public $defaultActionContext = null;

    /**
     * @var mixed
     */
    #[Url(as: 'tableAction')]
    public $defaultTableAction = null;

    /**
     * @var mixed
     */
    #[Url(as: 'tableActionRecord')]
    public $defaultTableActionRecord = null;

    /**
     * @var mixed
     */
    #[Url(as: 'tableActionArguments')]
    public $defaultTableActionArguments = null;

    /**
     * @var array<string, Action>
     */
    protected array $cachedActions = [];

    /**
     * @var array<Action> | null
     */
    protected ?array $cachedMountedActions = null;

    protected bool $hasActionsModalRendered = false;

    public function bootedInteractsWithActions(): void
    {
        if (filled($originallyMountedActionIndex = array_key_last($this->mountedActions))) {
            $this->originallyMountedActionIndex = $originallyMountedActionIndex;
        }

        $this->cacheTraitActions();

        // Boot the InteractsWithTable trait first so the table object is available.
        if (! ($this instanceof HasTable)) {
            $this->cacheMountedActions($this->mountedActions);
        }
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @param  array<string, mixed>  $context
     */
    public function mountAction(string $name, array $arguments = [], array $context = []): mixed
    {
        $this->mountedActions[] = [
            'name' => $name,
            'arguments' => $arguments,
            'context' => $context,
        ];

        try {
            $action = $this->getMountedAction();
        } catch (ActionNotResolvableException $exception) {
            $action = null;
        }

        if (! $action) {
            $this->unmountAction(canCancelParentActions: false);

            return null;
        }

        if ($action->isDisabled()) {
            $this->unmountAction(canCancelParentActions: false);

            return null;
        }

        if (($actionComponent = $action->getSchemaComponent()) instanceof ExposesStateToActionData) {
            foreach ($actionComponent->getChildSchemas() as $actionComponentChildSchema) {
                $actionComponentChildSchema->validate();
            }
        }

        try {
            if (
                $action->hasAuthorizationNotification() &&
                ($response = $action->getAuthorizationResponseWithMessage())->denied()
            ) {
                $action->sendUnauthorizedNotification($response);

                throw new Cancel;
            }

            $hasSchema = $this->mountedActionHasSchema(mountedAction: $action);

            if ($hasSchema) {
                $action->callBeforeFormFilled();
            }

            $schema = $this->getMountedActionSchema(mountedAction: $action);

            $action->mount([
                'form' => $schema,
                'schema' => $schema,
            ]);

            if ($hasSchema) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return null;
        } catch (Cancel $exception) {
            $this->unmountAction(canCancelParentActions: false);

            return null;
        }

        if (! $this->mountedActionShouldOpenModal(mountedAction: $action)) {
            return $this->callMountedAction();
        }

        $this->syncActionModals();

        $this->resetErrorBag();

        return null;
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function callMountedAction(array $arguments = []): mixed
    {
        $action = $this->getMountedAction();

        if (! $action) {
            return null;
        }

        $action->mergeArguments($arguments);

        if ($action->isDisabled()) {
            return null;
        }

        if (
            $action->hasAuthorizationNotification() &&
            (! $action->isAuthorized())
        ) {
            return null;
        }

        if ($rateLimit = $action->getRateLimit()) {
            try {
                $this->rateLimit($rateLimit, method: json_encode(array_map(fn (array $action): array => Arr::except($action, ['data']), $this->mountedActions)));
            } catch (TooManyRequestsException $exception) {
                $action->sendRateLimitedNotification($exception);

                return null;
            }
        }

        $schema = $this->getMountedActionSchema(mountedAction: $action);

        $originallyMountedActions = $this->mountedActions;

        $result = null;

        try {
            $action->beginDatabaseTransaction();

            $schemaState = [];

            if (($actionComponent = $action->getSchemaComponent()) instanceof ExposesStateToActionData) {
                foreach ($actionComponent->getChildSchemas() as $actionComponentChildSchema) {
                    $schemaState = [
                        ...$schemaState,
                        ...$actionComponentChildSchema->getState(),
                    ];
                }
            }

            if ($this->mountedActionHasSchema(mountedAction: $action)) {
                $action->callBeforeFormValidated();

                $schema->getState(afterValidate: function (array $state) use ($action, $schemaState): void {
                    $action->callAfterFormValidated();

                    $action->data([
                        ...$schemaState,
                        ...$state,
                    ]);

                    $action->callBefore();
                });
            } else {
                $action->data($schemaState);

                $action->callBefore();
            }

            $result = $action->call([
                'form' => $schema,
                'schema' => $schema,
            ]);

            $result = $action->callAfter() ?? $result;

            $this->afterActionCalled();

            (match ($action->getStatus()) {
                ActionStatus::Success => function () use ($action): void {
                    $action->sendSuccessNotification();
                    $action->dispatchSuccessRedirect();
                },
                ActionStatus::Failure => function () use ($action): void {
                    $action->sendFailureNotification();
                    $action->dispatchFailureRedirect();
                },
            })();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $action->rollBackDatabaseTransaction() :
                $action->commitDatabaseTransaction();

            return null;
        } catch (Cancel $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $action->rollBackDatabaseTransaction() :
                $action->commitDatabaseTransaction();
        } catch (ValidationException $exception) {
            $action->rollBackDatabaseTransaction();

            if (! $this->mountedActionShouldOpenModal(mountedAction: $action)) {
                $action->resetArguments();
                $action->resetData();

                $this->unmountAction();
            }

            throw $exception;
        } catch (Throwable $exception) {
            $action->rollBackDatabaseTransaction();

            throw $exception;
        }

        $action->commitDatabaseTransaction();

        if (store($this)->has('redirect')) {
            $this->unmountAction();

            return $result;
        }

        $action->resetArguments();
        $action->resetData();

        $onlyActionNamesAndContexts = fn (array $actions): array => collect($actions)
            ->map(fn (array $action): array => Arr::only($action, ['name', 'context']))
            ->all();

        // If the action was replaced while it was being called,
        // we don't want to unmount it.
        if ($onlyActionNamesAndContexts($originallyMountedActions) !== $onlyActionNamesAndContexts($this->mountedActions)) {
            $action->clearRecordAfter();

            return null;
        }

        $this->unmountAction();

        return $result;
    }

    protected function afterActionCalled(): void {}

    /**
     * @param  array<string, mixed>  $arguments
     * @param  array<string, mixed>  $context
     */
    public function replaceMountedAction(string $name, array $arguments = [], array $context = []): void
    {
        $this->mountedActions = [];
        $this->cachedMountedActions = null;

        foreach ($this->cachedSchemas as $schemaName => $schema) {
            if (str($schemaName)->startsWith('mountedActionSchema')) {
                unset($this->cachedSchemas[$schemaName]);
            }
        }

        $this->mountAction($name, $arguments, $context);
    }

    public function cacheAction(Action $action): Action
    {
        $action->livewire($this);

        return $this->cachedActions[$action->getName()] = $action;
    }

    /**
     * @param  array<string, Action>  $actions
     */
    protected function mergeCachedActions(array $actions): void
    {
        $this->cachedActions = [
            ...$this->cachedActions,
            ...$actions,
        ];
    }

    public function mountedActionShouldOpenModal(?Action $mountedAction = null): bool
    {
        return ($mountedAction ?? $this->getMountedAction())->shouldOpenModal(
            checkForSchemaUsing: $this->mountedActionHasSchema(...),
        );
    }

    public function mountedActionHasSchema(?Action $mountedAction = null): bool
    {
        return (bool) count($this->getMountedActionSchema(mountedAction: $mountedAction)?->getComponents() ?? []);
    }

    /**
     * @deprecated Use `mountedActionHasSchema()` instead.
     */
    public function mountedActionHasForm(?Action $mountedAction = null): bool
    {
        return $this->mountedActionHasSchema($mountedAction);
    }

    /**
     * @return array<Action>
     */
    public function getMountedActions(): array
    {
        if (blank($this->mountedActions ?? [])) {
            return [];
        }

        if (array_key_exists(count($this->mountedActions) - 1, $this->cachedMountedActions ?? [])) {
            return $this->cachedMountedActions;
        }

        return $this->cacheMountedActions($this->mountedActions);
    }

    public function cacheTraitActions(): void
    {
        foreach (class_uses_recursive($class = static::class) as $trait) {
            $traitBasename = class_basename($trait);

            if (
                str($traitBasename)->endsWith('Actions') &&
                method_exists($class, $method = "cache{$traitBasename}")
            ) {
                $this->{$method}();
            }
        }
    }

    public function getMountedAction(?int $actionNestingIndex = null): ?Action
    {
        if (! count($this->mountedActions ?? [])) {
            return null;
        }

        $actionNestingIndex ??= (count($this->mountedActions) - 1);

        if (array_key_exists($actionNestingIndex, $this->cachedMountedActions ?? [])) {
            return $this->cachedMountedActions[$actionNestingIndex];
        }

        $this->cacheMountedActions(
            Arr::take($this->mountedActions, $actionNestingIndex + 1),
        );

        return Arr::last($this->cachedMountedActions);
    }

    /**
     * @param  array<string, mixed>  $mountedActions
     * @return array<Action>
     */
    protected function cacheMountedActions(array $mountedActions): array
    {
        return $this->cachedMountedActions = $this->resolveActions($mountedActions);
    }

    /**
     * @param  array<array<string, mixed>>  $actions
     * @return array<Action>
     */
    protected function resolveActions(array $actions): array
    {
        $resolvedActions = [];

        foreach ($actions as $actionNestingIndex => $action) {
            if (blank($action['name'] ?? null)) {
                throw new ActionNotResolvableException('An action tried to resolve without a name.');
            }

            if (filled($action['context']['schemaComponent'] ?? null)) {
                $resolvedAction = $this->resolveSchemaComponentAction($action, $resolvedActions);
            } elseif ($this instanceof HasTable && filled($action['context']['table'] ?? null)) {
                $resolvedAction = $this->resolveTableAction($action, $resolvedActions);
            } else {
                $resolvedAction = $this->resolveAction($action, $resolvedActions);
            }

            if (! $resolvedAction) {
                continue;
            }

            $resolvedAction->nestingIndex($actionNestingIndex);
            $resolvedAction->boot();

            $resolvedActions[] = $resolvedAction;

            $this->cacheSchema(
                "mountedActionSchema{$actionNestingIndex}",
                $this->getMountedActionSchema($actionNestingIndex, $resolvedAction),
            );
        }

        return $resolvedActions;
    }

    /**
     * @param  array<string, mixed>  $action
     * @param  array<Action>  $parentActions
     */
    protected function resolveAction(array $action, array $parentActions): ?Action
    {
        if (count($parentActions)) {
            $parentAction = Arr::last($parentActions);
            $resolvedAction = $parentAction->getModalAction($action['name']) ?? throw new ActionNotResolvableException("Action [{$action['name']}] was not found for action [{$parentAction->getName()}].");
        } elseif (array_key_exists($action['name'], $this->cachedActions)) {
            $resolvedAction = $this->cachedActions[$action['name']];
        } else {
            if (
                (! str($action['name'])->endsWith('Action')) &&
                method_exists($this, "{$action['name']}Action")
            ) {
                $methodName = "{$action['name']}Action";
            } elseif (method_exists($this, $action['name'])) {
                $methodName = $action['name'];
            } else {
                return null;
            }

            $returnTypeReflection = (new ReflectionMethod($this, $methodName))->getReturnType();

            if (! $returnTypeReflection) {
                return null;
            }

            if (! $returnTypeReflection instanceof ReflectionNamedType) {
                return null;
            }

            $type = $returnTypeReflection->getName();

            if (! is_a($type, Action::class, allow_string: true)) {
                return null;
            }

            $resolvedAction = $this->{$methodName}();

            if (! $resolvedAction instanceof Action) {
                throw new ActionNotResolvableException('Actions must be an instance of ' . Action::class . ". The [{$methodName}] method on the Livewire component returned an instance of [" . get_class($resolvedAction) . '].');
            }

            $this->cacheAction($resolvedAction);
        }

        return $resolvedAction;
    }

    /**
     * @param  array<string, mixed>  $action
     * @param  array<Action>  $parentActions
     */
    protected function resolveTableAction(array $action, array $parentActions): Action
    {
        if (! ($this instanceof HasTable)) {
            throw new ActionNotResolvableException('Failed to resolve table action for Livewire component without the [' . HasTable::class . '] trait.');
        }

        $resolvedAction = null;

        if (count($parentActions)) {
            $parentAction = Arr::last($parentActions);
            $resolvedAction = $parentAction->getModalAction($action['name']) ?? throw new ActionNotResolvableException("Action [{$action['name']}] was not found for action [{$parentAction->getName()}].");
        } else {
            if ($action['context']['bulk'] ?? false) {
                $resolvedAction = $this->getTable()->getBulkAction($action['name']);
            }

            $resolvedAction ??= $this->getTable()->getAction($action['name']) ?? throw new ActionNotResolvableException("Action [{$action['name']}] not found on table.");
        }

        if (filled($action['context']['recordKey'] ?? null)) {
            $record = $this->getTableRecord($action['context']['recordKey']);

            $resolvedAction->getRootGroup()?->record($record) ?? $resolvedAction->record($record);
        }

        return $resolvedAction;
    }

    /**
     * @param  array<string, mixed>  $action
     * @param  array<Action>  $parentActions
     */
    protected function resolveSchemaComponentAction(array $action, array $parentActions): Action
    {
        if (! $this instanceof HasSchemas) {
            throw new ActionNotResolvableException('Failed to resolve action schema for Livewire component without the [' . InteractsWithSchemas::class . '] trait.');
        }

        $key = $action['context']['schemaComponent'];

        $schemaName = (string) str($key)->before('.');

        $schema = $this->getSchema($schemaName);

        if (! $schema) {
            throw new ActionNotResolvableException("Schema [{$schemaName}] not found.");
        }

        $resolvedAction = $schema->getAction(
            $action['name'],
            str($key)->contains('.') ? (string) str($key)->after('.') : null,
        );

        if (! $resolvedAction) {
            throw new ActionNotResolvableException("Action [{$action['name']}] not found in schema at [{$action['context']['schemaComponent']}].");
        }

        return $resolvedAction;
    }

    /**
     * @param  string | array<string>  $actions
     */
    public function getAction(string | array $actions): ?Action
    {
        $actions = array_map(
            fn (string | array $action): array => is_array($action) ? $action : ['name' => $action],
            Arr::wrap($actions),
        );

        return Arr::last($this->resolveActions($actions));
    }

    public function getMountedActionSchemaName(): ?string
    {
        if (empty($this->mountedActions)) {
            return null;
        }

        return 'mountedActionSchema' . array_key_last($this->mountedActions);
    }

    protected function getMountedActionSchema(?int $actionNestingIndex = null, ?Action $mountedAction = null): ?Schema
    {
        $actionNestingIndex ??= $mountedAction?->getNestingIndex() ?? array_key_last($this->mountedActions);

        $mountedAction ??= $this->getMountedAction($actionNestingIndex);

        if (! ($mountedAction instanceof Action)) {
            return null;
        }

        if ((! $this->isCachingSchemas) && $this->hasCachedSchema("mountedActionSchema{$actionNestingIndex}")) {
            return $this->getSchema("mountedActionSchema{$actionNestingIndex}");
        }

        return $mountedAction->getSchema(
            $this->makeSchema()
                ->model(function () use ($mountedAction): Model | array | string | null {
                    $schemaComponent = $mountedAction->getSchemaComponent();

                    return $mountedAction->getRecord(withDefault: blank($schemaComponent)) ?? $mountedAction->getModel(withDefault: blank($schemaComponent)) ?? $schemaComponent?->getActionSchemaModel() ?? $this->getMountedActionSchemaModel();
                })
                ->key("mountedActionSchema{$actionNestingIndex}")
                ->statePath("mountedActions.{$actionNestingIndex}.data")
                ->operation(
                    collect($this->mountedActions)
                        ->take($actionNestingIndex + 1)
                        ->pluck('name')
                        ->implode('.'),
                )
                ->rootHeadingLevel(3),
        );
    }

    /**
     * @deprecated Use `getMountedActionSchema()` instead.
     */
    protected function getMountedActionForm(?int $actionNestingIndex = null, ?Action $mountedAction = null): ?Schema
    {
        return $this->getMountedActionSchema($actionNestingIndex, $mountedAction);
    }

    /**
     * @return Model|class-string<Model>|null
     */
    protected function getMountedActionSchemaModel(): Model | string | null
    {
        return null;
    }

    public function unmountAction(bool $canCancelParentActions = true): void
    {
        try {
            $action = $this->getMountedAction();
        } catch (ActionNotResolvableException $exception) {
            $action = null;
        }

        if (! ($canCancelParentActions && $action)) {
            array_pop($this->mountedActions);
        } elseif ($action->shouldCancelAllParentActions()) {
            $this->mountedActions = [];
        } else {
            $parentActionToCancelTo = $action->getParentActionToCancelTo();

            while (true) {
                $recentlyClosedParentAction = array_pop($this->mountedActions);

                if (
                    blank($parentActionToCancelTo) ||
                    ($recentlyClosedParentAction['name'] === $parentActionToCancelTo)
                ) {
                    break;
                }
            }
        }

        $this->syncActionModals();

        while (count($this->cachedMountedActions ?? []) > count($this->mountedActions)) {
            array_pop($this->cachedMountedActions);
        }

        if (! count($this->mountedActions)) {
            $action?->clearRecordAfter();

            // Setting these to `null` creates a bug where the properties are
            // actually set to `'null'` strings and remain in the URL.
            $this->defaultAction = [];
            $this->defaultActionArguments = [];
            $this->defaultActionContext = [];

            return;
        }

        $this->resetErrorBag();
    }

    protected function syncActionModals(): void
    {
        $this->dispatch('sync-action-modals', id: $this->getId(), newActionNestingIndex: array_key_last($this->mountedActions));
    }

    public function getOriginallyMountedActionIndex(): ?int
    {
        return $this->originallyMountedActionIndex;
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function mergeMountedActionArguments(array $arguments): void
    {
        $this->mountedActions[array_key_last($this->mountedActions)]['arguments'] = array_merge(
            $this->mountedActions[array_key_last($this->mountedActions)]['arguments'],
            $arguments,
        );

        $this->getMountedAction()->mergeArguments($arguments);
    }

    public function getDefaultActionRecord(Action $action): ?Model
    {
        return null;
    }

    public function getDefaultActionRecordTitle(Action $action): ?string
    {
        return null;
    }

    /**
     * @return ?class-string<Model>
     */
    public function getDefaultActionModel(Action $action): ?string
    {
        return null;
    }

    public function getDefaultActionModelLabel(Action $action): ?string
    {
        return null;
    }

    public function getDefaultActionUrl(Action $action): ?string
    {
        return null;
    }

    public function getDefaultActionSuccessRedirectUrl(Action $action): ?string
    {
        return null;
    }

    public function getDefaultActionFailureRedirectUrl(Action $action): ?string
    {
        return null;
    }

    public function getDefaultActionRelationship(Action $action): ?Relation
    {
        return null;
    }

    public function getDefaultActionSchemaResolver(Action $action): ?Closure
    {
        return null;
    }

    public function getDefaultActionAuthorizationResponse(Action $action): ?Response
    {
        return null;
    }

    public function getDefaultActionIndividualRecordAuthorizationResponseResolver(Action $action): ?Closure
    {
        return null;
    }
}
