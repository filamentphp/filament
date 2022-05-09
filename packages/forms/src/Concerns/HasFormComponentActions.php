<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;

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

    protected function getHasFormComponentActionsForms(): array
    {
        if ($this->cachedForms === null) {
            return [];
        }

        if (! $this->hasMountedFormComponentAction()) {
            return [];
        }

        return [
            'mountedFormComponentActionForm' => $this->getMountedFormComponentActionForm(),
        ];
    }

    protected function getMountedFormComponentActionForm(): ComponentContainer
    {
        if (array_key_exists('mountedFormComponentActionForm', $this->cachedForms)) {
            return $this->cachedForms['mountedFormComponentActionForm'];
        }

        $component = $this->getMountedFormComponentActionComponent();

        return $this->makeForm()
            ->schema(($action = $this->getMountedFormComponentAction()) ? $action->getFormSchema() : [])
            ->model($component->getActionFormModel())
            ->statePath('mountedFormComponentActionData');
    }

    public function callMountedFormComponentAction()
    {
        $action = $this->getMountedFormComponentAction();

        if (! $action) {
            return;
        }

        $data = $this->getMountedFormComponentActionForm()->getState();

        try {
            return $action->call($data);
        } finally {
            $this->mountedFormComponentAction = null;

            $this->dispatchBrowserEvent('close-modal', [
                'id' => static::class . '-form-component-action',
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

        if ($action->isHidden()) {
            return;
        }

        $this->cachedForms['mountedFormComponentActionForm'] = $this->getMountedFormComponentActionForm();

        app()->call($action->getMountUsing(), [
            'action' => $action,
            'form' => $this->getMountedFormComponentActionForm(),
        ]);

        if (! $action->shouldOpenModal()) {
            return $this->callMountedFormComponentAction();
        }

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => static::class . '-form-component-action',
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
