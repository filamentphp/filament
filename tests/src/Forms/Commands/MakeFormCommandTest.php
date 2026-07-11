<?php

use Filament\Forms\Commands\FileGenerators\FormSchemaClassGenerator;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\PostWithHiddenLocation;
use Filament\Tests\TestCase;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class)->group('serial');

beforeEach(function (): void {
    $this->withoutMockingConsoleOutput();
});

it('reports binary columns that cannot be generated as schema components', function (): void {
    $generator = app(FormSchemaClassGenerator::class, [
        'fqn' => 'App\\Filament\\Schemas\\PostForm',
        'modelFqn' => Post::class,
    ]);

    // Generating reads the model schema, which records `binary` columns as skipped.
    expect($generator->generate())
        ->not->toContain("'location'");

    expect($generator->getSkippedColumns())
        ->toHaveKey('location');
});

it('does not report binary columns that are already `$hidden` on the model', function (): void {
    $generator = app(FormSchemaClassGenerator::class, [
        'fqn' => 'App\\Filament\\Schemas\\PostForm',
        'modelFqn' => PostWithHiddenLocation::class,
    ]);

    // The `binary` column is still skipped, but as it is already `$hidden` there is nothing to warn about.
    expect($generator->generate())
        ->not->toContain("'location'");

    expect($generator->getSkippedColumns())
        ->toBeEmpty();
});

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

    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
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

    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});
