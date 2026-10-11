<?php

use Filament\Support\Facades\FilamentCli;
use Filament\Tests\TestCase;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class)->group('serial');

beforeEach(function (): void {
    $this->withoutMockingConsoleOutput();
});

it('can generate a Livewire schema component', function (): void {
    $this->artisan('make:filament-livewire-schema', [
        'name' => 'ViewBlogPost',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Livewire/ViewBlogPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    assertFileExists($viewPath = resource_path('views/livewire/view-blog-post.blade.php'));
    expect(file_get_contents($viewPath))
        ->toMatchSnapshot();
});

it('can generate a Livewire schema component in a nested directory', function (): void {
    $this->artisan('make:filament-livewire-schema', [
        'name' => 'Blog/ViewPost',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Livewire/Blog/ViewPost.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    assertFileExists($viewPath = resource_path('views/livewire/blog/view-post.blade.php'));
    expect(file_get_contents($viewPath))
        ->toMatchSnapshot();
});

it('can generate a Livewire schema component in a custom namespace without a `Livewire` segment', function (): void {
    FilamentCli::registerLivewireComponentLocation(
        path: base_path('src/Components'),
        namespace: 'App\\Components',
        viewNamespace: '',
    );

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-livewire-schema', [
        'name' => 'Admin/EditUser',
    ])
        ->expectsQuestion('Where would you like to create the schema?', 'App\\Components');

    assertFileExists($path = base_path('src/Components/Admin/EditUser.php'));
    expect(file_get_contents($path))
        ->toContain("return view('livewire.admin.edit-user');");

    assertFileExists(resource_path('views/livewire/admin/edit-user.blade.php'));
});

it('preserves namespace segments beneath `Livewire` and nested component segments named `Livewire`', function (): void {
    FilamentCli::registerLivewireComponentLocation(
        path: base_path('src/Livewire/Admin'),
        namespace: 'App\\Livewire\\Admin',
        viewNamespace: '',
    );

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-livewire-schema', [
        'name' => 'Blog/Livewire/ViewPost',
    ])
        ->expectsQuestion('Where would you like to create the schema?', 'App\\Livewire\\Admin');

    assertFileExists($path = base_path('src/Livewire/Admin/Blog/Livewire/ViewPost.php'));
    expect(file_get_contents($path))
        ->toContain("return view('livewire.admin.blog.livewire.view-post');");

    assertFileExists(resource_path('views/livewire/admin/blog/livewire/view-post.blade.php'));
});
