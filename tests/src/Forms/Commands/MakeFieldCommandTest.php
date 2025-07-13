<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    // No specific setup needed
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

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
