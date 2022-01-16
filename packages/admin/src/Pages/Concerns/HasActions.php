<?php

namespace Filament\Pages\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Pages\Actions\Action;

/**
 * @property ComponentContainer $mountedActionForm
 */
trait HasActions
{
    public $mountedAction = null;

    public $mountedActionData = [];

    protected array $cachedActions;

    public function bootedHasActions(): void
    {
        $this->cacheActions();
    }

    public function cacheActions(): void
    {
        $this->cachedActions = collect($this->getActions())
            ->mapWithKeys(function (Action $action): array {
                $action->livewire($this);

                return [$action->getName() => $action];
            })
            ->toArray();
    }

    public function callMountedAction()
    {
        $action = $this->getMountedAction();

        if (! $action) {
            return;
        }

        if ($action->isHidden()) {
            return;
        }

        $data = $this->getMountedActionForm()->getState();

        try {
            return $action->call($data);
        } finally {
            $this->dispatchBrowserEvent('close-modal', [
                'id' => static::class . '-action',
            ]);
        }
    }

    public function mountAction(string $name)
    {
        $this->mountedAction = $name;

        $action = $this->getMountedAction();

        if (! $action) {
            return;
        }

        if ($action->isHidden()) {
            return;
        }

        $this->cacheForm('mountedActionForm');

        app()->call($action->getMountUsing(), [
            'action' => $action,
            'form' => $this->getMountedActionForm(),
        ]);

        if (! $action->shouldOpenModal()) {
            return $this->callMountedAction();
        }

        $this->dispatchBrowserEvent('open-modal', [
            'id' => static::class . '-action',
        ]);
    }

    public function getCachedActions(): array
    {
        return $this->cachedActions;
    }

    public function getMountedAction(): ?Action
    {
        if (! $this->mountedAction) {
            return null;
        }

        return $this->getCachedAction($this->mountedAction);
    }

    public function getMountedActionForm(): ComponentContainer
    {
        return $this->mountedActionForm;
    }

    protected function getCachedAction(string $name): ?Action
    {
        $action = $this->getCachedActions()[$name] ?? null;

        return $action;
    }

    protected function getActions(): array
    {
        return [];
    }
}
