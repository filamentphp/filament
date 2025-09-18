<?php

namespace Filament\Livewire\Concerns;

use Filament\Actions\Action;
use Filament\Facades\Filament;

trait HasTenantMenu
{
    /**
     * @var ?array<Action>
     */
    protected ?array $tenantMenuItems;

    public function bootHasTenantMenu(): void
    {
        if (Filament::auth()->guest()) {
            return;
        }

        if (! Filament::hasTenancy()) {
            return;
        }

        if (! Filament::hasTenantMenu()) {
            return;
        }

        $this->getTenantMenuItems();
    }

    /**
     * @return array<Action>
     */
    protected function getTenantMenuItems(): array
    {
        if (isset($this->tenantMenuItems)) {
            return $this->tenantMenuItems;
        }

        $this->tenantMenuItems = Filament::getTenantMenuItems();

        foreach ($this->tenantMenuItems as $action) {
            $action->defaultView($action::GROUPED_VIEW);

            $this->cacheAction($action);
        }

        if (blank($this->tenantMenuItems)) {
            $this->tenantMenuItems = null;
        }

        return $this->tenantMenuItems ?? [];
    }
}
