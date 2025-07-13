<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    $this->withoutMockingConsoleOutput();
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

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
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    assertFileExists($viewPath = resource_path('views/livewire/create-post-with-fields.blade.php'));
    expect(file_get_contents($viewPath))
        ->toMatchSnapshot();
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
