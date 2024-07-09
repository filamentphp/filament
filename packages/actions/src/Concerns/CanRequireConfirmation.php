<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;

trait CanRequireConfirmation
{
    public function requiresConfirmation(bool | Closure $condition = true): static
    {
        $this->modalAlignment(fn (Action $action): ?Alignment => $action->evaluate($condition) ? Alignment::Center : null);
        $this->modalFooterActionsAlignment(fn (Action $action): ?Alignment => $action->evaluate($condition) ? Alignment::Center : null);
        $this->modalIcon(fn (Action $action): ?string => $action->evaluate($condition) ? (FilamentIcon::resolve('actions::modal.confirmation') ?? 'heroicon-o-exclamation-triangle') : null);
        $this->modalHeading ??= fn (Action $action): string | Htmlable | null => $action->evaluate($condition) ? $action->getLabel() : null;
        $this->modalDescription(fn (Action $action): ?string => $action->evaluate($condition) ? __('filament-actions::modal.confirmation') : null);
        $this->modalSubmitActionLabel(fn (Action $action): ?string => $action->evaluate($condition) ? __('filament-actions::modal.actions.confirm.label') : null);
        $this->modalWidth(fn (Action $action): ?MaxWidth => $action->evaluate($condition) ? MaxWidth::Medium : null);

        return $this;
    }
}
