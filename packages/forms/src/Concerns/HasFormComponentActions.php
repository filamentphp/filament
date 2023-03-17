<?php

namespace Filament\Forms\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;

/**
 * @property ComponentContainer $mountedFormComponentActionForm
 */
trait HasFormComponentActions
{
    public $mountedFormComponentAction = null;

    public $mountedFormComponentActionArguments = [];

    public $mountedFormComponentActionData = [];

    public $mountedFormComponentActionComponent = null;

    protected function hasMountedFormComponentAction(): bool
    {
        return $this->mountedFormComponentActionComponent !== null;
    }

    protected function getMountedFormComponentActionForm(): ?ComponentContainer
    {
        $action = $this->getMountedFormComponentAction();

        if (! ($action instanceof Action)) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedFormComponentActionForm')) {
            return $this->getCachedForm('mountedFormComponentActionForm');
        }

        return $this->makeForm()
            ->schema($action->getFormSchema())
            ->model($this->getMountedFormComponentActionComponent()->getActionFormModel())
            ->statePath('mountedFormComponentActionData')
            ->context($this->mountedFormComponentAction);
    }

    public function callMountedFormComponentAction(?string $arguments = null)
    {
        $action = $this->getMountedFormComponentAction();

        if (! $action) {
            return;
        }

        if ($action->isDisabled()) {
            return;
        }

        $action->arguments(array_merge(
            $this->mountedFormComponentActionArguments ?? [],
            $arguments ? json_decode($arguments, associative: true) : [],
        ));

        $form = $this->getMountedFormComponentActionForm();

        $result = null;

        try {
            if ($action->hasForm()) {
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
            return;
        } catch (Cancel $exception) {
        }

        if (filled($this->redirectTo)) {
            return $result;
        }

        $this->mountedFormComponentAction = null;

        $action->resetArguments();
        $action->resetFormData();

        $this->dispatchBrowserEvent('close-modal', [
            'id' => "{$this->id}-form-component-action",
        ]);

        return $result;
    }

    public function getMountedFormComponentAction(): Action | Closure | null
    {
        if (! $this->mountedFormComponentAction) {
            return null;
        }

        return $this->getMountedFormComponentActionComponent()?->getAction($this->mountedFormComponentAction);
    }

    public function mountFormComponentAction(string $component, string $name, array $arguments = [])
    {
        $this->mountedFormComponentActionComponent = $component;
        $this->mountedFormComponentAction = $name;
        $this->mountedFormComponentActionArguments = $arguments;

        $action = $this->getMountedFormComponentAction();

        if (! $action) {
            return;
        }

        if ($action instanceof Closure) {
            try {
                return $this->getMountedFormComponentActionComponent()->evaluate($action);
            } finally {
                $this->mountedFormComponentActionComponent = null;
                $this->mountedFormComponentAction = null;
            }
        }

        if ($action->isDisabled()) {
            return;
        }

        $action->arguments($this->mountedFormComponentActionArguments);

        $this->cacheForm(
            'mountedFormComponentActionForm',
            fn () => $this->getMountedFormComponentActionForm(),
        );

        try {
            if ($action->hasForm()) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedFormComponentActionForm(),
            ]);

            if ($action->hasForm()) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return;
        } catch (Cancel $exception) {
            $this->mountedFormComponentActionComponent = null;
            $this->mountedFormComponentAction = null;

            return;
        }

        if (! $action->shouldOpenModal()) {
            return $this->callMountedFormComponentAction();
        }

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => "{$this->id}-form-component-action",
        ]);
    }

    public function getMountedFormComponentActionComponent(): ?Component
    {
        if (! $this->hasMountedFormComponentAction()) {
            return null;
        }

        foreach ($this->getCachedForms() as $form) {
            $component = $form->getComponent($this->mountedFormComponentActionComponent);

            if (! $component) {
                continue;
            }

            return $component;
        }

        return null;
    }

    protected function getFormComponentActions(): array
    {
        return [];
    }
}
