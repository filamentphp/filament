<?php

namespace Filament\Widgets\ChartWidget\Concerns;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\View\WidgetsIconAlias;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

trait HasEmptyState
{
    protected ?string $emptyStateDescription = null;

    protected ?string $emptyStateHeading = null;

    protected string | BackedEnum | null $emptyStateIcon = null;

    public function getEmptyState(): View | Htmlable | null
    {
        return null;
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
        return $this->emptyStateDescription;
    }

    public function getEmptyStateHeading(): string | Htmlable
    {
        return $this->emptyStateHeading ?? __('filament-widgets::chart.empty.heading');
    }

    public function getEmptyStateIcon(): string | BackedEnum | Htmlable
    {
        return $this->emptyStateIcon
            ?? FilamentIcon::resolve(WidgetsIconAlias::CHART_WIDGET_EMPTY_STATE)
            ?? Heroicon::OutlinedXMark;
    }
}
