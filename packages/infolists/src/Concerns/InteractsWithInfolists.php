<?php

namespace Filament\Infolists\Concerns;

use Exception;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Infolist;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Contracts\HasTable;

use function Livewire\store;

trait InteractsWithInfolists
{
    protected bool $hasInfolistsModalRendered = false;

    /**
     * @var array<string, Infolist>
     */
    protected array $cachedInfolists = [];

    /**
     * @var array<string> | null
     */
    public ?array $mountedInfolistActions = [];

    /**
     * @var array<string, array<string, mixed>> | null
     */
    public ?array $mountedInfolistActionsData = [];

    public ?string $mountedInfolistActionsComponent = null;

    public ?string $mountedInfolistActionsInfolist = null;

    public function getInfolist(string $name): ?Infolist
    {
        $infolist = $this->getCachedInfolists()[$name] ?? null;

        if ($infolist) {
            return $infolist;
        }

        if (! method_exists($this, $name)) {
            return null;
        }

        $infolist = $this->{$name}($this->makeInfolist());

        if (! ($infolist instanceof Infolist)) {
            return null;
        }

        return $this->cacheInfolist($name, $infolist);
    }

    public function cacheInfolist(string $name, Infolist $infolist): Infolist
    {
        $infolist->name($name);

        return $this->cachedInfolists[$name] = $infolist;
    }

    /**
     * @return array<string, Infolist>
     */
    public function getCachedInfolists(): array
    {
        return $this->cachedInfolists;
    }

    protected function hasCachedInfolist(string $name): bool
    {
        return array_key_exists($name, $this->getCachedInfolists());
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function callMountedInfolistAction(array $arguments = []): mixed
    {
        $action = $this->getMountedInfolistAction();

        if (! $action) {
            return null;
        }

        if ($action->isDisabled()) {
            return null;
        }

        $action->arguments($arguments);

        $form = $this->getMountedInfolistActionForm();

        $result = null;

        try {
            if ($this->mountedInfolistActionHasForm()) {
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

        if (store($this)->has('redirect')) {
            return $result;
        }

        $this->unmountInfolistAction();

        return $result;
    }

    public function mountInfolistAction(string $name, ?string $component = null, ?string $infolist = null): mixed
    {
        $this->mountedInfolistActions[] = $name;
        $this->mountedInfolistActionsData[] = [];

        if (blank($this->mountedInfolistActionsComponent) && filled($component)) {
            $this->mountedInfolistActionsComponent = $component;
        }

        if (blank($this->mountedInfolistActionsInfolist) && filled($infolist)) {
            $this->mountedInfolistActionsInfolist = $infolist;
        }

        $action = $this->getMountedInfolistAction();

        if (! $action) {
            $this->unmountInfolistAction();

            return null;
        }

        if ($action->isDisabled()) {
            $this->unmountInfolistAction();

            return null;
        }

        $this->cacheForm(
            'mountedInfolistActionForm',
            fn () => $this->getMountedInfolistActionForm(),
        );

        try {
            $hasForm = $this->mountedInfolistActionHasForm();

            if ($hasForm) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedInfolistActionForm(),
            ]);

            if ($hasForm) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return null;
        } catch (Cancel $exception) {
            $this->unmountInfolistAction(shouldCancelParentActions: false);

            return null;
        }

        if (! $this->mountedInfolistActionShouldOpenModal()) {
            return $this->callMountedInfolistAction();
        }

        $this->resetErrorBag();

        $this->openInfolistActionModal();

        return null;
    }

    protected function openInfolistActionModal(): void
    {
        if (
            ($this instanceof HasActions && count($this->mountedActions)) ||
            ($this instanceof HasForms && count($this->mountedFormComponentActions)) ||
            /** @phpstan-ignore-next-line */
            ($this instanceof HasTable && (count($this->mountedTableActions) || filled($this->mountedTableBulkAction)))
        ) {
            throw new Exception('Currently, infolist actions cannot open modals while they are nested within other action modals.');
        }

        $this->dispatch('open-modal', id: "{$this->getId()}-infolist-action");
    }

    public function mountedInfolistActionShouldOpenModal(): bool
    {
        $action = $this->getMountedInfolistAction();

        if ($action->isModalHidden()) {
            return false;
        }

        return $action->getModalDescription() ||
            $action->getModalContent() ||
            $action->getModalContentFooter() ||
            $action->getInfolist() ||
            $this->mountedInfolistActionHasForm();
    }

    public function mountedInfolistActionHasForm(): bool
    {
        return (bool) count($this->getMountedInfolistActionForm()?->getComponents() ?? []);
    }

    public function getMountedInfolistAction(): ?Action
    {
        if (! count($this->mountedInfolistActions ?? [])) {
            return null;
        }

        return $this->getMountedInfolistActionComponent()?->getAction($this->mountedInfolistActions);
    }

    public function getMountedInfolistActionComponent(): ?Component
    {
        $infolist = $this->getInfolist($this->mountedInfolistActionsInfolist);

        if (! $infolist) {
            return null;
        }

        return $infolist->getComponent($this->mountedInfolistActionsComponent);
    }

    public function getMountedInfolistActionForm(): ?Form
    {
        $action = $this->getMountedInfolistAction();

        if (! $action) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedInfolistActionForm')) {
            return $this->getForm('mountedInfolistActionForm');
        }

        return $action->getForm(
            $this->makeForm()
                ->model($action->getRecord())
                ->statePath('mountedInfolistActionsData.' . array_key_last($this->mountedInfolistActionsData))
                ->operation(implode('.', $this->mountedInfolistActions)),
        );
    }

    public function unmountInfolistAction(bool $shouldCancelParentActions = true): void
    {
        $action = $this->getMountedInfolistAction();

        if (! ($shouldCancelParentActions && $action)) {
            array_pop($this->mountedInfolistActions);
            array_pop($this->mountedInfolistActionsData);
        } elseif ($action->shouldCancelAllParentActions()) {
            $this->mountedInfolistActions = [];
            $this->mountedInfolistActionsData = [];
        } else {
            $parentActionToCancelTo = $action->getParentActionToCancelTo();

            while (true) {
                $recentlyClosedParentAction = array_pop($this->mountedInfolistActions);
                array_pop($this->mountedInfolistActionsData);

                if (
                    blank($parentActionToCancelTo) ||
                    ($recentlyClosedParentAction === $parentActionToCancelTo)
                ) {
                    break;
                }
            }
        }

        if (! count($this->mountedInfolistActions)) {
            $this->mountedInfolistActionsComponent = null;
            $this->mountedInfolistActionsInfolist = null;

            $this->dispatch('close-modal', id: "{$this->getId()}-infolist-action");

            return;
        }

        $this->cacheForm(
            'mountedInfolistActionForm',
            fn () => $this->getMountedInfolistActionForm(),
        );

        $this->resetErrorBag();

        $this->openInfolistActionModal();
    }

    protected function makeInfolist(): Infolist
    {
        return Infolist::make($this);
    }

    /**
     * @return array<string, Forms\Form>
     */
    protected function getInteractsWithInfolistsForms(): array
    {
        return [
            'mountedInfolistActionForm' => $this->getMountedInfolistActionForm(),
        ];
    }
}
