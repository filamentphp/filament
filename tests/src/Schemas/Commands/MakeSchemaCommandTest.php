<?php

use Filament\Tests\TestCase;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class)->group('commands');

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
