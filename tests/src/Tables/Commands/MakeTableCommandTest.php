<?php

use Filament\Tests\TestCase;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class)->group('commands');

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
        '--model-namespace' => app()->getNamespace() . 'Models\\Blog',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Tables/PostsTable.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});
