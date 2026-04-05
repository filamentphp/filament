<?php

namespace Filament\Tests\Widgets;

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
    $widget = Livewire::test(TestStatsOverviewWidgetTwoStats::class);

    expect($widget->instance()->getSectionContentComponent()->getColumns())
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
