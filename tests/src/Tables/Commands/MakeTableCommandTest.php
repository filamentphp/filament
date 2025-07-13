<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    // No specific setup needed
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

it('can generate a table class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-table', [
        'name' => 'CustomTable',
        'model' => 'User',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Tables/CustomTable.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a table class with a model', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-table', [
        'name' => 'PostsTable',
        'model' => 'Post',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Tables/PostsTable.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a table class with generated columns', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-table', [
        'name' => 'PostsTable',
        'model' => 'Post',
        '--generate' => true,
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Tables/PostsTable.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a table class in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-table', [
        'name' => 'Blog/PostsTable',
        'model' => 'Post',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Tables/Blog/PostsTable.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a table class with a model in a custom namespace', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-table', [
        'name' => 'PostsTable',
        'model' => 'Post',
        '--model-namespace' => 'App\\Models\\Blog',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Tables/PostsTable.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});
