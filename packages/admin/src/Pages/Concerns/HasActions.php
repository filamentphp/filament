<?php

namespace Filament\Pages\Concerns;

use Closure;
use Filament\Forms;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\ActionGroup;
use Filament\Pages\Contracts;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Forms\ComponentContainer $mountedActionForm
 */
trait HasActions
{
    use Forms\Concerns\InteractsWithForms;

    public $mountedAction = null;

    public $mountedActionData = [];

    protected ?array $cachedActions = null;

    public function callMountedAction(?string $arguments = null)
    {
        $action = $this->getMountedAction();

        if (! $action) {
            return;
        }

        if ($action->isDisabled()) {
            return;
        }

        $action->arguments($arguments ? json_decode($arguments, associative: true) : []);

        $form = $this->getMountedActionForm();

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

        $this->mountedAction = null;

        $action->resetArguments();
        $action->resetFormData();

        $this->dispatchBrowserEvent('close-modal', [
            'id' => 'page-action',
        ]);

        return $result;
    }

    public function mountAction(string $name)
    {
        $this->mountedAction = $name;

        $action = $this->getMountedAction();

        if (! $action) {
            return;
        }

        if ($action->isDisabled()) {
            return;
        }

        $this->cacheForm(
            'mountedActionForm',
            fn () => $this->getMountedActionForm(),
        );

        try {
            if ($action->hasForm()) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedActionForm(),
            ]);

            if ($action->hasForm()) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return;
        } catch (Cancel $exception) {
            $this->mountedAction = null;

            return;
        }

        if (! $action->shouldOpenModal()) {
            return $this->callMountedAction();
        }

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => 'page-action',
        ]);
    }

    public function getCachedActions(): array
    {
        if ($this->cachedActions === null) {
            $this->cacheActions();
        }

        return $this->cachedActions;
    }

    protected function cacheActions(): void
    {
        $actions = Action::configureUsing(
            Closure::fromCallable([$this, 'configureAction']),
            fn (): array => $this->getActions(),
        );

        $this->cachedActions = [];

        foreach ($actions as $index => $action) {
            if ($action instanceof ActionGroup) {
                foreach ($action->getActions() as $groupedAction) {
                    $groupedAction->livewire($this);
                }

                $this->cachedActions[$index] = $action;

                continue;
            }

            $action->livewire($this);

            $this->cachedActions[$action->getName()] = $action;
        }
    }

    protected function configureAction(Action $action): void
    {
    }

    public function getMountedAction(): ?Action
    {
        if (! $this->mountedAction) {
            return null;
        }

        $action = $this->getCachedAction($this->mountedAction);

        if ($action) {
            return $action;
        }

        if (! $this instanceof Contracts\HasFormActions) {
            return null;
        }

        return $this->getCachedFormAction($this->mountedAction);
    }

    protected function getHasActionsForms(): array
    {
        return [
            'mountedActionForm' => $this->getMountedActionForm(),
        ];
    }

    public function getMountedActionForm(): ?Forms\ComponentContainer
    {
        $action = $this->getMountedAction();

        if (! $action) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedActionForm')) {
            return $this->getCachedForm('mountedActionForm');
        }

        return $this->makeForm()
            ->schema($action->getFormSchema())
            ->statePath('mountedActionData')
            ->model($action->getModel() ?? $this->getMountedActionFormModel())
            ->context($this->mountedAction);
    }

    protected function getMountedActionFormModel(): Model | string | null
    {
        return null;
    }

    public function getCachedAction(string $name): ?Action
    {
        $actions = $this->getCachedActions();

        $action = $actions[$name] ?? null;

        if ($action) {
            return $action;
        }

        foreach ($actions as $action) {
            if (! $action instanceof ActionGroup) {
                continue;
            }

            $groupedAction = $action->getActions()[$name] ?? null;

            if (! $groupedAction) {
                continue;
            }

            return $groupedAction;
        }

        return null;
    }

    protected function getActions(): array
    {
        return [];
    }
}
