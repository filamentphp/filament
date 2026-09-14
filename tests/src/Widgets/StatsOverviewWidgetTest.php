<?php

namespace Filament\Tests\Widgets;

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('returns `null` from `getHeading()` by default', function (): void {
    $widget = Livewire::test(TestStatsOverviewWidgetDefault::class);

    expect($widget->instance()->getSectionContentComponent()->getHeading())->toBeNull();
});

it('returns heading from `getSectionContentComponent()` when `$heading` is set', function (): void {
    $widget = Livewire::test(TestStatsOverviewWidgetWithHeading::class);

    expect($widget->instance()->getSectionContentComponent()->getHeading())->toBe('Overview');
});

it('returns `null` from `getDescription()` by default', function (): void {
    $widget = Livewire::test(TestStatsOverviewWidgetDefault::class);

    expect($widget->instance()->getSectionContentComponent()->getDescription())->toBeNull();
});

it('returns description from `getSectionContentComponent()` when `$description` is set', function (): void {
    $widget = Livewire::test(TestStatsOverviewWidgetWithDescription::class);

    expect($widget->instance()->getSectionContentComponent()->getDescription())->toBe('Key metrics');
});

it('returns a 3-column layout for fewer than 3 stats via `getColumns()`', function (): void {
    $widgetWithNoStats = Livewire::test(TestStatsOverviewWidgetDefault::class);
    $widgetWithOneStat = Livewire::test(TestStatsOverviewWidgetOneStat::class);
    $widgetWithTwoStats = Livewire::test(TestStatsOverviewWidgetTwoStats::class);

    expect($widgetWithNoStats->instance()->getSectionContentComponent()->getColumns())
        ->toBe(['@xl' => 3, '!@lg' => 3])
        ->and($widgetWithOneStat->instance()->getSectionContentComponent()->getColumns())
        ->toBe(['@xl' => 3, '!@lg' => 3])
        ->and($widgetWithTwoStats->instance()->getSectionContentComponent()->getColumns())
        ->toBe(['@xl' => 3, '!@lg' => 3]);
});

it('returns a 4-column layout when stat count mod 3 is 1 via `getColumns()`', function (): void {
    $widget = Livewire::test(TestStatsOverviewWidgetFourStats::class);

    expect($widget->instance()->getSectionContentComponent()->getColumns())
        ->toBe(['@xl' => 4, '!@lg' => 4]);
});

it('returns a 3-column layout when stat count mod 3 is not 1 via `getColumns()`', function (): void {
    $widget = Livewire::test(TestStatsOverviewWidgetThreeStats::class);

    expect($widget->instance()->getSectionContentComponent()->getColumns())
        ->toBe(['@xl' => 3, '!@lg' => 3]);
});

it('returns `full` as the default `$columnSpan`', function (): void {
    $widget = Livewire::test(TestStatsOverviewWidgetDefault::class);

    expect($widget->instance()->getColumnSpan())->toBe('full');
});

it('initializes `$chartDataChecksums` on mount via `mountHasChartData()`', function (): void {
    TestStatsOverviewWidgetWithChart::$chartData = [1, 2, 3];

    $widget = Livewire::test(TestStatsOverviewWidgetWithChart::class);

    $chartDataChecksums = $widget->get('chartDataChecksums');

    expect($chartDataChecksums)->toHaveKey('stats-overview-stat-0')
        ->and($chartDataChecksums['stats-overview-stat-0'])->toBe(md5(json_encode([1, 2, 3])));
});

it('does not dispatch `updateStatsOverviewChartData` when chart data is unchanged', function (): void {
    TestStatsOverviewWidgetWithChart::$chartData = [1, 2, 3];

    Livewire::test(TestStatsOverviewWidgetWithChart::class)
        ->assertNotDispatched('updateStatsOverviewChartData')
        ->call('$refresh')
        ->assertNotDispatched('updateStatsOverviewChartData');
});

it('dispatches `updateStatsOverviewChartData` when chart data changes via `renderingHasChartData()`', function (): void {
    TestStatsOverviewWidgetWithChart::$chartData = [1, 2, 3];

    $widget = Livewire::test(TestStatsOverviewWidgetWithChart::class);

    TestStatsOverviewWidgetWithChart::$chartData = [4, 5, 6];

    $widget
        ->call('$refresh')
        ->assertDispatched('updateStatsOverviewChartData', key: 'stats-overview-stat-0', data: [4, 5, 6]);
});

it('renders values considered filled by `filled()` instead of the placeholder', function (mixed $value): void {
    TestStatsOverviewWidgetWithPlaceholder::$value = $value;

    Livewire::test(TestStatsOverviewWidgetWithPlaceholder::class)
        ->assertSeeHtml('class="fi-wi-stats-overview-stat-value"')
        ->assertDontSeeHtml('class="fi-wi-stats-overview-stat-placeholder"')
        ->assertDontSee('Not available');
})->with([
    'integer zero' => [0],
    'string zero' => ['0'],
    'false' => [false],
]);

it('renders `placeholder()` for values considered blank by `blank()`', function (mixed $value): void {
    TestStatsOverviewWidgetWithPlaceholder::$value = $value;

    Livewire::test(TestStatsOverviewWidgetWithPlaceholder::class)
        ->assertSeeHtml('class="fi-wi-stats-overview-stat-placeholder"')
        ->assertDontSeeHtml('class="fi-wi-stats-overview-stat-value"')
        ->assertSee('Not available');
})->with([
    'null' => [null],
    'empty string' => [''],
    'whitespace-only string' => ['   '],
]);

it('has no accessibility issues in light mode', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/stats-overview-widget-browser-test')
            ->assertSee('Total orders')
            ->assertSee('Not available')
            ->assertNoAccessibilityIssues();
    });
});

it('has no accessibility issues in dark mode', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/stats-overview-widget-browser-test')
            ->inDarkMode()
            ->assertSee('Total orders')
            ->assertSee('Not available')
            ->assertNoAccessibilityIssues();
    });
});

class TestStatsOverviewWidgetDefault extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [];
    }
}

class TestStatsOverviewWidgetWithHeading extends StatsOverviewWidget
{
    protected ?string $heading = 'Overview';

    protected function getStats(): array
    {
        return [];
    }
}

class TestStatsOverviewWidgetWithDescription extends StatsOverviewWidget
{
    protected ?string $description = 'Key metrics';

    protected function getStats(): array
    {
        return [];
    }
}

class TestStatsOverviewWidgetWithChart extends StatsOverviewWidget
{
    /**
     * @var array<int>
     */
    public static array $chartData = [1, 2, 3];

    protected function getStats(): array
    {
        return [
            Stat::make('Users', 100)
                ->chart(static::$chartData),
        ];
    }
}

class TestStatsOverviewWidgetWithPlaceholder extends StatsOverviewWidget
{
    public static mixed $value = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Value', static::$value)
                ->placeholder('Not available'),
        ];
    }
}

class TestStatsOverviewWidgetOneStat extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', 100),
        ];
    }
}

class TestStatsOverviewWidgetTwoStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', 100),
            Stat::make('Revenue', 200),
        ];
    }
}

class TestStatsOverviewWidgetThreeStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', 100),
            Stat::make('Revenue', 200),
            Stat::make('Orders', 300),
        ];
    }
}

class TestStatsOverviewWidgetFourStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', 100),
            Stat::make('Revenue', 200),
            Stat::make('Orders', 300),
            Stat::make('Returns', 50),
        ];
    }
}
