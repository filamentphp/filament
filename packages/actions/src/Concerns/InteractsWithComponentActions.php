<?php

namespace Filament\Actions\Concerns;

use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Schema\ComponentContainer;
use Filament\Schema\Components\Component;
use Filament\Schema\Components\Contracts\ExposesStateToActionData;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Illuminate\Validation\ValidationException;
use Throwable;

use function Livewire\store;

/**
 * @property ComponentContainer $mountedFormComponentActionForm
 */
trait InteractsWithComponentActions
{
    /**
     * @var array<array<string, mixed>> | null
     */
    public ?array $mountedFormComponentActions = [];

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

        $action->mergeArguments($arguments);

        $form = $this->getMountedFormComponentActionForm(mountedAction: $action);

        $result = null;

        try {
            $action->beginDatabaseTransaction();

            $formData = [];

            if (($actionComponent = $action->getComponent()) instanceof ExposesStateToActionData) {
                foreach ($actionComponent->getChildComponentContainers() as $actionComponentChildComponentContainer) {
                    $formData = [
                        ...$formData,
                        ...$actionComponentChildComponentContainer->getState(),
                    ];
                }
            }

            if ($this->mountedFormComponentActionHasForm(mountedAction: $action)) {
                $action->callBeforeFormValidated();

                $formData = [
                    ...$formData,
                    ...$form->getState(),
                ];

                $action->callAfterFormValidated();
            }

            $action->formData($formData);

            $action->callBefore();

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

            if (! $this->mountedFormComponentActionShouldOpenModal(mountedAction: $action)) {
                $action->resetArguments();
                $action->resetFormData();

                $this->unmountFormComponentAction();
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

        $this->unmountFormComponentAction();

        return $result;
    }

    /**
     * @param  array<string, mixed>  $action
     */
    public function mountFormComponentAction(array $action): mixed
    {
        $this->mountedFormComponentActions[] = $action;

        $action = $this->getMountedFormComponentAction();

        if (! $action) {
            $this->unmountFormComponentAction();

            return null;
        }

        if ($action->isDisabled()) {
            $this->unmountFormComponentAction();

            return null;
        }

        if (($actionComponent = $action->getComponent()) instanceof ExposesStateToActionData) {
            foreach ($actionComponent->getChildComponentContainers() as $actionComponentChildComponentContainer) {
                $actionComponentChildComponentContainer->validate();
            }
        }

        $this->cacheMountedFormComponentActionForm(mountedAction: $action);

        try {
            $hasForm = $this->mountedFormComponentActionHasForm(mountedAction: $action);

            if ($hasForm) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedFormComponentActionForm(mountedAction: $action),
            ]);

            if ($hasForm) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return null;
        } catch (Cancel $exception) {
            $this->unmountFormComponentAction(shouldCancelParentActions: false);

            return null;
        }

        if (! $this->mountedFormComponentActionShouldOpenModal(mountedAction: $action)) {
            return $this->callMountedFormComponentAction();
        }

        $this->resetErrorBag();

        $this->openFormComponentActionModal();

        return null;
    }

    protected function cacheMountedFormComponentActionForm(?Action $mountedAction = null): void
    {
        $this->cacheSchema(
            'mountedFormComponentActionForm' . array_key_last($this->mountedFormComponentActions),
            fn () => $this->getMountedFormComponentActionForm(mountedAction: $mountedAction),
        );
    }

    public function mountedFormComponentActionShouldOpenModal(?Action $mountedAction = null): bool
    {
        return ($mountedAction ?? $this->getMountedFormComponentAction())->shouldOpenModal(
            checkForFormUsing: $this->mountedFormComponentActionHasForm(...),
        );
    }

    public function mountedFormComponentActionHasForm(?Action $mountedAction = null): bool
    {
        return (bool) count($this->getMountedFormComponentActionForm(mountedAction: $mountedAction)?->getComponents() ?? []);
    }

    public function getMountedFormComponentAction(?int $actionNestingIndex = null): ?Action
    {
        $actionNestingIndex ??= array_key_last($this->mountedFormComponentActions);
        $action = $this->mountedFormComponentActions[$actionNestingIndex] ?? [];
        $actionName = $action['name'] ?? null;

        if (blank($actionName)) {
            return null;
        }

        return $this->getMountedFormComponentActionComponent($actionNestingIndex)
            ?->getAction($actionName)
            ?->arguments($action['arguments'] ?? []);
    }

    protected function getMountedFormComponentActionForm(?int $actionNestingIndex = null, ?Action $mountedAction = null): ?ComponentContainer
    {
        $actionNestingIndex ??= array_key_last($this->mountedFormComponentActions);

        $mountedAction ??= $this->getMountedFormComponentAction($actionNestingIndex);

        if (! ($mountedAction instanceof Action)) {
            return null;
        }

        if ((! $this->isCachingSchemas) && $this->hasCachedSchema("mountedFormComponentActionForm{$actionNestingIndex}")) {
            return $this->getSchema("mountedFormComponentActionForm{$actionNestingIndex}");
        }

        return $mountedAction->getForm(
            $this->makeForm()
                ->model($this->getMountedFormComponentActionComponent($actionNestingIndex)->getActionFormModel())
                ->statePath("mountedFormComponentActions.{$actionNestingIndex}.data")
                ->operation(
                    collect($this->mountedFormComponentActions)
                        ->take($actionNestingIndex + 1)
                        ->pluck('name')
                        ->implode('.'),
                ),
        );
    }

    public function getMountedFormComponentActionComponent(?int $actionNestingIndex = null): ?Component
    {
        $actionNestingIndex ??= array_key_last($this->mountedFormComponentActions);
        $componentKey = $this->mountedFormComponentActions[$actionNestingIndex]['context']['schemaComponent'] ?? null;

        if (blank($componentKey)) {
            return null;
        }

        foreach ($this->getCachedSchemas() as $form) {
            $component = $form->getComponent($componentKey);

            if (! $component) {
                continue;
            }

            return $component;
        }

        return null;
    }

    public function unmountFormComponentAction(bool $shouldCancelParentActions = true): void
    {
        $action = $this->getMountedFormComponentAction();

        if (! ($shouldCancelParentActions && $action)) {
            array_pop($this->mountedFormComponentActions);
        } elseif ($action->shouldCancelAllParentActions()) {
            $this->mountedFormComponentActions = [];
        } else {
            $parentActionToCancelTo = $action->getParentActionToCancelTo();

            while (true) {
                $recentlyClosedParentAction = array_pop($this->mountedFormComponentActions);

                if (
                    blank($parentActionToCancelTo) ||
                    ($recentlyClosedParentAction['name'] === $parentActionToCancelTo)
                ) {
                    break;
                }
            }
        }

        if (! count($this->mountedFormComponentActions)) {
            $this->closeFormComponentActionModal();

            return;
        }

        $this->resetErrorBag();

        $this->openFormComponentActionModal();
    }

    protected function closeFormComponentActionModal(): void
    {
        $this->dispatch('close-modal', id: "{$this->getId()}-form-component-action");

        $this->dispatch('closed-form-component-action-modal', id: $this->getId());
    }

    protected function openFormComponentActionModal(): void
    {
        $this->dispatch('open-modal', id: "{$this->getId()}-form-component-action");

        $this->dispatch('opened-form-component-action-modal', id: $this->getId());
    }

    public function mountedFormComponentActionInfolist(): Infolist
    {
        return $this->getMountedFormComponentAction()->getInfolist();
    }
}
