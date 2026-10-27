<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedNotificationAndChartMethods;

use Filament\Notifications\Livewire\Notifications;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

abstract class FilteredChart extends ChartWidget
{
    use HasFiltersSchema;

    public function restore(): void
    {
        $this->resetFiltersForm();
    }
}

abstract class InheritedFilteredChart extends FilteredChart {}

class CustomNotifications extends Notifications {}

function calls(Notifications $notifications, ?CustomNotifications $nullableNotifications, FilteredChart $chart, ?InheritedFilteredChart $nullableChart, array $batches, array $arguments): void
{
    $notifications->pullNotificationsFromSession();
    $notifications->pullNotificationsFromSession(batches: $batches);
    $notifications->PULLNOTIFICATIONSFROMSESSION($batches);
    $notifications->pullNotificationsFromSession(...$arguments);
    $nullableNotifications?->pullNotificationsFromSession($batches);
    $pull = $notifications->pullNotificationsFromSession(...);
    $chart->resetFiltersForm();
    $nullableChart?->resetFiltersForm();
    $reset = $chart->resetFiltersForm(...);
    $notifications->pullNotifications($batches);
    $chart->resetFilters();
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedNotificationAndChartMethods;

use Filament\Notifications\Livewire\Notifications;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

abstract class FilteredChart extends ChartWidget
{
    use HasFiltersSchema;

    public function restore(): void
    {
        $this->resetFilters();
    }
}

abstract class InheritedFilteredChart extends FilteredChart {}

class CustomNotifications extends Notifications {}

function calls(Notifications $notifications, ?CustomNotifications $nullableNotifications, FilteredChart $chart, ?InheritedFilteredChart $nullableChart, array $batches, array $arguments): void
{
    $notifications->pullNotifications();
    $notifications->pullNotifications(batches: $batches);
    $notifications->pullNotifications($batches);
    $notifications->pullNotifications(...$arguments);
    $nullableNotifications?->pullNotifications($batches);
    $pull = $notifications->pullNotifications(...);
    $chart->resetFilters();
    $nullableChart?->resetFilters();
    $reset = $chart->resetFilters(...);
    $notifications->pullNotifications($batches);
    $chart->resetFilters();
}
