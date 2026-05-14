<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Enums\UserMenuPosition;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsIconAlias;
use Illuminate\Support\Collection;

trait HasUserMenu
{
    protected UserMenuPosition | Closure | null $userMenuPosition = null;

    protected bool | Closure $hasUserMenu = true;

    /**
     * Each element is a list of user-registered items: `Action`, `Closure`, or `MenuItem`.
     * Multiple groups render as separate dropdown lists (after the theme switcher) for visual separation.
     *
     * @var array<int, array<int | string, Action | Closure | MenuItem>>
     */
    protected array $userMenuItemGroups = [];

    /**
     * Built menu items for this request. Profile and logout are recreated on each build;
     * caching keeps the same action instances so Livewire can apply the grouped dropdown
     * view and register actions once.
     *
     * @var array<Action> | null
     */
    protected ?array $cachedResolvedUserMenuItems = null;

    public function userMenu(bool | Closure $condition = true, UserMenuPosition | Closure | null $position = null): static
    {
        $this->hasUserMenu = $condition;
        $this->userMenuPosition = $position;

        return $this;
    }

    /**
     * @param  array<int | string, Action | Closure | MenuItem> | array<int, array<int | string, Action | Closure | MenuItem>>  $items
     */
    public function userMenuItems(array $items): static
    {
        if ($items === []) {
            return $this;
        }

        if ($this->isNestedUserMenuItemGroups($items)) {
            foreach ($items as $group) {
                $this->userMenuItemGroups[] = $group;
            }

            return $this;
        }

        if ($this->userMenuItemGroups === []) {
            $this->userMenuItemGroups[] = $items;

            return $this;
        }

        $lastIndex = array_key_last($this->userMenuItemGroups);

        $this->userMenuItemGroups[$lastIndex] = [
            ...$this->userMenuItemGroups[$lastIndex],
            ...$items,
        ];

        return $this;
    }

    /**
     * @param  array<int | string, Action | Closure | MenuItem>  $items
     */
    protected function isNestedUserMenuItemGroups(array $items): bool
    {
        if (! array_is_list($items)) {
            return false;
        }

        foreach ($items as $item) {
            if (! is_array($item)) {
                return false;
            }
        }

        return true;
    }

    public function hasMultipleUserMenuItemGroups(): bool
    {
        return count($this->userMenuItemGroups) > 1;
    }

    /**
     * After-theme items split by registration group (multiple `<x-filament::dropdown.list>`).
     * Empty when not using multiple groups.
     *
     * @return array<int, Collection<string, Action>>
     */
    public function getUserMenuItemGroupsAfterTheme(): array
    {
        if (! $this->hasMultipleUserMenuItemGroups()) {
            return [];
        }

        $flatByName = collect($this->getUserMenuItems())->keyBy(fn (Action $action): string => $action->getName());

        $afterGroups = [];

        foreach ($this->userMenuItemGroups as $rawGroup) {
            $names = $this->resolveActionNamesFromRawGroup($rawGroup);

            $groupActions = collect($names)
                ->map(fn (string $name): ?Action => $flatByName->get($name))
                ->filter()
                ->filter(fn (Action $action): bool => $action->isVisible() && $action->getSort() >= 0)
                ->sortBy(fn (Action $action): int => $action->getSort());

            if ($groupActions->isNotEmpty()) {
                $afterGroups[] = $groupActions;
            }
        }

        $this->appendLogoutToLastAfterThemeGroup($afterGroups, $flatByName);

        return $afterGroups;
    }

    /**
     * @param  array<int | string, Action | Closure | MenuItem>  $rawGroup
     * @return list<string>
     */
    protected function resolveActionNamesFromRawGroup(array $rawGroup): array
    {
        $names = [];

        foreach ($rawGroup as $key => $item) {
            if ($item instanceof Action) {
                $names[] = $item->getName();

                continue;
            }

            if (in_array($key, ['profile', 'account'])) {
                $names[] = 'profile';

                continue;
            }

            if ($key === 'logout') {
                $names[] = 'logout';

                continue;
            }

            $action = $this->evaluate($item);

            if ($action instanceof MenuItem) {
                $action = $action->toAction();
            }

            if ($action instanceof Action) {
                $names[] = $action->getName();
            }
        }

        return $names;
    }

    /**
     * @param  array<int, Collection<string, Action>>  $afterGroups
     * @param  Collection<string, Action>  $flatByName
     */
    protected function appendLogoutToLastAfterThemeGroup(array &$afterGroups, Collection $flatByName): void
    {
        if (! $flatByName->has('logout')) {
            return;
        }

        $logout = $flatByName->get('logout');

        if (! $logout instanceof Action || ! $logout->isVisible()) {
            return;
        }

        foreach ($afterGroups as $group) {
            if ($group->contains(fn (Action $action): bool => $action->getName() === 'logout')) {
                return;
            }
        }

        if ($afterGroups === []) {
            $afterGroups[] = collect(['logout' => $logout]);

            return;
        }

        $lastIndex = array_key_last($afterGroups);
        $afterGroups[$lastIndex] = $afterGroups[$lastIndex]
            ->values()
            ->push($logout)
            ->sortBy(fn (Action $action): int => $action->getSort())
            ->keyBy(fn (Action $action): string => $action->getName());
    }

    public function hasUserMenu(): bool
    {
        return (bool) $this->evaluate($this->hasUserMenu);
    }

    public function getUserMenuPosition(): UserMenuPosition
    {
        return $this->evaluate($this->userMenuPosition) ?? ($this->hasTopbar() ? UserMenuPosition::Topbar : UserMenuPosition::Sidebar);
    }

    protected function getUserProfileMenuItem(Action | Closure | MenuItem | null $item = null): Action
    {
        $page = Filament::getProfilePage();

        $action = Action::make('profile')
            ->label(($page ? $page::getLabel() : null) ?? Filament::getUserName(Filament::auth()->user()))
            ->icon(FilamentIcon::resolve(PanelsIconAlias::USER_MENU_PROFILE_ITEM) ?? Heroicon::UserCircle)
            ->url(Filament::getProfileUrl())
            ->sort(-1);

        if ($item instanceof MenuItem) {
            return $item->toAction($action);
        }

        return $this->evaluate($item, [
            'action' => $action,
        ]) ?? $action;
    }

    protected function getUserLogoutMenuItem(Action | Closure | MenuItem | null $item = null): Action
    {
        $action = Action::make('logout')
            ->label(__('filament-panels::layout.actions.logout.label'))
            ->icon(FilamentIcon::resolve(PanelsIconAlias::USER_MENU_LOGOUT_BUTTON) ?? Heroicon::ArrowLeftEndOnRectangle)
            ->url(Filament::getLogoutUrl())
            ->postToUrl()
            ->sort(PHP_INT_MAX);

        if ($item instanceof MenuItem) {
            return $item->toAction($action);
        }

        return $this->evaluate($item, [
            'action' => $action,
        ]) ?? $action;
    }

    /**
     * @return array<Action>
     */
    public function getUserMenuItems(): array
    {
        if ($this->cachedResolvedUserMenuItems !== null) {
            return $this->cachedResolvedUserMenuItems;
        }

        $flattened = [];

        foreach ($this->userMenuItemGroups as $group) {
            $flattened = array_merge($flattened, $group);
        }

        $this->cachedResolvedUserMenuItems = collect($flattened)
            ->mapWithKeys(function (Action | Closure | MenuItem $item, int | string $key): array {
                if ($item instanceof Action) {
                    return [$item->getName() => $item];
                }

                if (in_array($key, ['profile', 'account'])) {
                    return ['profile' => $this->getUserProfileMenuItem($item)];
                }

                if ($key === 'logout') {
                    return ['logout' => $this->getUserLogoutMenuItem($item)];
                }

                $action = $this->evaluate($item);

                if ($action instanceof MenuItem) {
                    $action = $action->toAction();
                }

                return [$action->getName() => $action];
            })
            ->when(
                fn (Collection $items): bool => ! $items->has('profile'),
                fn (Collection $items): Collection => $items->put('profile', $this->getUserProfileMenuItem()),
            )
            ->when(
                fn (Collection $items): bool => ! $items->has('logout'),
                fn (Collection $items): Collection => $items->put('logout', $this->getUserLogoutMenuItem()),
            )
            ->filter(fn (Action $item): bool => $item->isVisible())
            ->sortBy(fn (Action $item): int => $item->getSort())
            ->all();

        return $this->cachedResolvedUserMenuItems;
    }
}
