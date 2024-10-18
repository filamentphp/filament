<?php

namespace Filament\Widgets\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

trait HasEmptyState
{
    use EvaluatesClosures;

    protected View | Htmlable | Closure | null $emptyState = null;

    protected string | Htmlable | Closure | null $emptyStateDescription = null;

    protected string | Htmlable | Closure | null $emptyStateHeading = null;

    protected string | Closure | null $emptyStateIcon = null;

    protected bool $empty = false;

    public function getEmptyState(): View | Htmlable | null
    {
        return $this->evaluate($this->emptyState);
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getEmptyStateActions(): array
    {
        return [];
    }

    public function getEmptyStateDescription(): string | Htmlable | null
    {
        return $this->evaluate($this->emptyStateDescription);
    }

    public function getEmptyStateHeading(): string | Htmlable
    {
        return $this->evaluate($this->emptyStateHeading) ?? __('filament-panels::widgets/empty-state.heading');
    }

    public function getEmptyStateIcon(): string
    {
        return $this->evaluate($this->emptyStateIcon)
            ?? FilamentIcon::resolve('panels::widgets.empty-state')
            ?? 'heroicon-o-x-mark';
    }

    public function empty(bool $empty = true): void
    {
        $this->empty = $empty;
    }

    public function isEmpty(): bool
    {
        return $this->empty;
    }
}
