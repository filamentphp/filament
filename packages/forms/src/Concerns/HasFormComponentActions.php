<?php

namespace Filament\Forms\Concerns;

use Closure;
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
     * @var array<string, mixed> | null
     */
    public ?array $mountedFormComponentActionArguments = [];

    /**
     * @var array<string, mixed> | null
     */
    public ?array $mountedFormComponentActionData = [];

    public ?string $mountedFormComponentActionComponent = null;

    protected function hasMountedFormComponentAction(): bool
    {
        return $this->mountedFormComponentActionComponent !== null;
    }

    protected function getMountedFormComponentActionForm(): ?Form
    {
        $action = $this->getMountedFormComponentAction();

        if (! ($action instanceof Action)) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedFormComponentActionForm')) {
            return $this->getCachedForm('mountedFormComponentActionForm');
        }

        return $action->getForm(
            $this->makeForm()
                ->model($this->getMountedFormComponentActionComponent()->getActionFormModel())
                ->statePath('mountedFormComponentActionData')
                ->operation($this->mountedFormComponentAction),
        );
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function callMountedFormComponentAction(array $arguments = []): mixed
    {
        $action = $this->getMountedFormComponentAction();

        if (! $action) {
            return null;
        }

        if ($action->isDisabled()) {
            return null;
        }

        $action->arguments(array_merge(
            $this->mountedFormComponentActionArguments ?? [],
            $arguments,
        ));

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
            return null;
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

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function mountFormComponentAction(string $component, string $name, array $arguments = []): mixed
    {
        $this->mountedFormComponentAction = $name;
        $this->mountedFormComponentActionArguments = $arguments;
        $this->mountedFormComponentActionComponent = $component;

        $action = $this->getMountedFormComponentAction();

        if (! $action) {
            return null;
        }

        if ($action instanceof Closure) {
            try {
                return $this->getMountedFormComponentActionComponent()->evaluate(
                    $action,
                    $this->mountedFormComponentActionArguments,
                );
            } finally {
                $this->mountedFormComponentActionComponent = null;
                $this->mountedFormComponentAction = null;
            }
        }

        if ($action->isDisabled()) {
            return null;
        }

        $action->arguments($this->mountedFormComponentActionArguments);

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
            return null;
        } catch (Cancel $exception) {
            $this->mountedFormComponentActionComponent = null;
            $this->mountedFormComponentAction = null;

            return null;
        }

        if (! $this->mountedFormComponentActionShouldOpenModal()) {
            return $this->callMountedFormComponentAction();
        }

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => "{$this->id}-form-component-action",
        ]);

        return null;
    }

    public function mountedFormComponentActionShouldOpenModal(): bool
    {
        $action = $this->getMountedFormComponentAction();

        if ($action->isModalHidden()) {
            return false;
        }

        return $action->getModalSubheading() ||
            $action->getModalContent() ||
            $action->getModalFooter() ||
            $action->getInfolist() ||
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
