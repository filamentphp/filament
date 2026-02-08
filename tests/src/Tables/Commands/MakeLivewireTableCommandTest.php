<?php

use Filament\Tests\TestCase;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class)->group('commands');

beforeEach(function (): void {
    $this->withoutMockingConsoleOutput();
});

it('can generate a Livewire table component', function (): void {
    $this->artisan('make:filament-livewire-table', [
        'name' => 'ListBlogPosts',
        'model' => 'BlogPost',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Livewire/ListBlogPosts.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }

    assertFileExists($viewPath = resource_path('views/livewire/list-blog-posts.blade.php'));
    expect(file_get_contents($viewPath));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a Livewire table component with a model', function (): void {
    $this->artisan('make:filament-livewire-table', [
        'name' => 'ListPosts',
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Livewire/ListPosts.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }

    assertFileExists($viewPath = resource_path('views/livewire/list-posts.blade.php'));
    expect(file_get_contents($viewPath));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a Livewire table component with generated columns', function (): void {
    $this->artisan('make:filament-livewire-table', [
        'name' => 'ListPostsWithColumns',
        'model' => 'Post',
        '--generate' => true,
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Livewire/ListPostsWithColumns.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }

    assertFileExists($viewPath = resource_path('views/livewire/list-posts-with-columns.blade.php'));
    expect(file_get_contents($viewPath));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a Livewire table component in a nested directory', function (): void {
    $this->artisan('make:filament-livewire-table', [
        'name' => 'Blog/ListPosts',
        'model' => 'Post',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Livewire/Blog/ListPosts.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }

    assertFileExists($viewPath = resource_path('views/livewire/blog/list-posts.blade.php'));
    expect(file_get_contents($viewPath));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});

it('can generate a Livewire table component for a model in a nested directory', function (): void {
    $this->artisan('make:filament-livewire-table', [
        'name' => 'Blog/ListCategories',
        'model' => 'Blog/Category',
        '--model-namespace' => 'Filament\Tests\Fixtures\Models',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Livewire/Blog/ListCategories.php'));
    expect(file_get_contents($path));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }

    assertFileExists($viewPath = resource_path('views/livewire/blog/list-categories.blade.php'));
    expect(file_get_contents($viewPath));
    if (config('database.default') === 'testing') {
        expect(file_get_contents($path))
            ->toMatchSnapshot();
    }
});
