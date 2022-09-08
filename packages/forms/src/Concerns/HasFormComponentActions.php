<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Support\Actions\Exceptions\Hold;

/**
 * @property ComponentContainer $mountedFormComponentActionForm
 */
trait HasFormComponentActions
{
    public $mountedFormComponentAction = null;

    public $mountedFormComponentActionData = [];

    public $mountedFormComponentActionComponent = null;

    protected function hasMountedFormComponentAction(): bool
    {
        return $this->mountedFormComponentActionComponent !== null;
    }

    protected function getMountedFormComponentActionForm(): ?ComponentContainer
    {
        $action = $this->getMountedFormComponentAction();

        if (! $action) {
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

        $action->arguments($arguments ? json_decode($arguments, associative: true) : []);

        $form = $this->getMountedFormComponentActionForm();

        if ($action->hasForm()) {
            $action->callBeforeFormValidated();

            $action->formData($form->getState());

            $action->callAfterFormValidated();
        }

        $action->callBefore();

        try {
            $result = $action->call([
                'form' => $form,
            ]);
        } catch (Hold $exception) {
            return;
        }

        try {
            return $action->callAfter() ?? $result;
        } finally {
            $this->mountedFormComponentAction = null;

            $action->resetArguments();
            $action->resetFormData();

            $this->dispatchBrowserEvent('close-modal', [
                'id' => "{$this->id}-form-component-action",
            ]);
        }
    }

    public function getMountedFormComponentAction(): ?Action
    {
        if (! $this->mountedFormComponentAction) {
            return null;
        }

        return $this->getMountedFormComponentActionComponent()?->getAction($this->mountedFormComponentAction);
    }

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

        if ($action->hasForm()) {
            $action->callBeforeFormFilled();
        }

        $action->mount([
            'form' => $this->getMountedFormComponentActionForm(),
        ]);

        if ($action->hasForm()) {
            $action->callAfterFormFilled();
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
