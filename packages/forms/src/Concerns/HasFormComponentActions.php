<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;

/**
 * @property ComponentContainer $mountedFormComponentActionForm
 */
trait HasFormComponentActions
{
    public ?string $mountedFormComponentAction = null;

    /**
     * @var array<string, mixed>
     */
    public array $mountedFormComponentActionData = [];

    public ?string $mountedFormComponentActionComponent = null;

    protected function hasMountedFormComponentAction(): bool
    {
        return $this->mountedFormComponentActionComponent !== null;
    }

    protected function getMountedFormComponentActionForm(): ?Form
    {
        $action = $this->getMountedFormComponentAction();

        if (! $action) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedFormComponentActionForm')) {
            return $this->getCachedForm('mountedFormComponentActionForm');
        }

        return $action->getForm(
            $this->makeForm()
                ->model($this->getMountedFormComponentActionComponent()->getActionFormModel())
                ->statePath('mountedFormComponentActionData')
                ->context($this->mountedFormComponentAction),
        );
    }

    /**
     * @return mixed
     */
    public function callMountedFormComponentAction(?string $arguments = null)
    {
        $action = $this->getMountedFormComponentAction();

        if (! $action) {
            return;
        }

        if ($action->isDisabled()) {
            return;
        }

        $action->arguments($arguments ? json_decode($arguments, associative: true) : []);

        $form = $this->getMountedFormComponentActionForm();

        $result = null;

        try {
            if ($this->mountedFormComponentActionHasForm()) {
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

        $this->mountedFormComponentAction = null;

        $action->resetArguments();
        $action->resetFormData();

        $this->dispatchBrowserEvent('close-modal', [
            'id' => "{$this->id}-form-component-action",
        ]);

        return $result;
    }

    public function getMountedFormComponentAction(): ?Action
    {
        if (! $this->mountedFormComponentAction) {
            return null;
        }

        return $this->getMountedFormComponentActionComponent()?->getAction($this->mountedFormComponentAction);
    }

    /**
     * @return mixed
     */
    public function mountFormComponentAction(string $component, string $name)
    {
        $this->mountedFormComponentActionComponent = $component;
        $this->mountedFormComponentAction = $name;

        $action = $this->getMountedFormComponentAction();

        if (! $action) {
            return;
        }

        if ($action->isDisabled()) {
            return;
        }

        $this->cacheForm(
            'mountedFormComponentActionForm',
            fn () => $this->getMountedFormComponentActionForm(),
        );

        try {
            $hasForm = $this->mountedFormComponentActionHasForm();

            if ($hasForm) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedFormComponentActionForm(),
            ]);

            if ($hasForm) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return;
        } catch (Cancel $exception) {
            $this->mountedFormComponentActionComponent = null;
            $this->mountedFormComponentAction = null;

            return;
        }

        if (! $this->mountedFormComponentActionShouldOpenModal()) {
            return $this->callMountedFormComponentAction();
        }

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => "{$this->id}-form-component-action",
        ]);
    }

    public function mountedFormComponentActionShouldOpenModal(): bool
    {
        $action = $this->getMountedFormComponentAction();

        return $action->getModalSubheading() ||
            $action->getModalContent() ||
            $action->getModalFooter() ||
            $this->mountedFormComponentActionHasForm();
    }

    public function mountedFormComponentActionHasForm(): bool
    {
        return (bool) count($this->getMountedFormComponentActionForm()?->getComponents() ?? []);
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
}
