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
     * @var array<Action | Closure | MenuItem>
     */
    protected array $userMenuItems = [];

    public function userMenu(bool | Closure $condition = true, UserMenuPosition | Closure | null $position = null): static
    {
        $this->hasUserMenu = $condition;
        $this->userMenuPosition = $position;

        return $this;
    }

    /**
     * @param  array<Action | Closure | MenuItem>  $items
     */
    public function userMenuItems(array $items): static
    {
        $this->userMenuItems = [
            ...$this->userMenuItems,
            ...$items,
        ];

        return $this;
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
        return collect($this->userMenuItems)
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
    }
}
