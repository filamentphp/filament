<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\StaticAction;
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
    public ?array $mountedActionArguments = [];

    /**
     * @var array<string, array<<string, mixed>> | null
     */
    public ?array $mountedActionData = [];

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
            Arr::last($this->mountedActionArguments),
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
        $this->mountedActionArguments[] = $arguments;

        $action = $this->getMountedAction();

        if (! $action) {
            return null;
        }

        if ($action->isDisabled()) {
            return null;
        }

        $action->arguments(Arr::last($this->mountedActionArguments));

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
                ->statePath("mountedActionData.{$action->getName()}")
                ->model($action->getModel() ?? $this->getMountedActionFormModel())
                ->operation(implode('.', $this->mountedActions)),
        );
    }

    protected function getMountedActionFormModel(): Model | string | null
    {
        return null;
    }

    public function getAction(string | array $name): ?Action
    {
        if (is_array($name)) {
            $firstName = array_shift($name);
            $modalActionNames = $name;

            $name = $firstName;
        }

        $action = $this->cachedActions[$name] ?? null;

        if ($action) {
            return $this->getModalActionFromAction(
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

        return $this->getModalActionFromAction(
            $this->cacheAction($action, name: $name),
            modalActionNames: $modalActionNames ?? [],
            parentActionName: $name,
        );
    }

    public function getModalActionFromAction(Action $action, array $modalActionNames, string $parentActionName): ?Action
    {
        foreach ($modalActionNames as $modalActionName) {
            $action = $action->getModalAction($modalActionName);

            if (! $action) {
                throw new InvalidArgumentException("The [{$modalActionName}] action has not been registered on the [{$parentActionName}] action.");
            }

            $parentActionName = $modalActionName;
        }

        return $action;
    }

    public function unmountAction(bool $shouldCloseParentActions = true): void
    {
        if (
            $shouldCloseParentActions &&
            ($action = $this->getMountedAction())
        ) {
            if ($action->shouldCloseAllParentActions()) {
                $this->mountedActions = [];
                $this->mountedActionArguments = [];
                $this->mountedActionData = [];
            } else {
                $parentActionToCloseTo = $action->getParentActionToCloseTo();

                while (true) {
                    $recentlyClosedParentAction = array_pop($this->mountedActions);
                    array_pop($this->mountedActionArguments);
                    array_pop($this->mountedActionData);

                    if (
                        blank($parentActionToCloseTo) ||
                        ($recentlyClosedParentAction === $parentActionToCloseTo)
                    ) {
                        break;
                    }
                }
            }
        } else {
            array_pop($this->mountedActions);
            array_pop($this->mountedActionArguments);
            array_pop($this->mountedActionData);
        }

        if (! count($this->mountedActions)) {
            $this->dispatchBrowserEvent('close-modal', [
                'id' => "{$this->id}-action",
            ]);

            return;
        }

        $this->cacheForm(
            'mountedActionForm',
            fn () => $this->getMountedActionForm(),
        );

        $this->dispatchBrowserEvent('open-modal', [
            'id' => "{$this->id}-action",
        ]);
    }
}
