<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Livewire\Exceptions\PropertyNotFoundException;

/**
 * @property Forms\Form $mountedActionForm
 */
trait InteractsWithActions
{
    use Forms\Concerns\InteractsWithForms {
        __get as __getForm;
    }

    public ?string $mountedAction = null;

    /**
     * @var array<string, mixed> | null
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

    public function callMountedAction(?string $arguments = null): mixed
    {
        $action = $this->getMountedAction();

        if (! $action) {
            return null;
        }

        if ($action->isDisabled()) {
            return null;
        }

        $action->arguments($arguments ? json_decode($arguments, associative: true) : []);

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

        $this->mountedAction = null;

        $action->resetArguments();
        $action->resetFormData();

        $this->dispatchBrowserEvent('close-modal', [
            'id' => 'page-action',
        ]);

        return $result;
    }

    public function mountAction(string $name): mixed
    {
        $this->mountedAction = $name;

        $action = $this->getMountedAction();

        if (! $action) {
            return null;
        }

        if ($action->isDisabled()) {
            return null;
        }

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
            $this->mountedAction = null;

            return null;
        }

        if (! $this->mountedActionShouldOpenModal()) {
            return $this->callMountedAction();
        }

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => 'page-action',
        ]);

        return null;
    }

    public function mountedActionShouldOpenModal(): bool
    {
        $action = $this->getMountedAction();

        return $action->getModalSubheading() ||
            $action->getModalContent() ||
            $action->getModalFooter() ||
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
        if (! $this->mountedAction) {
            return null;
        }

        return $this->getAction($this->mountedAction);
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
                ->statePath('mountedActionData')
                ->model($action->getModel() ?? $this->getMountedActionFormModel())
                ->context($this->mountedAction),
        );
    }

    protected function getMountedActionFormModel(): Model | string | null
    {
        return null;
    }

    public function getAction(string $name): ?Action
    {
        $action = $this->cachedActions[$name] ?? null;

        if ($action) {
            return $action;
        }

        if (method_exists($this, $name)) {
            $action = $this->cacheAction(Action::configureUsing(
                Closure::fromCallable([$this, 'configureAction']),
                fn () => $this->{$name}(),
            ), name: $name);
        }

        if ($action instanceof Action) {
            return $action;
        }

        return null;
    }
}
