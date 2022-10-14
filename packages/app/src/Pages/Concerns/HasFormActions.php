<?php

namespace Filament\Pages\Concerns;

use Filament\Pages\Actions\Action;

trait HasFormActions
{
    protected ?array $cachedFormActions = null;

    protected function getCachedFormActions(): array
    {
        if ($this->cachedFormActions === null) {
            $this->cacheFormActions();
        }

        return $this->cachedFormActions;
    }

    protected function cacheFormActions(): void
    {
        $this->cachedFormActions = [];

        foreach ($this->getFormActions() as $action) {
            $action->livewire($this);

            $this->cachedFormActions[$action->getName()] = $action;
        }
    }

    public function getCachedFormAction(string $name): ?Action
    {
        return $this->getCachedFormActions()[$name] ?? null;
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}
