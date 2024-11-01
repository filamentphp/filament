<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\Exceptions\ActionNotResolvableException;
use Filament\Schema\Components\Contracts\ExposesStateToActionData;
use Filament\Schema\Concerns\InteractsWithSchemas;
use Filament\Schema\Contracts\HasSchemas;
use Filament\Schema\Schema;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Throwable;

use function Livewire\store;

trait InteractsWithActions
{
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

        $this->syncActionModals();

        if (($actionComponent = $action->getSchemaComponent()) instanceof ExposesStateToActionData) {
            foreach ($actionComponent->getChildComponentContainers() as $actionComponentChildComponentContainer) {
                $actionComponentChildComponentContainer->validate();
            }
        }

        try {
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

        if ($action->isDisabled()) {
            return null;
        }

        $action->mergeArguments($arguments);

        $schema = $this->getMountedActionSchema(mountedAction: $action);

        $originallyMountedActions = $this->mountedActions;

        $result = null;

        try {
            $action->beginDatabaseTransaction();

            $schemaState = [];

            if (($actionComponent = $action->getSchemaComponent()) instanceof ExposesStateToActionData) {
                foreach ($actionComponent->getChildComponentContainers() as $actionComponentChildComponentContainer) {
                    $schemaState = [
                        ...$schemaState,
                        ...$actionComponentChildComponentContainer->getState(),
                    ];
                }
            }

            if ($this->mountedActionHasSchema(mountedAction: $action)) {
                $action->callBeforeFormValidated();

                $schema->getState(afterValidate: function (array $state) use ($action, $schemaState) {
                    $action->callAfterFormValidated();

                    $action->formData([
                        ...$schemaState,
                        ...$state,
                    ]);

                    $action->callBefore();
                });
            } else {
                $action->formData($schemaState);

                $action->callBefore();
            }

            $result = $action->call([
                'form' => $schema,
                'schema' => $schema,
            ]);

            $result = $action->callAfter() ?? $result;

            $this->afterActionCalled();

            $action->commitDatabaseTransaction();
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
                $action->resetFormData();

                $this->unmountAction();
            }

            throw $exception;
        } catch (Throwable $exception) {
            $action->rollBackDatabaseTransaction();

            throw $exception;
        }

        if (store($this)->has('redirect')) {
            return $result;
        }

        $action->resetArguments();
        $action->resetFormData();

        // If the action was replaced while it was being called,
        // we don't want to unmount it.
        if ($originallyMountedActions !== $this->mountedActions) {
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

            $resolvedAction->nestingIndex($actionNestingIndex);

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
    protected function resolveAction(array $action, array $parentActions): Action
    {
        if (count($parentActions)) {
            $parentAction = Arr::last($parentActions);
            $resolvedAction = $parentAction->getMountableModalAction($action['name']);

            if (! $resolvedAction) {
                throw new ActionNotResolvableException("Action [{$action['name']}] was not found for action [{$parentAction->getName()}].");
            }
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
                throw new ActionNotResolvableException("Action was not resolvable from methods [{$action['name']}Action] or [{$action['name']}]");
            }

            $resolvedAction = Action::configureUsing(
                Closure::fromCallable([$this, 'configureAction']),
                fn () => $this->{$methodName}(),
            );

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
            throw new ActionNotResolvableException('Failed to resolve table action for Livewire component without the ' . HasTable::class . ' trait.');
        }

        $resolvedAction = null;

        if ($action['context']['bulk'] ?? false) {
            $resolvedAction = $this->getTable()->getBulkAction($action['name']);
        }

        $resolvedAction ??= $this->getTable()->getAction($action['name']) ?? throw new ActionNotResolvableException("Action [{$action['name']}] not found on table.");

        if (filled($action['context']['recordKey'] ?? null)) {
            $record = $this->getTableRecord($action['context']['recordKey']);

            $resolvedAction->record($record);
            $resolvedAction->getRootGroup()?->record($record);
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
            throw new ActionNotResolvableException('Failed to resolve action schema component for Livewire component without the ' . InteractsWithSchemas::class . ' trait.');
        }

        $component = $this->getSchemaComponent($action['context']['schemaComponent']);

        if (! $component) {
            throw new ActionNotResolvableException("Schema component [{$action['context']['schemaComponent']}] not found.");
        }

        $componentAction = $component->getAction($action['name']);

        if (! $componentAction) {
            throw new ActionNotResolvableException("Action [{$action['name']}] not found on schema component [{$action['context']['schemaComponent']}].");
        }

        return $componentAction;
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

    /**
     * @param  array<string>  $modalActionNames
     */
    protected function getMountableModalActionFromAction(Action $action, array $modalActionNames): ?Action
    {
        $mountedActions = $this->mountedActions;

        foreach ($modalActionNames as $modalActionName) {
            $action = $action->getMountableModalAction($modalActionName);

            if (! $action) {
                return null;
            }
        }

        if (! $action instanceof Action) {
            return null;
        }

        return $action;
    }

    protected function getMountedActionSchema(?int $actionNestingIndex = null, ?Action $mountedAction = null): ?Schema
    {
        $actionNestingIndex ??= array_key_last($this->mountedActions);

        $mountedAction ??= $this->getMountedAction($actionNestingIndex);

        if (! ($mountedAction instanceof Action)) {
            return null;
        }

        if ((! $this->isCachingSchemas) && $this->hasCachedSchema("mountedActionSchema{$actionNestingIndex}")) {
            return $this->getSchema("mountedActionSchema{$actionNestingIndex}");
        }

        return $mountedAction->getSchema(
            $this->makeSchema()
                ->model($mountedAction->getRecord() ?? $mountedAction->getModel() ?? $mountedAction->getSchemaComponent()?->getActionFormModel() ?? $this->getMountedActionSchemaModel())
                ->key("mountedActionSchema{$actionNestingIndex}")
                ->statePath("mountedActions.{$actionNestingIndex}.data")
                ->operation(
                    collect($this->mountedActions)
                        ->take($actionNestingIndex + 1)
                        ->pluck('name')
                        ->implode('.'),
                ),
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

    protected function configureAction(Action $action): void {}

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
            $action->callBeforeClose();

            $this->closeActionModal();

            $action->callAfterClose();

            $action?->clearRecordAfter();

            // Setting these to `null` creates a bug where the properties are
            // actually set to `'null'` strings and remain in the URL.
            $this->defaultAction = [];
            $this->defaultActionArguments = [];

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
}
