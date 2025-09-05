<?php

namespace Filament\Livewire\Concerns;

use Filament\Actions\Action;
use Filament\Facades\Filament;

trait HasUserMenu
{
    /**
     * @var ?array<Action>
     */
    protected ?array $userMenuItems = null;

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
     * @return array<Action>
     */
    protected function getUserMenuItems(): array
    {
        if (isset($this->userMenuItems)) {
            return $this->userMenuItems;
        }

        $this->userMenuItems = Filament::getUserMenuItems();

        foreach ($this->userMenuItems as $action) {
            $action->defaultView($action::GROUPED_VIEW);

            $this->cacheAction($action);
        }

        if (blank($this->userMenuItems)) {
            $this->userMenuItems = null;
        }

        return $this->userMenuItems ?? [];
    }
}
