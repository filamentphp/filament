<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Livewire\Attributes\Url;
use Throwable;

use function Livewire\store;

/**
 * @property Form $mountedActionForm
 */
trait InteractsWithActions
{
    /**
     * @var array<string> | null
     */
    public ?array $mountedActions = [];

    /**
     * @var array<string, array<string, mixed>> | null
     */
    public ?array $mountedActionsArguments = [];

    /**
     * @var array<string, array<string, mixed>> | null
     */
    public ?array $mountedActionsData = [];

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

    protected bool $hasActionsModalRendered = false;

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

        $form = $this->getMountedActionForm(mountedAction: $action);

        $result = null;

        $originallyMountedActions = $this->mountedActions;

        try {
            $action->beginDatabaseTransaction();

            if ($this->mountedActionHasForm(mountedAction: $action)) {
                $action->callBeforeFormValidated();

                $form->getState(afterValidate: function (array $state) use ($action) {
                    $action->callAfterFormValidated();

                    $action->formData($state);

                    $action->callBefore();
                });
            } else {
                $action->callBefore();
            }

            $result = $action->call([
                'form' => $form,
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
     */
    public function mountAction(string $name, array $arguments = []): mixed
    {
        $this->mountedActions[] = $name;
        $this->mountedActionsArguments[] = $arguments;
        $this->mountedActionsData[] = [];

        $action = $this->getMountedAction();

        if (! $action) {
            $this->unmountAction();

            return null;
        }

        if ($action->isDisabled()) {
            $this->unmountAction();

            return null;
        }

        $this->cacheMountedActionForm(mountedAction: $action);

        try {
            $hasForm = $this->mountedActionHasForm(mountedAction: $action);

            if ($hasForm) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedActionForm(mountedAction: $action),
            ]);

            if ($hasForm) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return null;
        } catch (Cancel $exception) {
            $this->unmountAction(shouldCancelParentActions: false);

            return null;
        }

        if (! $this->mountedActionShouldOpenModal(mountedAction: $action)) {
            return $this->callMountedAction();
        }

        $this->resetErrorBag();

        $this->openActionModal();

        return null;
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function replaceMountedAction(string $name, array $arguments = []): void
    {
        $this->resetMountedActionProperties();
        $this->mountAction($name, $arguments);
    }

    public function mountedActionShouldOpenModal(?Action $mountedAction = null): bool
    {
        return ($mountedAction ?? $this->getMountedAction())->shouldOpenModal(
            checkForFormUsing: $this->mountedActionHasForm(...),
        );
    }

    public function mountedActionHasForm(?Action $mountedAction = null): bool
    {
        return (bool) count($this->getMountedActionForm(mountedAction: $mountedAction)?->getComponents() ?? []);
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

    protected function configureAction(Action $action): void {}

    public function getMountedAction(): ?Action
    {
        if (! count($this->mountedActions ?? [])) {
            return null;
        }

        return $this->getAction($this->mountedActions);
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getInteractsWithActionsForms(): array
    {
        return [
            'mountedActionForm' => $this->getMountedActionForm(),
        ];
    }

    public function getMountedActionForm(?Action $mountedAction = null): ?Form
    {
        $mountedAction ??= $this->getMountedAction();

        if (! $mountedAction) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedActionForm')) {
            return $this->getForm('mountedActionForm');
        }

        return $mountedAction->getForm(
            $this->makeForm()
                ->statePath('mountedActionsData.' . array_key_last($this->mountedActionsData))
                ->model($mountedAction->getRecord() ?? $mountedAction->getModel() ?? $this->getMountedActionFormModel())
                ->operation(implode('.', $this->mountedActions)),
        );
    }

    protected function getMountedActionFormModel(): Model | string | null
    {
        return null;
    }

    /**
     * @param  string | array<string>  $name
     */
    public function getAction(string | array $name): ?Action
    {
        if (is_string($name) && str($name)->contains('.')) {
            $name = explode('.', $name);
        }

        if (is_array($name)) {
            $firstName = array_shift($name);
            $modalActionNames = $name;

            $name = $firstName;
        }

        if ($action = $this->cachedActions[$name] ?? null) {
            return $this->getMountableModalActionFromAction(
                $action,
                modalActionNames: $modalActionNames ?? [],
            );
        }

        if (
            (! str($name)->endsWith('Action')) &&
            method_exists($this, "{$name}Action")
        ) {
            $methodName = "{$name}Action";
        } elseif (method_exists($this, $name)) {
            $methodName = $name;
        } else {
            return null;
        }

        $action = Action::configureUsing(
            Closure::fromCallable([$this, 'configureAction']),
            fn () => $this->{$methodName}(),
        );

        if (! $action instanceof Action) {
            throw new InvalidArgumentException('Actions must be an instance of ' . Action::class . ". The [{$methodName}] method on the Livewire component returned an instance of [" . get_class($action) . '].');
        }

        return $this->getMountableModalActionFromAction(
            $this->cacheAction($action),
            modalActionNames: $modalActionNames ?? [],
        );
    }

    /**
     * @param  array<string>  $modalActionNames
     */
    protected function getMountableModalActionFromAction(Action $action, array $modalActionNames): ?Action
    {
        $arguments = $this->mountedActionsArguments;

        if (
            (($actionArguments = array_shift($arguments)) !== null) &&
            (! $action->hasArguments())
        ) {
            $action->arguments($actionArguments);
        }

        foreach ($modalActionNames as $modalActionName) {
            $action = $action->getMountableModalAction($modalActionName);

            if (! $action) {
                return null;
            }

            if (
                (($actionArguments = array_shift($arguments)) !== null) &&
                (! $action->hasArguments())
            ) {
                $action->arguments($actionArguments);
            }
        }

        if (! $action instanceof Action) {
            return null;
        }

        return $action;
    }

    protected function popMountedAction(): ?string
    {
        try {
            return array_pop($this->mountedActions);
        } finally {
            array_pop($this->mountedActionsArguments);
            array_pop($this->mountedActionsData);
        }
    }

    protected function resetMountedActionProperties(): void
    {
        $this->mountedActions = [];
        $this->mountedActionsArguments = [];
        $this->mountedActionsData = [];
    }

    public function unmountAction(bool $shouldCancelParentActions = true): void
    {
        $action = $this->getMountedAction();

        if (! ($shouldCancelParentActions && $action)) {
            $this->popMountedAction();
        } elseif ($action->shouldCancelAllParentActions()) {
            $this->resetMountedActionProperties();
        } else {
            $parentActionToCancelTo = $action->getParentActionToCancelTo();

            while (true) {
                $recentlyClosedParentAction = $this->popMountedAction();

                if (
                    blank($parentActionToCancelTo) ||
                    ($recentlyClosedParentAction === $parentActionToCancelTo)
                ) {
                    break;
                }
            }
        }

        if (! count($this->mountedActions)) {
            $this->closeActionModal();

            $action?->clearRecordAfter();

            // Setting these to `null` creates a bug where the properties are
            // actually set to `'null'` strings and remain in the URL.
            $this->defaultAction = [];
            $this->defaultActionArguments = [];

            return;
        }

        $this->cacheMountedActionForm();

        $this->resetErrorBag();

        $this->openActionModal();
    }

    protected function cacheMountedActionForm(?Action $mountedAction = null): void
    {
        $this->cacheForm(
            'mountedActionForm',
            fn () => $this->getMountedActionForm($mountedAction),
        );
    }

    protected function closeActionModal(): void
    {
        $this->dispatch('close-modal', id: "{$this->getId()}-action");
    }

    protected function openActionModal(): void
    {
        $this->dispatch('open-modal', id: "{$this->getId()}-action");
    }

    public function getActiveActionsLocale(): ?string
    {
        return null;
    }

    public function mountedActionInfolist(): Infolist
    {
        return $this->getMountedAction()->getInfolist();
    }
}
