<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    // No specific setup needed
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

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
