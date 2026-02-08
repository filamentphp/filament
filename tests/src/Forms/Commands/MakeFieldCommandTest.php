<?php

use Filament\Tests\TestCase;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class)->group('commands');

it('can generate a field class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-form-field', [
        'name' => 'CustomField',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Forms/Components/CustomField.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a field view', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-form-field', [
        'name' => 'CustomField',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = resource_path('views/filament/forms/components/custom-field.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a field class in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-form-field', [
        'name' => 'Custom/NestedField',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Forms/Components/Custom/NestedField.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a field view in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-form-field', [
        'name' => 'Custom/NestedField',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = resource_path('views/filament/forms/components/custom/nested-field.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});
