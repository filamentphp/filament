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

it('renders accessible charts and stops chart work after `destroy()`', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/stats-overview-widget-browser-test')
        ->assertScript(<<<'JS'
            (() => {
                const canvas = document.querySelector('[data-testid="orders-stat"] canvas')
                const component = canvas && Alpine.$data(canvas.closest('[x-data]'))

                return typeof component?.getChart === 'function' && !!component.getChart()
            })()
            JS, true)
        ->assertNoAccessibilityIssues();

    // `inDarkMode()` requires a fresh browser context.
    visit('/stats-overview-widget-browser-test')
        ->inDarkMode()
        ->assertScript(<<<'JS'
            (() => {
                const canvas = document.querySelector('[data-testid="orders-stat"] canvas')
                const component = canvas && Alpine.$data(canvas.closest('[x-data]'))

                return typeof component?.getChart === 'function' && !!component.getChart()
            })()
            JS, true)
        ->assertNoAccessibilityIssues()
        ->assertScript(<<<'JS'
            (async () => {
                const canvas = document.querySelector('[data-testid="orders-stat"] canvas')
                const element = canvas.closest('[x-data]')
                const component = Alpine.$data(element)
                const chart = component.getChart()
                const initialValues = JSON.stringify(chart.data.datasets[0].data) === '[13,4,21,9]'
                const livewireElement = element.closest('[wire\\:id]')

                livewireElement.dispatchEvent(new CustomEvent('updateStatsOverviewChartData', {
                    detail: { key: component.key, data: [7, 2, 19] },
                }))

                const updatesInPlace = component.getChart() === chart
                    && JSON.stringify(chart.data.datasets[0].data) === '[7,2,19]'
                    && JSON.stringify(chart.data.labels) === '[0,1,2]'

                Alpine.store('theme', 'system')
                await Alpine.nextTick()

                let destroyedLookups = 0
                let themeUpdates = 0
                const getChart = component.getChart
                const updateChartTheme = component.updateChartTheme
                component.getChart = () => {
                    destroyedLookups++

                    return getChart.call(component)
                }
                component.updateChartTheme = () => {
                    themeUpdates++

                    return updateChartTheme.call(component)
                }

                component.systemThemeListener()
                Alpine.store('theme', 'light')
                Alpine.destroyTree(element)
                const lookupsAtDestroy = destroyedLookups
                const chartDestroyed = chart.canvas === null
                Alpine.initTree(element)
                await Alpine.nextTick()

                const replacement = Alpine.$data(element)
                const replacementChart = replacement.getChart()
                component.updateChartData([99, 100])
                component.initChart()
                component.systemThemeListener()

                const queuedWorkStopped = destroyedLookups === lookupsAtDestroy
                    && replacement.getChart() === replacementChart
                    && JSON.stringify(replacementChart.data.datasets[0].data) === '[13,4,21,9]'
                const updatesAfterQueuedWork = themeUpdates
                Alpine.store('theme', 'dark')
                await Alpine.nextTick()
                const effectReleased = !component.themeEffect.active
                    && themeUpdates === updatesAfterQueuedWork

                // Tear down before the next instance's deferred `initChart()` runs.
                Alpine.destroyTree(element)
                Alpine.initTree(element)
                const pending = Alpine.$data(element)
                let pendingLookups = 0
                const pendingGetChart = pending.getChart
                pending.getChart = () => {
                    pendingLookups++

                    return pendingGetChart.call(pending)
                }
                Alpine.destroyTree(element)
                const pendingLookupsAtDestroy = pendingLookups
                Alpine.initTree(element)
                await Alpine.nextTick()
                const deferredInitializationStopped = pendingLookups === pendingLookupsAtDestroy
                    && !!Alpine.$data(element).getChart()

                return { initialValues, updatesInPlace, chartDestroyed, queuedWorkStopped, effectReleased, deferredInitializationStopped }
            })()
            JS, [
            'initialValues' => true,
            'updatesInPlace' => true,
            'chartDestroyed' => true,
            'queuedWorkStopped' => true,
            'effectReleased' => true,
            'deferredInitializationStopped' => true,
        ]);
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
