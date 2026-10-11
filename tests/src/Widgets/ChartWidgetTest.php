<?php

namespace Filament\Tests\Widgets;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;
use Livewire\Livewire;

uses(TestCase::class);

it('has deferred filters disabled by default', function (): void {
    $widget = Livewire::test(TestChartWidgetDefault::class);

    expect($widget->instance()->hasDeferredFilters())->toBeFalse();
});

it('can enable deferred filters via `$hasDeferredFilters` property', function (): void {
    $widget = Livewire::test(TestChartWidgetWithDeferredFiltersProperty::class);

    expect($widget->instance()->hasDeferredFilters())->toBeTrue();
});

it('initializes both `$filters` and `$deferredFilters` on mount when deferred', function (): void {
    Livewire::test(TestChartWidgetWithDeferredFiltersProperty::class)
        ->assertSet('filters', ['year' => '2024'])
        ->assertSet('deferredFilters', ['year' => '2024']);
});

it('updates `$filters` immediately when deferred is disabled', function (): void {
    Livewire::test(TestChartWidgetDefault::class)
        ->assertSet('filters', ['year' => '2024'])
        ->set('filters.year', '2023')
        ->assertSet('filters', ['year' => '2023']);
});

it('updates only `$deferredFilters` when changed with deferred enabled', function (): void {
    Livewire::test(TestChartWidgetWithDeferredFiltersProperty::class)
        ->assertSet('filters', ['year' => '2024'])
        ->assertSet('deferredFilters', ['year' => '2024'])
        ->set('deferredFilters.year', '2023')
        ->assertSet('filters', ['year' => '2024'])
        ->assertSet('deferredFilters', ['year' => '2023']);
});

it('applies deferred filters when `applyFilters()` is called', function (): void {
    Livewire::test(TestChartWidgetWithDeferredFiltersProperty::class)
        ->set('deferredFilters.year', '2023')
        ->call('applyFilters')
        ->assertSet('filters', ['year' => '2023'])
        ->assertSet('deferredFilters', ['year' => '2023']);
});

it('resets filters to defaults when `resetFiltersForm()` is called', function (): void {
    Livewire::test(TestChartWidgetWithDeferredFiltersProperty::class)
        ->set('deferredFilters.year', '2022')
        ->call('applyFilters')
        ->assertSet('filters', ['year' => '2022'])
        ->call('resetFiltersForm')
        ->assertSet('filters', ['year' => '2024'])
        ->assertSet('deferredFilters', ['year' => '2024']);
});

it('can override `hasDeferredFilters()` for dynamic behavior', function (): void {
    $widget = Livewire::test(TestChartWidgetWithDynamicDeferredFilters::class);

    expect($widget->instance()->hasDeferredFilters())->toBeTrue();
});

it('uses `statePath("deferredFilters")` when deferred', function (): void {
    $widget = Livewire::test(TestChartWidgetWithDeferredFiltersProperty::class);

    expect($widget->instance()->getFiltersSchema()->getStatePath())->toBe('deferredFilters');
});

it('uses `statePath("filters")` when not deferred', function (): void {
    $widget = Livewire::test(TestChartWidgetDefault::class);

    expect($widget->instance()->getFiltersSchema()->getStatePath())->toBe('filters');
});

it('returns `primary` as default value from `getColor()`', function (): void {
    $widget = Livewire::test(TestChartWidgetDefault::class);

    expect($widget->instance()->getColor())->toBe('primary');
});

it('returns custom color from `getColor()` when `$color` is overridden', function (): void {
    $widget = Livewire::test(TestChartWidgetWithCustomColor::class);

    expect($widget->instance()->getColor())->toBe('success');
});

it('returns `false` from `isCollapsible()` by default', function (): void {
    $widget = Livewire::test(TestChartWidgetDefault::class);

    expect($widget->instance()->isCollapsible())->toBeFalse();
});

it('returns `true` from `isCollapsible()` when `$isCollapsible` is overridden', function (): void {
    $widget = Livewire::test(TestChartWidgetCollapsible::class);

    expect($widget->instance()->isCollapsible())->toBeTrue();
});

it('returns heading from `getHeading()`', function (): void {
    $widget = Livewire::test(TestChartWidgetWithHeading::class);

    expect($widget->instance()->getHeading())->toBe('Sales over time');
});

it('returns `null` from `getHeading()` by default', function (): void {
    $widget = Livewire::test(TestChartWidgetDefault::class);

    expect($widget->instance()->getHeading())->toBeNull();
});

it('returns description from `getDescription()`', function (): void {
    $widget = Livewire::test(TestChartWidgetWithDescription::class);

    expect($widget->instance()->getDescription())->toBe('A summary of sales');
});

it('returns `null` from `getDescription()` by default', function (): void {
    $widget = Livewire::test(TestChartWidgetDefault::class);

    expect($widget->instance()->getDescription())->toBeNull();
});

it('returns `null` from `getChartAssistiveContent()` by default', function (): void {
    $widget = Livewire::test(TestChartWidgetDefault::class);

    expect($widget->instance()->getChartAssistiveContent())->toBeNull();
});

it('renders an escaped string from `getChartAssistiveContent()`', function (): void {
    Livewire::test(TestChartWidgetWithStringAssistiveContent::class)
        ->assertSeeHtml('class="fi-wi-chart-assistive-content fi-sr-only"')
        ->assertSee('<strong>Sales increased</strong>')
        ->assertDontSeeHtml('<strong>Sales increased</strong>');
});

it('renders an `Htmlable` from `getChartAssistiveContent()`', function (): void {
    Livewire::test(TestChartWidgetWithHtmlableAssistiveContent::class)
        ->assertSeeHtml('<strong>Sales increased</strong>');
});

it('renders a `View` from `getChartAssistiveContent()`', function (): void {
    Livewire::test(TestChartWidgetWithViewAssistiveContent::class)
        ->assertSeeHtml('<caption>Monthly sales</caption>')
        ->assertSeeHtml('<th scope="col">Month</th>')
        ->assertSeeHtml('<th scope="row">January</th>');
});

it('renders a `View` from `getChartAssistiveContent()` once', function (): void {
    $renderCount = 0;

    app('view')->composer('widgets.chart-assistive-content', function () use (&$renderCount): void {
        $renderCount++;
    });

    Livewire::test(TestChartWidgetWithViewAssistiveContent::class);

    expect($renderCount)->toBe(1);
});

it('does not render `getChartAssistiveContent()` in the empty state', function (): void {
    Livewire::test(TestEmptyChartWidgetWithAssistiveContent::class)
        ->assertDontSee('There is no chart data.');
});

it('exposes the chart wrapper as an image and hides the canvas', function (): void {
    Livewire::test(TestChartWidgetWithDescription::class)
        ->assertSeeHtml('class="fi-wi-chart-image"')
        ->assertSeeHtml('role="img"')
        ->assertSeeHtml('aria-label="A summary of sales"')
        ->assertSeeHtml('aria-hidden="true"');
});

it('keeps the accessible name and alternative synchronized when the chart data is unchanged', function (): void {
    Livewire::test(TestChartWidgetWithDynamicAssistiveContent::class)
        ->assertSeeHtml('aria-label="Sales. Sales for 2024"')
        ->assertSee('The monthly sales total for 2024 is 60.')
        ->set('filter', '2023')
        ->assertSeeHtml('aria-label="Sales. Sales for 2023"')
        ->assertSee('The monthly sales total for 2023 is 60.');
});

it('keeps chart alternatives accessible through updates and chart replacement', function (): void {
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());

    $page = visit('/chart-widget-browser-test', ['reducedMotion' => 'reduce'])
        ->inLightMode()
        ->assertAttribute('.fi-wi-chart-image', 'aria-label', 'Sales. Sales for 2024')
        ->assertAttribute('.fi-wi-chart-image canvas', 'aria-hidden', 'true')
        ->assertSee('Monthly sales for 2024')
        ->assertScript('document.querySelector(".fi-wi-chart-assistive-content").offsetWidth', 1)
        ->assertScript('document.querySelector(".fi-wi-chart-assistive-content").closest("[aria-hidden=true]")', null)
        ->assertScript('document.querySelector(".fi-wi-chart-assistive-content table").closest(".fi-wi-chart-image")', null)
        ->assertScript('(() => { let element = document.querySelector(".fi-wi-chart-assistive-content table"); while (element) { if (element.hasAttribute("wire:ignore")) return true; element = element.parentElement; } return false; })()', false)
        ->assertScript('document.querySelector(".fi-wi-chart-assistive-content").closest("[aria-live], [role=alert], [role=status]")', null)
        ->assertScript('document.querySelector(".fi-wi-chart-assistive-content").querySelector("[aria-live], [role=alert], [role=status]")', null)
        ->assertNoAccessibilityIssues();

    $page->script('document.querySelector(".fi-wi-chart-image canvas").dataset.preserved = "true"');

    $page->select('.fi-wi-chart-filter select', '2023')
        ->assertAttribute('.fi-wi-chart-image', 'aria-label', 'Sales. Sales for 2023')
        ->assertAttribute('.fi-wi-chart-image canvas', 'data-preserved', 'true')
        ->assertSee('Monthly sales for 2023')
        ->assertScript('document.querySelector(".fi-wi-chart-assistive-content table").closest(".fi-wi-chart-image")', null)
        ->assertScript('(() => { let element = document.querySelector(".fi-wi-chart-assistive-content table"); while (element) { if (element.hasAttribute("wire:ignore")) return true; element = element.parentElement; } return false; })()', false)
        ->assertScript('document.querySelector(".fi-wi-chart-assistive-content").closest("[aria-live], [role=alert], [role=status]")', null)
        ->assertScript('document.querySelector(".fi-wi-chart-assistive-content").querySelector("[aria-live], [role=alert], [role=status]")', null)
        ->assertNoAccessibilityIssues();

    $page->assertScript('Boolean(Alpine.$data(document.querySelector("[role=img] canvas").parentElement).getChart())', true);

    $page->script(<<<'JS'
        const element = document.querySelector('[role=img] canvas').parentElement
        const component = Alpine.$data(element)
        const updateChartTheme = component.updateChartTheme.bind(component)
        window.chartLifecycle = { element, component, chart: component.getChart(), oldDataUpdates: 0, themeUpdates: 0 }
        component.updateChartTheme = () => {
            updateChartTheme()
            window.chartLifecycle.themeUpdates++
        }
        component.$wire.$dispatchSelf('updateChartData', {
            data: { labels: ['April', 'May'], datasets: [{ label: 'Sales', data: [9, 4] }] },
        })
        JS);

    $page->assertScript('window.chartLifecycle.chart.data.datasets[0].data', [9, 4]);
    $page->script('Alpine.store("theme", "dark")');
    $page->assertScript('window.chartLifecycle.themeUpdates > 0', true)
        ->assertScript('window.chartLifecycle.component.getChart() === window.chartLifecycle.chart', true)
        ->assertScript('window.chartLifecycle.chart.data.datasets[0].data', [9, 4]);

    $page->script(<<<'JS'
        const { element, component } = window.chartLifecycle
        Alpine.destroyTree(element)
        component.updateChartData = () => window.chartLifecycle.oldDataUpdates++
        Alpine.initTree(element)
        JS);

    $page->assertScript('Boolean(Alpine.$data(window.chartLifecycle.element).getChart())', true)
        ->assertScript('window.chartLifecycle.chart.canvas', null);
    $page->script(<<<'JS'
        Alpine.$data(window.chartLifecycle.element).$wire.$dispatchSelf('updateChartData', {
            data: { labels: ['June', 'July'], datasets: [{ label: 'Sales', data: [2, 7] }] },
        })
        JS);
    $page->assertScript('Alpine.$data(window.chartLifecycle.element).getChart().data.datasets[0].data', [2, 7])
        ->assertScript('window.chartLifecycle.oldDataUpdates', 0)
        ->assertNoJavaScriptErrors()
        ->assertNoAccessibilityIssues();

    visit('/chart-widget-browser-test', ['reducedMotion' => 'reduce'])
        ->inDarkMode()
        ->assertAttribute('.fi-wi-chart-image', 'aria-label', 'Sales. Sales for 2024')
        ->assertAttribute('.fi-wi-chart-image canvas', 'aria-hidden', 'true')
        ->assertSee('Monthly sales for 2024')
        ->assertScript('document.querySelector(".fi-wi-chart-assistive-content").offsetWidth', 1)
        ->assertScript('document.querySelector(".fi-wi-chart-assistive-content").closest("[aria-hidden=true]")', null)
        ->assertScript('document.querySelector(".fi-wi-chart-assistive-content").closest("[aria-live], [role=alert], [role=status]")', null)
        ->assertScript('document.querySelector(".fi-wi-chart-assistive-content").querySelector("[aria-live], [role=alert], [role=status]")', null)
        ->assertNoAccessibilityIssues();
});

class TestChartWidgetDefault extends ChartWidget
{
    use ChartWidget\Concerns\HasFiltersSchema;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $year = (int) ($this->filters['year'] ?? 2024);

        return [
            'datasets' => [
                [
                    'label' => "Data for {$year}",
                    'data' => [10, 20, 30],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar'],
        ];
    }

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('year')
                    ->options([
                        '2024' => '2024',
                        '2023' => '2023',
                        '2022' => '2022',
                    ])
                    ->default('2024'),
            ]);
    }
}

class TestChartWidgetWithDeferredFiltersProperty extends ChartWidget
{
    use ChartWidget\Concerns\HasFiltersSchema;

    protected bool $hasDeferredFilters = true;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $year = (int) ($this->filters['year'] ?? 2024);

        return [
            'datasets' => [
                [
                    'label' => "Data for {$year}",
                    'data' => [10, 20, 30],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar'],
        ];
    }

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('year')
                    ->options([
                        '2024' => '2024',
                        '2023' => '2023',
                        '2022' => '2022',
                    ])
                    ->default('2024'),
            ]);
    }
}

class TestChartWidgetWithDynamicDeferredFilters extends ChartWidget
{
    use ChartWidget\Concerns\HasFiltersSchema;

    protected function getType(): string
    {
        return 'line';
    }

    public function hasDeferredFilters(): bool
    {
        return true;
    }

    protected function getData(): array
    {
        return [
            'datasets' => [['data' => [10, 20, 30]]],
            'labels' => ['Jan', 'Feb', 'Mar'],
        ];
    }

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('year')->options(['2024' => '2024', '2023' => '2023'])->default('2024'),
            ]);
    }
}

class TestChartWidgetWithCustomColor extends ChartWidget
{
    protected string $color = 'success';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        return ['datasets' => [], 'labels' => []];
    }
}

class TestChartWidgetCollapsible extends ChartWidget
{
    protected bool $isCollapsible = true;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        return ['datasets' => [], 'labels' => []];
    }
}

class TestChartWidgetWithHeading extends ChartWidget
{
    protected ?string $heading = 'Sales over time';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        return ['datasets' => [], 'labels' => []];
    }
}

class TestChartWidgetWithDescription extends ChartWidget
{
    protected ?string $description = 'A summary of sales';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        return ['datasets' => [], 'labels' => []];
    }
}

abstract class TestChartWidgetWithAssistiveContent extends ChartWidget
{
    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        return ['datasets' => [['data' => [10]]], 'labels' => ['January']];
    }
}

class TestChartWidgetWithStringAssistiveContent extends TestChartWidgetWithAssistiveContent
{
    public function getChartAssistiveContent(): string
    {
        return '<strong>Sales increased</strong>';
    }
}

class TestChartWidgetWithHtmlableAssistiveContent extends TestChartWidgetWithAssistiveContent
{
    public function getChartAssistiveContent(): HtmlString
    {
        return new HtmlString('<strong>Sales increased</strong>');
    }
}

class TestChartWidgetWithViewAssistiveContent extends TestChartWidgetWithAssistiveContent
{
    public function getChartAssistiveContent(): View
    {
        return view('widgets.chart-assistive-content');
    }
}

class TestChartWidgetWithDynamicAssistiveContent extends TestChartWidgetWithAssistiveContent
{
    protected ?string $heading = 'Sales';

    public ?string $filter = '2024';

    public function getDescription(): string
    {
        return "Sales for {$this->filter}";
    }

    public function getChartAssistiveContent(): string
    {
        return "The monthly sales total for {$this->filter} is 60.";
    }
}

class TestEmptyChartWidgetWithAssistiveContent extends TestChartWidgetWithStringAssistiveContent
{
    protected function getData(): array
    {
        return [];
    }

    public function getChartAssistiveContent(): string
    {
        return 'There is no chart data.';
    }
}
