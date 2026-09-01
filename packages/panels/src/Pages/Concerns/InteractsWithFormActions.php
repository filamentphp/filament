<?php

namespace Filament\Pages\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;

trait InteractsWithFormActions /** @phpstan-ignore trait.unused */
{
    /**
     * @var array<Action | ActionGroup>
     */
    protected array $cachedFormActions = [];

    public function cacheInteractsWithFormActions(): void
    {
        $actions = $this->getFormActions();

        foreach ($actions as $action) {
            if ($action instanceof ActionGroup) {
                $action->livewire($this);

                /** @var array<string, Action> $flatActions */
                $flatActions = $action->getFlatActions();

                $this->mergeCachedActions($flatActions);
                $this->cachedFormActions[] = $action;

                continue;
            }

            $this->cacheAction($action);
            $this->cachedFormActions[] = $action;
        }
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getCachedFormActions(): array
    {
        return $this->cachedFormActions;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getFormActions(): array
    {
        return [];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}
