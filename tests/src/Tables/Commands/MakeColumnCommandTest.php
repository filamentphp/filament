<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\assertFileDoesNotExist;
use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    // No specific setup needed
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

it('can generate a column class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-table-column', [
        'name' => 'CustomColumn',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Tables/Columns/CustomColumn.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a column view', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-table-column', [
        'name' => 'CustomColumn',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = resource_path('views/filament/tables/columns/custom-column.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a column class in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-table-column', [
        'name' => 'Custom/NestedColumn',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Tables/Columns/Custom/NestedColumn.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a column view in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-table-column', [
        'name' => 'Custom/NestedColumn',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = resource_path('views/filament/tables/columns/custom/nested-column.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a column class with embedded view', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-table-column', [
        'name' => 'EmbeddedViewColumn',
        '--embedded-view' => true,
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Tables/Columns/EmbeddedViewColumn.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    assertFileDoesNotExist(resource_path('views/filament/tables/columns/embedded-view-column.blade.php'));
});
