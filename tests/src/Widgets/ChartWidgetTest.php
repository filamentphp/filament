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
