<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedNotificationAndChartManualBoundaries;

use Filament\Notifications\Livewire\Notifications;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class LegacyNotifications extends Notifications
{
    public function pullNotificationsFromSession(array $receipts = []): void
    {
        parent::pullNotificationsFromSession($receipts);
    }
}

class CanonicalNotifications extends Notifications
{
    public function pullNotifications(array $receipts = []): void {}
}

abstract class FiltersChart extends ChartWidget
{
    use HasFiltersSchema;
}

abstract class LegacyChart extends FiltersChart
{
    public function resetFiltersForm(): void
    {
        parent::resetFiltersForm();
    }
}

abstract class CanonicalChart extends ChartWidget
{
    use HasFiltersSchema;

    public function resetFilters(): void {}
}

abstract class HiddenChart extends ChartWidget
{
    use HasFiltersSchema {
        resetFilters as protected;
    }
}

class StandaloneFilters
{
    use HasFiltersSchema;
}

class Unrelated
{
    public function pullNotificationsFromSession(): void {}

    public function resetFiltersForm(): void {}
}

function manual(Notifications $notifications, LegacyNotifications $legacyNotifications, CanonicalNotifications $canonicalNotifications, LegacyChart $legacyChart, CanonicalChart $canonicalChart, HiddenChart $hiddenChart, ChartWidget $bareChart, StandaloneFilters $standalone, Unrelated $unrelated, Notifications | Unrelated $union, LegacyChart | CanonicalChart $chartUnion, $unknown): void
{
    $legacyNotifications->pullNotificationsFromSession(receipts: []);
    $canonicalNotifications->pullNotificationsFromSession([]);
    $legacyChart->resetFiltersForm();
    $canonicalChart->resetFiltersForm();
    $hiddenChart->resetFiltersForm();
    $bareChart->resetFiltersForm();
    $standalone->resetFiltersForm();
    $unrelated->pullNotificationsFromSession();
    $unrelated->resetFiltersForm();
    $union->pullNotificationsFromSession();
    $chartUnion->resetFiltersForm();
    $unknown->pullNotificationsFromSession();
    $method = 'pullNotificationsFromSession';
    $notifications->{$method}();
    $notifications->{'pullNotificationsFromSession'}();
    $callback = [$notifications, 'pullNotificationsFromSession'];
    Notifications::pullNotificationsFromSession();
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedNotificationAndChartManualBoundaries;

use Filament\Notifications\Livewire\Notifications;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class LegacyNotifications extends Notifications
{
    public function pullNotificationsFromSession(array $receipts = []): void
    {
        parent::pullNotifications($receipts);
    }
}

class CanonicalNotifications extends Notifications
{
    public function pullNotifications(array $receipts = []): void {}
}

abstract class FiltersChart extends ChartWidget
{
    use HasFiltersSchema;
}

abstract class LegacyChart extends FiltersChart
{
    public function resetFiltersForm(): void
    {
        parent::resetFilters();
    }
}

abstract class CanonicalChart extends ChartWidget
{
    use HasFiltersSchema;

    public function resetFilters(): void {}
}

abstract class HiddenChart extends ChartWidget
{
    use HasFiltersSchema {
        resetFilters as protected;
    }
}

class StandaloneFilters
{
    use HasFiltersSchema;
}

class Unrelated
{
    public function pullNotificationsFromSession(): void {}

    public function resetFiltersForm(): void {}
}

function manual(Notifications $notifications, LegacyNotifications $legacyNotifications, CanonicalNotifications $canonicalNotifications, LegacyChart $legacyChart, CanonicalChart $canonicalChart, HiddenChart $hiddenChart, ChartWidget $bareChart, StandaloneFilters $standalone, Unrelated $unrelated, Notifications | Unrelated $union, LegacyChart | CanonicalChart $chartUnion, $unknown): void
{
    $legacyNotifications->pullNotificationsFromSession(receipts: []);
    $canonicalNotifications->pullNotificationsFromSession([]);
    $legacyChart->resetFiltersForm();
    $canonicalChart->resetFiltersForm();
    $hiddenChart->resetFiltersForm();
    $bareChart->resetFiltersForm();
    $standalone->resetFiltersForm();
    $unrelated->pullNotificationsFromSession();
    $unrelated->resetFiltersForm();
    $union->pullNotificationsFromSession();
    $chartUnion->resetFiltersForm();
    $unknown->pullNotificationsFromSession();
    $method = 'pullNotificationsFromSession';
    $notifications->{$method}();
    $notifications->{'pullNotificationsFromSession'}();
    $callback = [$notifications, 'pullNotificationsFromSession'];
    Notifications::pullNotificationsFromSession();
}
