<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Support\Collection;

trait HasUserMenu
{
    /**
     * @var array<Action | Closure | MenuItem>
     */
    protected array $userMenuItems = [];

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

    protected function getUserProfileMenuItem(Action | Closure | MenuItem | null $item = null): Action
    {
        $page = Filament::getProfilePage();

        $action = Action::make('profile')
            ->label(($page ? $page::getLabel() : null) ?? Filament::getUserName(Filament::auth()->user()))
            ->icon(FilamentIcon::resolve('panels::user-menu.profile-item') ?? 'heroicon-m-user-circle')
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
            ->icon(FilamentIcon::resolve('panels::user-menu.logout-button') ?? 'heroicon-m-arrow-left-on-rectangle')
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
            ->sort(fn (Action $item): int => $item->getSort())
            ->all();
    }
}
