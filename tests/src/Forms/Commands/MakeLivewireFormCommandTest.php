<?php

use Filament\Support\Facades\FilamentCli;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\View;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class)->group('serial');

beforeEach(function (): void {
    $this->withoutMockingConsoleOutput();
});

it('can generate a Livewire form component', function (): void {
    $this->artisan('make:filament-livewire-form', [
        'name' => 'CreateBlogPost',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Livewire/CreateBlogPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    assertFileExists($viewPath = resource_path('views/livewire/create-blog-post.blade.php'));
    expect(file_get_contents($viewPath))
        ->toMatchSnapshot();
});

it('can run `make:filament-livewire-form` non-interactively when a Livewire component location and an extra view namespace are registered', function (): void {
    FilamentCli::registerLivewireComponentLocation(
        path: base_path('src/Livewire'),
        namespace: 'CustomNamespace\\Livewire',
        viewNamespace: null,
    );

    View::addNamespace('custom', base_path('custom-views'));

    $this->artisan('make:filament-livewire-form', [
        'name' => 'NonInteractiveForm',
        '--no-interaction' => true,
    ]);

    assertFileExists(app_path('Livewire/NonInteractiveForm.php'));
    assertFileExists(resource_path('views/livewire/non-interactive-form.blade.php'));
});

it('can generate a Livewire form component for creating a model', function (): void {
    $this->artisan('make:filament-livewire-form', [
        'name' => 'CreatePost',
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Livewire/CreatePost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    assertFileExists($viewPath = resource_path('views/livewire/create-post.blade.php'));
    expect(file_get_contents($viewPath))
        ->toMatchSnapshot();
});

it('can generate a Livewire form component for editing a model', function (): void {
    $this->artisan('make:filament-livewire-form', [
        'name' => 'EditPost',
        'model' => 'Post',
        '--edit' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Livewire/EditPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    assertFileExists($viewPath = resource_path('views/livewire/edit-post.blade.php'));
    expect(file_get_contents($viewPath))
        ->toMatchSnapshot();
});

it('can generate a Livewire form component with generated fields', function (): void {
    $this->artisan('make:filament-livewire-form', [
        'name' => 'CreatePostWithFields',
        'model' => 'Post',
        '--generate' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Livewire/CreatePostWithFields.php'));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }

    assertFileExists($viewPath = resource_path('views/livewire/create-post-with-fields.blade.php'));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($viewPath))
            ->toMatchSnapshot();
    }
});

it('can generate a Livewire form component in a nested directory', function (): void {
    $this->artisan('make:filament-livewire-form', [
        'name' => 'Blog/CreatePost',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Livewire/Blog/CreatePost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    assertFileExists($viewPath = resource_path('views/livewire/blog/create-post.blade.php'));
    expect(file_get_contents($viewPath))
        ->toMatchSnapshot();
});

it('can generate a Livewire form component for a model in a nested directory', function (): void {
    $this->artisan('make:filament-livewire-form', [
        'name' => 'Blog/CreateCategory',
        'model' => 'Blog/Category',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Livewire/Blog/CreateCategory.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    assertFileExists($viewPath = resource_path('views/livewire/blog/create-category.blade.php'));
    expect(file_get_contents($viewPath))
        ->toMatchSnapshot();
});
