<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\MountableAction;

trait CanRequireConfirmation
{
    public function requiresConfirmation(bool | Closure $condition = true): static
    {
        $this->modalSubheading(fn (MountableAction $action): ?string => $action->evaluate($condition) ? __('filament-actions::modal.confirmation') : null);
        $this->modalButton(fn (MountableAction $action): ?string => $action->evaluate($condition) ? __('filament-actions::modal.actions.confirm.label') : null);
        $this->modalWidth(fn (MountableAction $action): ?string => $action->evaluate($condition) ? 'sm' : null);
        $this->centerModal($condition);

        return $this;
    }
}
