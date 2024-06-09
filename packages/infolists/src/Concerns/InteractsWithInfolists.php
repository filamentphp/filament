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
use Illuminate\Validation\ValidationException;
use Throwable;

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

        $action->mergeArguments($arguments);

        $form = $this->getMountedInfolistActionForm(mountedAction: $action);

        $result = null;

        try {
            $action->beginDatabaseTransaction();

            if ($this->mountedInfolistActionHasForm(mountedAction: $action)) {
                $action->callBeforeFormValidated();

                $form->getState(afterValidate: function (array $state) use ($action) {
                    $action->callAfterFormValidated();

                    $action->formData($state);

                    $action->callBefore();
                });
            } else {
                $action->callBefore();
            }

            $result = $action->call([
                'form' => $form,
            ]);

            $result = $action->callAfter() ?? $result;

            $action->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $action->rollBackDatabaseTransaction() :
                $action->commitDatabaseTransaction();

            return null;
        } catch (Cancel $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $action->rollBackDatabaseTransaction() :
                $action->commitDatabaseTransaction();
        } catch (ValidationException $exception) {
            $action->rollBackDatabaseTransaction();

            if (! $this->mountedInfolistActionShouldOpenModal(mountedAction: $action)) {
                $action->resetArguments();
                $action->resetFormData();

                $this->unmountInfolistAction();
            }

            throw $exception;
        } catch (Throwable $exception) {
            $action->rollBackDatabaseTransaction();

            throw $exception;
        }

        if (store($this)->has('redirect')) {
            return $result;
        }

        $action->resetArguments();
        $action->resetFormData();

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
            fn () => $this->getMountedInfolistActionForm(mountedAction: $action),
        );

        try {
            $hasForm = $this->mountedInfolistActionHasForm(mountedAction: $action);

            if ($hasForm) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedInfolistActionForm(mountedAction: $action),
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

        if (! $this->mountedInfolistActionShouldOpenModal(mountedAction: $action)) {
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

    public function mountedInfolistActionShouldOpenModal(?Action $mountedAction = null): bool
    {
        return ($mountedAction ?? $this->getMountedInfolistAction())->shouldOpenModal(
            checkForFormUsing: $this->mountedInfolistActionHasForm(...),
        );
    }

    public function mountedInfolistActionHasForm(?Action $mountedAction = null): bool
    {
        return (bool) count($this->getMountedInfolistActionForm(mountedAction: $mountedAction)?->getComponents() ?? []);
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

    public function getMountedInfolistActionForm(?Action $mountedAction = null): ?Form
    {
        $mountedAction ??= $this->getMountedInfolistAction();

        if (! $mountedAction) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedInfolistActionForm')) {
            return $this->getForm('mountedInfolistActionForm');
        }

        return $mountedAction->getForm(
            $this->makeForm()
                ->model($mountedAction->getRecord())
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
