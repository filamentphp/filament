<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    // No specific setup needed
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

it('can generate a custom widget class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-widget', [
        'name' => 'CustomWidget',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Which type of widget would you like to create?', 'Filament\\Widgets\\Widget')
        ->expectsQuestion('Would you like to create this widget in a resource?', false);

    assertFileExists($path = app_path('Filament/Widgets/CustomWidget.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a custom widget view', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-widget', [
        'name' => 'CustomWidget',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Which type of widget would you like to create?', 'Filament\\Widgets\\Widget')
        ->expectsQuestion('Would you like to create this widget in a resource?', false);

    assertFileExists($path = resource_path('views/filament/widgets/custom-widget.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a chart widget class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-widget', [
        'name' => 'ChartWidget',
        '--chart' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Would you like to create this widget in a resource?', false)
        ->expectsQuestion('Which type of chart would you like to create?', 'line');

    assertFileExists($path = app_path('Filament/Widgets/ChartWidget.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a stats overview widget class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-widget', [
        'name' => 'StatsOverviewWidget',
        '--stats-overview' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Would you like to create this widget in a resource?', false);

    assertFileExists($path = app_path('Filament/Widgets/StatsOverviewWidget.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a table widget class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-widget', [
        'name' => 'TableWidget',
        '--table' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Would you like to create this widget in a resource?', false)
        ->expectsQuestion('What is the model?', app()->getNamespace() . 'Models\\User')
        ->expectsQuestion('Should the table columns be generated from the current database columns?', false);

    assertFileExists($path = app_path('Filament/Widgets/TableWidget.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a widget class in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-widget', [
        'name' => 'Custom/NestedWidget',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Which type of widget would you like to create?', 'Filament\\Widgets\\Widget')
        ->expectsQuestion('Would you like to create this widget in a resource?', false);

    assertFileExists($path = app_path('Filament/Widgets/Custom/NestedWidget.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a widget view in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-widget', [
        'name' => 'Custom/NestedWidget',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Which type of widget would you like to create?', 'Filament\\Widgets\\Widget')
        ->expectsQuestion('Would you like to create this widget in a resource?', false);

    assertFileExists($path = resource_path('views/filament/widgets/custom/nested-widget.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});
