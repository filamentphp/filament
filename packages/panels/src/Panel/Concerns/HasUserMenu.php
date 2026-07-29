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

trait HasUserMenu
{
    protected UserMenuPosition | Closure | null $userMenuPosition = null;

    protected bool | Closure $hasUserMenu = true;

    /**
     * @var array<int, array<int | string, Action | Closure | MenuItem>>
     */
    protected array $userMenuItemGroups = [];

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

        $registrationGroups = $this->splitUserMenuRegistrationGroups($items);

        if ($registrationGroups !== null) {
            foreach ($registrationGroups as $group) {
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
     * @param  array<mixed>  $items
     * @return list<array<int | string, Action | Closure | MenuItem>> | null
     */
    protected function splitUserMenuRegistrationGroups(array $items): ?array
    {
        if (! array_is_list($items) || $items === []) {
            return null;
        }

        $groups = [];

        foreach ($items as $entry) {
            if (! is_array($entry)) {
                return null;
            }

            $groups[] = $entry;
        }

        return $groups;
    }

    public function hasMultipleUserMenuItemGroups(): bool
    {
        return count($this->userMenuItemGroups) > 1;
    }

    /**
     * @return array<int, array<string, Action>>
     */
    public function getUserMenuItemGroups(): array
    {
        $groups = array_values(array_map(
            fn (array $group): array => collect($group)
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
                ->all(),
            $this->userMenuItemGroups,
        ));

        $isRegistered = fn (string $name): bool => collect($groups)
            ->contains(fn (array $group): bool => array_key_exists($name, $group));

        if (! $isRegistered('profile')) {
            if ($groups === []) {
                $groups[] = [];
            }

            $firstGroupKey = array_key_first($groups);
            $groups[$firstGroupKey] = ['profile' => $this->getUserProfileMenuItem()] + $groups[$firstGroupKey];
        }

        if (! $isRegistered('logout')) {
            if ($groups === []) {
                $groups[] = [];
            }

            $lastGroupKey = array_key_last($groups);
            $groups[$lastGroupKey] += ['logout' => $this->getUserLogoutMenuItem()];
        }

        return $groups;
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
        return collect($this->getUserMenuItemGroups())
            ->collapse()
            ->filter(fn (Action $item): bool => $item->isVisible())
            ->sortBy(fn (Action $item): int => $item->getSort())
            ->all();
    }
}
