<?php

namespace Filament\Livewire\Concerns;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HasTenantMenu
{
    /**
     * @var ?array<Action>
     */
    protected ?array $tenantMenuItems = null;

    /**
     * @var array<int, Collection<string, Action>>
     */
    protected array $tenantMenuItemGroupsAfterSwitcher = [];

    /**
     * @var ?array<Model>
     */
    protected ?array $switchableTenants = null;

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
     * @return array<int, Collection<string, Action>>
     */
    public function getTenantMenuItemGroupsAfterSwitcher(): array
    {
        $this->getTenantMenuItems();

        return $this->tenantMenuItemGroupsAfterSwitcher;
    }

    public function hasMultipleTenantMenuItemGroups(): bool
    {
        return Filament::getCurrentPanel()?->hasMultipleTenantMenuItemGroups() ?? false;
    }

    /**
     * @return array<Action>
     */
    protected function getTenantMenuItems(): array
    {
        if (isset($this->tenantMenuItems)) {
            return $this->tenantMenuItems;
        }

        $panel = Filament::getCurrentPanel();

        // Resolve once so the flat menu and the grouped lists share the same cached `Action` instances.
        $groups = $panel?->getTenantMenuItemGroups() ?? [];

        $items = collect($groups)
            ->collapse()
            ->filter(fn (Action $action): bool => $action->isVisible())
            ->sortBy(fn (Action $action): int => $action->getSort())
            ->all();

        foreach ($items as $action) {
            $action->defaultView($action::GROUPED_VIEW);

            $this->cacheAction($action);
        }

        // Split the groups into the separate lists rendered after the tenant switcher. The identity items (a
        // negative sort, like the profile and billing links) are only pulled out when a switcher is present to
        // render them before; without one, they stay in their group so they are not dropped.
        if ($panel?->hasMultipleTenantMenuItemGroups()) {
            $canSwitchTenants = $this->canSwitchTenants();

            $this->tenantMenuItemGroupsAfterSwitcher = collect($groups)
                ->map(fn (array $group): Collection => collect($group)
                    ->filter(fn (Action $action): bool => $action->isVisible() && ((! $canSwitchTenants) || ($action->getSort() >= 0)))
                    ->sortBy(fn (Action $action): int => $action->getSort()))
                ->reject(fn (Collection $group): bool => $group->isEmpty())
                ->values()
                ->all();
        }

        if (blank($items)) {
            return [];
        }

        return $this->tenantMenuItems = $items;
    }

    /**
     * @return array<Model>
     */
    protected function getSwitchableTenants(): array
    {
        if (isset($this->switchableTenants)) {
            return $this->switchableTenants;
        }

        if (! Filament::hasTenantSwitcher()) {
            return $this->switchableTenants = [];
        }

        $currentTenant = Filament::getTenant();

        return $this->switchableTenants = array_filter(
            Filament::getUserTenants(Filament::auth()->user()),
            fn (Model $tenant): bool => ! $tenant->is($currentTenant),
        );
    }

    protected function canSwitchTenants(): bool
    {
        return filled($this->getSwitchableTenants());
    }
}
