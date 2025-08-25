<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    // No specific setup needed
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

it('can generate an entry class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-infolist-entry', [
        'name' => 'CustomEntry',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Infolists/Components/CustomEntry.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate an entry view', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-infolist-entry', [
        'name' => 'CustomEntry',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = resource_path('views/filament/infolists/components/custom-entry.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate an entry class in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-infolist-entry', [
        'name' => 'Custom/NestedEntry',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Infolists/Components/Custom/NestedEntry.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate an entry view in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-infolist-entry', [
        'name' => 'Custom/NestedEntry',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = resource_path('views/filament/infolists/components/custom/nested-entry.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});
