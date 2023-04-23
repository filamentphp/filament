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
use Livewire\Exceptions\PropertyNotFoundException;

/**
 * @property Forms\Form $mountedActionForm
 */
trait InteractsWithActions
{
    use Forms\Concerns\InteractsWithForms {
        __get as __getForm;
    }

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
     * @param  string  $property
     */
    public function __get($property): mixed
    {
        try {
            return $this->__getForm($property);
        } catch (PropertyNotFoundException $exception) {
            if ($action = $this->getAction($property)) {
                return $action;
            }

            throw $exception;
        }
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

        $action->arguments(array_merge(
            Arr::last($this->mountedActionsArguments),
            $arguments,
        ));

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
        } catch (Halt $exception) {
            return null;
        } catch (Cancel $exception) {
        }

        $action->resetArguments();
        $action->resetFormData();

        if (filled($this->redirectTo)) {
            return $result;
        }

        $this->unmountAction();

        return $result;
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function mountAction(string $name, array $arguments = []): mixed
    {
        $this->mountedActions[] = $name;
        $this->mountedActionsArguments[$name] = $arguments;
        $this->mountedActionsData[$name] = [];

        $action = $this->getMountedAction();

        if (! $action) {
            return null;
        }

        if ($action->isDisabled()) {
            return null;
        }

        $action->arguments(Arr::last($this->mountedActionsArguments));

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

        $this->dispatchBrowserEvent('open-modal', [
            'id' => "{$this->id}-action",
        ]);

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

    public function cacheAction(Action $action, ?string $name = null): Action
    {
        $action->livewire($this);

        if (filled($name)) {
            $action->name($name);
        }

        return $this->cachedActions[$action->getName()] = $action;
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
            return $this->getCachedForm('mountedActionForm');
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

        $action = $this->cachedActions[$name] ?? null;

        if ($action) {
            return $this->getMountableModalActionFromAction(
                $action,
                modalActionNames: $modalActionNames ?? [],
                parentActionName: $name,
            );
        }

        if (! method_exists($this, $name)) {
            return null;
        }

        $action = Action::configureUsing(
            Closure::fromCallable([$this, 'configureAction']),
            fn () => $this->{$name}(),
        );

        if (! $action instanceof Action) {
            throw new InvalidArgumentException('Actions must be an instance of ' . Action::class . ". The [{$name}] method on the Livewire component returned an instance of [" . get_class($action) . '].');
        }

        return $this->getMountableModalActionFromAction(
            $this->cacheAction($action, name: $name),
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
                throw new InvalidArgumentException("The [{$modalActionName}] action has not been registered on the [{$parentActionName}] action.");
            }

            $parentActionName = $modalActionName;
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
            $this->dispatchBrowserEvent('close-modal', [
                'id' => "{$this->id}-action",
            ]);

            $action?->record(null);

            return;
        }

        $this->cacheForm(
            'mountedActionForm',
            fn () => $this->getMountedActionForm(),
        );

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => "{$this->id}-action",
        ]);
    }
}
