<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\MountableAction;
use Filament\Support\Enums\Alignment;

trait CanRequireConfirmation
{
    public function requiresConfirmation(bool | Closure $condition = true): static
    {
        $this->modalAlignment(fn (MountableAction $action): ?Alignment => $action->evaluate($condition) ? Alignment::Center : null);
        $this->modalFooterActionsAlignment(fn (MountableAction $action): ?Alignment => $action->evaluate($condition) ? Alignment::Center : null);
        $this->modalIcon(fn (MountableAction $action): ?string => $action->evaluate($condition) ? 'heroicon-o-exclamation-triangle' : null);
        $this->modalDescription(fn (MountableAction $action): ?string => $action->evaluate($condition) ? __('filament-actions::modal.confirmation') : null);
        $this->modalSubmitActionLabel(fn (MountableAction $action): ?string => $action->evaluate($condition) ? __('filament-actions::modal.actions.confirm.label') : null);
        $this->modalWidth(fn (MountableAction $action): ?string => $action->evaluate($condition) ? 'md' : null);

        return $this;
    }
}
