<?php

namespace Filament\Tests\Widgets;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('has deferred filters disabled by default', function (): void {
    $widget = Livewire::test(TestChartWidgetDefault::class);

    expect($widget->instance()->hasDeferredFilters())->toBeFalse();
});

it('can enable deferred filters via property', function (): void {
    $widget = Livewire::test(TestChartWidgetWithDeferredFiltersProperty::class);

    expect($widget->instance()->hasDeferredFilters())->toBeTrue();
});

it('can disable deferred filters via property', function (): void {
    $widget = Livewire::test(TestChartWidgetWithoutDeferredFiltersProperty::class);

    expect($widget->instance()->hasDeferredFilters())->toBeFalse();
});

it('initializes both filters and deferredFilters on mount when deferred', function (): void {
    Livewire::test(TestChartWidgetWithDeferredFiltersProperty::class)
        ->assertSet('filters', ['year' => '2024'])
        ->assertSet('deferredFilters', ['year' => '2024']);
});

it('updates filters immediately when deferred is disabled', function (): void {
    Livewire::test(TestChartWidgetDefault::class)
        ->assertSet('filters', ['year' => '2024'])
        ->set('filters.year', '2023')
        ->assertSet('filters', ['year' => '2023'])
        ->assertDispatched('filtersApplied');
});

it('updates only deferredFilters when changed with deferred enabled', function (): void {
    Livewire::test(TestChartWidgetWithDeferredFiltersProperty::class)
        ->assertSet('filters', ['year' => '2024'])
        ->assertSet('deferredFilters', ['year' => '2024'])
        ->set('deferredFilters.year', '2023')
        ->assertSet('filters', ['year' => '2024'])
        ->assertSet('deferredFilters', ['year' => '2023']);
});

it('applies deferred filters when applyFilters is called', function (): void {
    Livewire::test(TestChartWidgetWithDeferredFiltersProperty::class)
        ->set('deferredFilters.year', '2023')
        ->call('applyFilters')
        ->assertSet('filters', ['year' => '2023'])
        ->assertSet('deferredFilters', ['year' => '2023']);
});

it('resets filters to defaults when resetFiltersForm is called', function (): void {
    Livewire::test(TestChartWidgetWithDeferredFiltersProperty::class)
        ->set('deferredFilters.year', '2022')
        ->call('applyFilters')
        ->assertSet('filters', ['year' => '2022'])
        ->call('resetFiltersForm')
        ->assertSet('filters', ['year' => '2024'])
        ->assertSet('deferredFilters', ['year' => '2024']);
});

it('shows apply action when deferred filters are enabled', function (): void {
    $widget = Livewire::test(TestChartWidgetWithDeferredFiltersProperty::class);

    expect($widget->instance()->getFiltersApplyAction()->isVisible())->toBeTrue();
});

it('hides apply action when deferred filters are disabled', function (): void {
    $widget = Livewire::test(TestChartWidgetDefault::class);

    expect($widget->instance()->getFiltersApplyAction()->isVisible())->toBeFalse();
});

it('shows reset action', function (): void {
    $widget = Livewire::test(TestChartWidgetDefault::class);

    expect($widget->instance()->getFiltersResetAction())->not->toBeNull();
});

it('can use deferFilters() to enable deferred filters', function (): void {
    $widget = Livewire::test(TestChartWidgetWithDeferFiltersMethod::class);

    expect($widget->instance()->hasDeferredFilters())->toBeTrue();
});

it('can use deferFilters(false) to disable deferred filters', function (): void {
    $widget = Livewire::test(TestChartWidgetWithDeferFiltersMethodDisabled::class);

    expect($widget->instance()->hasDeferredFilters())->toBeFalse();
});

it('uses statePath("deferredFilters") when deferred', function (): void {
    $widget = Livewire::test(TestChartWidgetWithDeferredFiltersProperty::class);

    expect($widget->instance()->getFiltersSchema()->getStatePath())->toBe('deferredFilters');
});

it('uses statePath("filters") when not deferred', function (): void {
    $widget = Livewire::test(TestChartWidgetDefault::class);

    expect($widget->instance()->getFiltersSchema()->getStatePath())->toBe('filters');
});

it('shows the count of active filters on the trigger action', function (): void {
    $widget = Livewire::test(TestChartWidgetDefault::class);

    expect($widget->instance()->getFiltersActiveCount())->toBe(1);

    $widget->set('filters.year', null);

    expect($widget->instance()->getFiltersActiveCount())->toBeNull();
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

class TestChartWidgetWithoutDeferredFiltersProperty extends ChartWidget
{
    use ChartWidget\Concerns\HasFiltersSchema;

    protected bool $hasDeferredFilters = false;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [['data' => [10, 20]]],
            'labels' => ['A', 'B'],
        ];
    }

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('year')->options(['2024' => '2024'])->default('2024'),
            ]);
    }
}

class TestChartWidgetWithDeferFiltersMethod extends ChartWidget
{
    use ChartWidget\Concerns\HasFiltersSchema;

    protected function getType(): string
    {
        return 'line';
    }

    public function mount(): void
    {
        $this->deferFilters();
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

class TestChartWidgetWithDeferFiltersMethodDisabled extends ChartWidget
{
    use ChartWidget\Concerns\HasFiltersSchema;

    protected function getType(): string
    {
        return 'line';
    }

    public function mount(): void
    {
        $this->deferFilters(false);
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
