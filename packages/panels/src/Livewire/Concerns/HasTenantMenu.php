<?php

namespace Filament\Livewire\Concerns;

use Filament\Actions\Action;
use Filament\Facades\Filament;

trait HasTenantMenu
{
    /**
     * @var array<Action>
     */
    protected array $tenantMenuItems = [];

    public function bootHasTenantMenu(): void
    {
        if (! Filament::hasTenancy()) {
            return;
        }

        if (! Filament::hasTenantMenu()) {
            return;
        }

        $this->tenantMenuItems = Filament::getTenantMenuItems();

        foreach ($this->tenantMenuItems as $action) {
            $action->defaultView($action::GROUPED_VIEW);

            $this->cacheAction($action);
        }
    }

    /**
     * @return array<Action>
     */
    protected function getTenantMenuItems(): array
    {
        return $this->tenantMenuItems;
    }
}
