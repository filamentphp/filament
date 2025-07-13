<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    $this->withoutMockingConsoleOutput();
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

it('can generate a form schema class', function (): void {
    $this->artisan('make:filament-form', [
        'name' => 'BlogPostForm',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Schemas/BlogPostForm.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a form schema class for a model', function (): void {
    $this->artisan('make:filament-form', [
        'name' => 'PostForm',
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Schemas/PostForm.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a form schema class in a nested directory', function (): void {
    $this->artisan('make:filament-form', [
        'name' => 'Blog/PostForm',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Schemas/Blog/PostForm.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a form schema class for a model in a nested directory', function (): void {
    $this->artisan('make:filament-form', [
        'name' => 'Blog/CategoryForm',
        'model' => 'Blog/Category',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Schemas/Blog/CategoryForm.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});
