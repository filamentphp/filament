<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedNotificationAndChartNormalizedUnions;

use Filament\Notifications\Livewire\Notifications;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class LegacyNotifications extends Notifications
{
    public function pullNotificationsFromSession(array $batches = []): void {}
}

abstract class FiltersChart extends ChartWidget
{
    use HasFiltersSchema;
}

abstract class LegacyChart extends FiltersChart
{
    public function resetFiltersForm(): void {}
}

function manual(Notifications | LegacyNotifications $notifications, FiltersChart | LegacyChart $chart): void
{
    $notifications->pullNotificationsFromSession();
    $chart->resetFiltersForm();
}
