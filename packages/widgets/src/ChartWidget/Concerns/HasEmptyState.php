<?php

namespace Filament\Widgets\ChartWidget\Concerns;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Attributes\ServerOnly;
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

    #[ServerOnly]
    public function getEmptyState(): View | Htmlable | null
    {
        return null;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    #[ServerOnly]
    public function getEmptyStateActions(): array
    {
        return [];
    }

    #[ServerOnly]
    public function getEmptyStateDescription(): string | Htmlable | null
    {
        return $this->emptyStateDescription;
    }

    #[ServerOnly]
    public function getEmptyStateHeading(): string | Htmlable
    {
        return $this->emptyStateHeading ?? __('filament-widgets::chart.empty.heading');
    }

    #[ServerOnly]
    public function getEmptyStateIcon(): string | BackedEnum | Htmlable
    {
        return $this->emptyStateIcon
            ?? FilamentIcon::resolve(WidgetsIconAlias::CHART_WIDGET_EMPTY_STATE)
            ?? Heroicon::OutlinedXMark;
    }
}
