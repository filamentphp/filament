<?php

namespace Filament\Livewire\Concerns;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Support\Collection;

trait HasUserMenu
{
    /**
     * @var ?array<Action>
     */
    protected ?array $userMenuItems = null;

    /**
     * @var array<int, Collection<string, Action>>
     */
    protected array $userMenuItemGroupsAfterTheme = [];

    public function bootHasUserMenu(): void
    {
        if (Filament::auth()->guest()) {
            return;
        }

        if (! Filament::hasUserMenu()) {
            return;
        }

        $this->getUserMenuItems();
    }

    /**
     * @return array<int, Collection<string, Action>>
     */
    public function getUserMenuItemGroupsAfterTheme(): array
    {
        $this->getUserMenuItems();

        return $this->userMenuItemGroupsAfterTheme;
    }

    public function hasMultipleUserMenuItemGroups(): bool
    {
        return Filament::getCurrentPanel()?->hasMultipleUserMenuItemGroups() ?? false;
    }

    /**
     * @return array<Action>
     */
    protected function getUserMenuItems(): array
    {
        if (isset($this->userMenuItems)) {
            return $this->userMenuItems;
        }

        $panel = Filament::getCurrentPanel();

        // Resolve once so the flat menu and the grouped lists share the same cached `Action` instances.
        $groups = $panel?->getUserMenuItemGroups() ?? [];

        $items = collect($groups)
            ->collapse()
            ->filter(fn (Action $action): bool => $action->isVisible())
            ->sortBy(fn (Action $action): int => $action->getSort())
            ->all();

        foreach ($items as $action) {
            $action->defaultView($action::GROUPED_VIEW);

            $this->cacheAction($action);
        }

        // Split the groups into the separate lists rendered after the theme switcher (visible items with a non-negative sort).
        if ($panel?->hasMultipleUserMenuItemGroups()) {
            $this->userMenuItemGroupsAfterTheme = collect($groups)
                ->map(fn (array $group): Collection => collect($group)
                    ->filter(fn (Action $action): bool => $action->isVisible() && ($action->getSort() >= 0))
                    ->sortBy(fn (Action $action): int => $action->getSort()))
                ->reject(fn (Collection $group): bool => $group->isEmpty())
                ->values()
                ->all();
        }

        if (blank($items)) {
            return [];
        }

        return $this->userMenuItems = $items;
    }
}
