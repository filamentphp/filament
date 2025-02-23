<?php

use Filament\Commands\MakeResourceCommand;
use Filament\Support\Commands\FileGenerators\FileGenerationFlag;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    config()->set('filament.file_generation.flags', [
        FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_SCHEMAS,
        FileGenerationFlag::EMBEDDED_PANEL_RESOURCE_TABLES,
        FileGenerationFlag::PARTIAL_IMPORTS,
        FileGenerationFlag::PANEL_RESOURCE_CLASSES_OUTSIDE_DIRECTORIES,
    ]);

    $this->withoutMockingConsoleOutput();

    MakeResourceCommand::$shouldCheckModelsForSoftDeletes = false;
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

it('can generate a resource class', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource list page', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/PostResource/Pages/ListPosts.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource create page', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/PostResource/Pages/CreatePost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource edit page', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/PostResource/Pages/EditPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource view page', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--view' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/PostResource/Pages/ViewPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate the form and table of a resource class', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--generate' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource class with soft deletes', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--soft-deletes' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource edit page with soft deletes', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--soft-deletes' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/PostResource/Pages/EditPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a simple resource class', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--simple' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a simple resource manage page', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Post',
        '--simple' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/PostResource/Pages/ManagePosts.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource class in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/PostResource.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource list page in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/PostResource/Pages/ListPosts.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource create page in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/PostResource/Pages/CreatePost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource edit page in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/PostResource/Pages/EditPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a resource view page in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--view' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/PostResource/Pages/ViewPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a simple resource manage page in a nested directory', function (): void {
    $this->artisan('make:filament-resource', [
        'model' => 'Blog/Post',
        '--simple' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Resources/Blog/PostResource/Pages/ManagePosts.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});
