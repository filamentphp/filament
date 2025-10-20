<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    $this->withoutMockingConsoleOutput();
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

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
