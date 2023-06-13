<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use function Livewire\store;

/**
 * @property Forms\Form $mountedActionForm
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

        $action->arguments([
            ...Arr::last($this->mountedActionsArguments),
            ...$arguments,
        ]);

        $form = $this->getMountedActionForm();

        $result = null;

        try {
            if ($this->mountedActionHasForm()) {
                $action->callBeforeFormValidated();

                $action->formData($form->getState());

                $action->callAfterFormValidated();
            }

            $action->callBefore();

            $result = $action->call([
                'form' => $form,
            ]);

            $result = $action->callAfter() ?? $result;

            $this->afterActionCalled();
        } catch (Halt $exception) {
            return null;
        } catch (Cancel $exception) {
        }

        $action->resetArguments();
        $action->resetFormData();

        if (store($this)->has('redirect')) {
            return $result;
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

        $action->arguments($arguments);

        $this->cacheForm(
            'mountedActionForm',
            fn () => $this->getMountedActionForm(),
        );

        try {
            $hasForm = $this->mountedActionHasForm();

            if ($hasForm) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedActionForm(),
            ]);

            if ($hasForm) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return null;
        } catch (Cancel $exception) {
            $this->unmountAction(shouldCloseParentActions: false);

            return null;
        }

        if (! $this->mountedActionShouldOpenModal()) {
            return $this->callMountedAction();
        }

        $this->resetErrorBag();

        $this->dispatch('open-modal', id: "{$this->getId()}-action");

        return null;
    }

    public function mountedActionShouldOpenModal(): bool
    {
        $action = $this->getMountedAction();

        if ($action->isModalHidden()) {
            return false;
        }

        return $action->getModalSubheading() ||
            $action->getModalContent() ||
            $action->getModalFooter() ||
            $action->getInfolist() ||
            $this->mountedActionHasForm();
    }

    public function mountedActionHasForm(): bool
    {
        return (bool) count($this->getMountedActionForm()?->getComponents() ?? []);
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

    protected function configureAction(Action $action): void
    {
    }

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

    public function getMountedActionForm(): ?Forms\Form
    {
        $action = $this->getMountedAction();

        if (! $action) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedActionForm')) {
            return $this->getForm('mountedActionForm');
        }

        return $action->getForm(
            $this->makeForm()
                ->statePath('mountedActionsData.' . array_key_last($this->mountedActionsData))
                ->model($action->getModel() ?? $this->getMountedActionFormModel())
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
                parentActionName: $name,
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
            parentActionName: $name,
        );
    }

    /**
     * @param  array<string>  $modalActionNames
     */
    protected function getMountableModalActionFromAction(Action $action, array $modalActionNames, string $parentActionName): ?Action
    {
        foreach ($modalActionNames as $modalActionName) {
            $action = $action->getMountableModalAction($modalActionName);

            if (! $action) {
                return null;
            }

            $parentActionName = $modalActionName;
        }

        if (! $action instanceof Action) {
            return null;
        }

        return $action;
    }

    public function unmountAction(bool $shouldCloseParentActions = true): void
    {
        $action = $this->getMountedAction();

        if (! ($shouldCloseParentActions && $action)) {
            array_pop($this->mountedActions);
            array_pop($this->mountedActionsArguments);
            array_pop($this->mountedActionsData);
        } elseif ($action->shouldCloseAllParentActions()) {
            $this->mountedActions = [];
            $this->mountedActionsArguments = [];
            $this->mountedActionsData = [];
        } else {
            $parentActionToCloseTo = $action->getParentActionToCloseTo();

            while (true) {
                $recentlyClosedParentAction = array_pop($this->mountedActions);
                array_pop($this->mountedActionsArguments);
                array_pop($this->mountedActionsData);

                if (
                    blank($parentActionToCloseTo) ||
                    ($recentlyClosedParentAction === $parentActionToCloseTo)
                ) {
                    break;
                }
            }
        }

        if (! count($this->mountedActions)) {
            $this->dispatch('close-modal', id: "{$this->getId()}-action");

            if ($action?->shouldClearRecordAfter()) {
                $action->record(null);
            }

            return;
        }

        $this->cacheForm(
            'mountedActionForm',
            fn () => $this->getMountedActionForm(),
        );

        $this->resetErrorBag();

        $this->dispatch('open-modal', id: "{$this->getId()}-action");
    }
}
