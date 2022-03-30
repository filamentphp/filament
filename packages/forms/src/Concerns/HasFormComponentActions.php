<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Illuminate\Database\Eloquent\Model;

/**
 * @property ComponentContainer $mountedFormComponentActionForm
 */
trait HasFormComponentActions
{
    public $mountedFormComponentAction = null;

    public $mountedFormComponentActionData = [];

    public $mountedFormComponentActionComponent = null;

    public function getHasFormComponentActionsForms(): array
    {
        $component = $this->getMountedFormComponentActionComponent();

        return [
            'mountedFormComponentActionForm' => $this->makeForm()
                ->schema(($action = $this->getMountedFormComponentAction()) ? $action->getFormSchema() : [])
                ->model($component->getRecord() ?? $component->getModel())
                ->statePath('mountedFormComponentActionData'),
        ];
    }

    public function callMountedFormComponentAction()
    {
        $component = $this->getMountedFormComponentActionComponent();

        if (! $component) {
            return;
        }

        if (! $component->hasAction($this->mountedFormComponentAction)) {
            return;
        }

        $data = $this->getMountedFormComponentActionForm()->getState();

        try {
            return $this->getMountedFormComponentAction()->call($data);
        } finally {
            $this->dispatchBrowserEvent('close-modal', [
                'id' => 'form-component-action',
            ]);
        }
    }

    public function getMountedFormComponentAction(): ?Action
    {
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

        $this->cacheForm('mountedFormComponentActionForm');

        app()->call($action->getMountUsing(), [
            'action' => $action,
            'form' => $this->getMountedFormComponentActionForm(),
        ]);

        if (! $action->shouldOpenModal()) {
            return $this->callMountedFormComponentAction();
        }

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => 'form-component-action',
        ]);
    }

    public function getMountedFormComponentActionComponent(): ?Component
    {
        foreach ($this->getCachedForms() as $form) {
            $component = $form->getComponent($this->mountedFormComponentActionComponent);

            if (! $component) {
                continue;
            }

            return $component;
        }

        return null;
    }

    public function getMountedFormComponentActionForm(): ComponentContainer
    {
        return $this->mountedFormComponentActionForm;
    }

    protected function getFormComponentActions(): array
    {
        return [];
    }
}
