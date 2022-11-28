<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Filament\Support\Actions\Action;

trait CanRequireConfirmation
{
    public function requiresConfirmation(bool | Closure $condition = true): static
    {
        $this->modalSubheading(fn (Action $action): ?string => $action->evaluate($condition) ? __('filament-support::actions/modal.confirmation') : null);
        $this->modalButton(fn (Action $action): ?string => $action->evaluate($condition) ? __('filament-support::actions/modal.actions.confirm.label') : null);
        $this->modalWidth(fn (Action $action): ?string => $action->evaluate($condition) ? 'sm' : null);
        $this->centerModal($condition);

        return $this;
    }
}
