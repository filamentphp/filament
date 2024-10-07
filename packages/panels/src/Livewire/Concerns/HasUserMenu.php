<?php

namespace Filament\Livewire\Concerns;

use Filament\Actions\Action;
use Filament\Facades\Filament;

trait HasUserMenu
{
    /**
     * @var array<Action>
     */
    protected array $userMenuItems = [];

    public function bootHasUserMenu(): void
    {
        $this->userMenuItems = Filament::getUserMenuItems();

        foreach ($this->userMenuItems as $action) {
            $action->defaultView($action::GROUPED_VIEW);

            $this->cacheAction($action);
        }
    }

    /**
     * @return array<Action>
     */
    protected function getUserMenuItems(): array
    {
        return $this->userMenuItems;
    }
}
