<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    // No specific setup needed
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

it('can generate a component class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-schema-component', [
        'name' => 'CustomComponent',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Schemas/Components/CustomComponent.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a component view', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-schema-component', [
        'name' => 'CustomComponent',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = resource_path('views/filament/schemas/components/custom-component.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a component class in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-schema-component', [
        'name' => 'Custom/NestedComponent',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Schemas/Components/Custom/NestedComponent.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a component view in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-schema-component', [
        'name' => 'Custom/NestedComponent',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = resource_path('views/filament/schemas/components/custom/nested-component.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});
