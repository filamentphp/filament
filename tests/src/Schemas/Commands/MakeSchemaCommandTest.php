<?php

use Filament\Support\Facades\FilamentCli;
use Filament\Tests\TestCase;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class)->group('serial');

it('can generate a schema class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-schema', [
        'name' => 'CustomSchema',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Schemas/CustomSchema.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can run `make:filament-schema` non-interactively when a component location is registered', function (): void {
    $this->withoutMockingConsoleOutput();

    FilamentCli::registerComponentLocation(
        path: base_path('src/Filament'),
        namespace: 'CustomNamespace\\Filament',
        viewNamespace: null,
    );

    $this->artisan('make:filament-schema', [
        'name' => 'NonInteractiveSchema',
        '--no-interaction' => true,
    ]);

    assertFileExists(app_path('Filament/Schemas/NonInteractiveSchema.php'));
});

it('can generate a schema class in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-schema', [
        'name' => 'Custom/NestedSchema',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Schemas/Custom/NestedSchema.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});
