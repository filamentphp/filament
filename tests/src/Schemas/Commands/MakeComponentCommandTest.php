<?php

use Filament\Tests\TestCase;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class)->group('commands');

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
