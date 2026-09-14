<?php

use Filament\Facades\Filament;
use Filament\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

uses(TestCase::class)->group('serial');

beforeEach(function (): void {
    $this->inertiaDirectory = sys_get_temp_dir() . '/filament-inertia-generator-' . Str::uuid();
    $this->app->getNamespace();
    $this->app->setBasePath($this->inertiaDirectory);
    invade(Filament::getPanel('admin'))->pageDirectories = [$this->inertiaDirectory . '/app'];
    invade(Filament::getPanel('admin'))->pageNamespaces = ['App\\Filament\\Pages'];
    $this->missingInertiaPackages = [];
    $this->inertiaCoreTypes = 'externalNavigation?: ExternalNavigationOptions;';

    $this->partialMock(Filesystem::class, function ($filesystem): void {
        $filesystem->shouldReceive('exists')
            ->withArgs(fn (string $path): bool => str_starts_with($path, base_path('node_modules/')))
            ->andReturnUsing(fn (string $path): bool => ! in_array($path, $this->missingInertiaPackages));
        $filesystem->shouldReceive('get')
            ->with(base_path('node_modules/@inertiajs/core/types/types.d.ts'))
            ->andReturnUsing(fn (): string => $this->inertiaCoreTypes);
    });
});

afterEach(function (): void {
    app(Filesystem::class)->deleteDirectory($this->inertiaDirectory);
});

it('generates a PHP page, component, shared renderer and optional server for each framework', function (string $framework, bool $typescript, string $componentExtension): void {
    $this->artisan('make:filament-page', [
        'name' => 'Sales/Reports', '--panel' => 'admin', "--{$framework}" => true,
        '--ts' => $typescript, '--ssr' => true, '--no-interaction' => true,
    ])->assertSuccessful();

    $extension = $typescript ? 'ts' : 'js';
    $directory = resource_path('js/filament');
    $class = file_get_contents($this->inertiaDirectory . '/app/Sales/Reports.php');

    expect($class)->toContain('use InteractsWithInertia;', 'protected function getInertiaResponse(): Response', "Inertia::render('Filament/Sales/Reports'", "'title' => 'Reports'")
        ->not->toContain('$view');
    expect(file_exists("{$directory}/pages/Sales/Reports.{$componentExtension}"))->toBeTrue();
    expect(file_get_contents("{$directory}/inertia.{$extension}"))->toContain("inertia/{$framework}.js", 'export default createRenderer({ resolve })');
    expect(file_get_contents("{$directory}/resolve.{$extension}"))->toContain("name.startsWith('Filament/')", 'import.meta.glob');
    expect(file_get_contents("{$directory}/ssr.{$extension}"))->toContain("id: 'filament-inertia'", "'./resolve.{$extension}'");
    expect(file_exists(resource_path('views/filament/pages/sales/reports.blade.php')))->toBeFalse();
})->with([
    ['react', false, 'jsx'], ['react', true, 'tsx'],
    ['vue', false, 'vue'], ['vue', true, 'vue'],
    ['svelte', false, 'svelte'], ['svelte', true, 'svelte'],
]);

it('rejects incompatible options before writing files', function (array $options): void {
    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--no-interaction' => true, ...$options,
    ])->assertFailed();

    expect(is_dir($this->inertiaDirectory))->toBeFalse();
})->with([
    [['--vue' => true, '--react' => true]],
    [['--svelte' => true, '--react' => true]],
    [['--ts' => true]], [['--typescript' => true]], [['--ssr' => true]],
    [['--vue' => true, '--resource' => 'Users']],
    [['--vue' => true, '--resource' => null]],
    [['--vue' => true, '-R' => null]],
    [['--vue' => true, '--type' => 'custom']],
    [['--vue' => true, '--resource-namespace' => 'App\\Filament\\Resources']],
]);

it('reports missing dependencies without writing a partial page', function (string $package): void {
    $this->missingInertiaPackages[] = base_path("node_modules/{$package}/package.json");

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--react' => true, '--ts' => true, '--no-interaction' => true,
    ])->expectsOutputToContain("Missing JavaScript package [{$package}]")
        ->assertFailed();

    expect(is_dir($this->inertiaDirectory))->toBeFalse();
})->with(['@inertiajs/react', 'react-dom', '@vitejs/plugin-react', 'typescript', '@types/react']);

it('rejects an Inertia core without `externalNavigation` instead of trusting its version', function (): void {
    $this->inertiaCoreTypes = 'export type Page = {};';

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--vue' => true, '--no-interaction' => true,
    ])->expectsOutputToContain('matching Inertia core and adapter builds with externalNavigation support')
        ->assertFailed();

    expect(is_dir($this->inertiaDirectory))->toBeFalse();
});

it('preserves every shared file with `--force` and reuses JavaScript for `--typescript`', function (): void {
    $directory = resource_path('js/filament');
    app(Filesystem::class)->ensureDirectoryExists($directory);

    foreach (['inertia', 'resolve', 'ssr'] as $name) {
        file_put_contents("{$directory}/{$name}.js", "// Custom {$name}\n");
    }

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--react' => true,
        '--typescript' => true, '--ssr' => true, '--force' => true, '--no-interaction' => true,
    ])->assertSuccessful();

    foreach (['inertia', 'resolve', 'ssr'] as $name) {
        expect(file_get_contents("{$directory}/{$name}.js"))->toBe("// Custom {$name}\n");
        expect(file_exists("{$directory}/{$name}.ts"))->toBeFalse();
    }

    expect(file_get_contents("{$directory}/pages/Reports.tsx"))->toContain('{ title: string }');
});

it('adds a second page without changing the shared resolver or renderer', function (): void {
    $options = ['--panel' => 'admin', '--vue' => true, '--no-interaction' => true];
    $this->artisan('make:filament-page', ['name' => 'Reports', ...$options])->assertSuccessful();
    $directory = resource_path('js/filament');
    $renderer = file_get_contents("{$directory}/inertia.js");
    $resolver = file_get_contents("{$directory}/resolve.js");

    $this->artisan('make:filament-page', ['name' => 'Sales/Orders', ...$options])->assertSuccessful();

    expect(file_exists("{$directory}/pages/Sales/Orders.vue"))->toBeTrue();
    expect(file_get_contents("{$directory}/inertia.js"))->toBe($renderer);
    expect(file_get_contents("{$directory}/resolve.js"))->toBe($resolver);
    expect(file_exists("{$directory}/ssr.js"))->toBeFalse();
});

it('does not overwrite one page file when the other collides', function (string $file): void {
    $path = $this->inertiaDirectory . $file;
    app(Filesystem::class)->ensureDirectoryExists(dirname($path));
    file_put_contents($path, 'existing content');

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--vue' => true, '--no-interaction' => true,
    ])->assertFailed();

    expect(file_get_contents($path))->toBe('existing content');
    expect(app(Filesystem::class)->allFiles($this->inertiaDirectory))->toHaveCount(1);
})->with(['/app/Reports.php', '/resources/js/filament/pages/Reports.vue']);

it('rejects a different framework without modifying existing files', function (): void {
    $options = ['--panel' => 'admin', '--no-interaction' => true];
    $this->artisan('make:filament-page', ['name' => 'Reports', '--vue' => true, ...$options])->assertSuccessful();
    $renderer = file_get_contents(resource_path('js/filament/inertia.js'));

    $this->artisan('make:filament-page', ['name' => 'Orders', '--svelte' => true, '--force' => true, ...$options])
        ->expectsOutputToContain('already uses vue')
        ->assertFailed();

    expect(file_exists($this->inertiaDirectory . '/app/Orders.php'))->toBeFalse();
    expect(file_get_contents(resource_path('js/filament/inertia.js')))->toBe($renderer);
});

it('generates valid PHP when the page name matches an imported class', function (string $name): void {
    $this->artisan('make:filament-page', [
        'name' => $name, '--panel' => 'admin', '--vue' => true, '--no-interaction' => true,
    ])->assertSuccessful();

    $process = new Process([PHP_BINARY, '-l', $this->inertiaDirectory . "/app/{$name}.php"]);
    $process->run();

    expect($process->getOutput() . $process->getErrorOutput())->toContain('No syntax errors detected');
})->with(['Page', 'Inertia', 'Response', 'InteractsWithInertia']);

it('keeps panel and cluster namespaces in the component name', function (string $namespace, string $prefix): void {
    invade(Filament::getPanel('admin'))->pageNamespaces = [$namespace];

    $this->artisan('make:filament-page', [
        'name' => 'Sales/Reports', '--panel' => 'admin', '--vue' => true, '--no-interaction' => true,
    ])->assertSuccessful();

    expect(file_get_contents($this->inertiaDirectory . '/app/Sales/Reports.php'))
        ->toContain("Inertia::render('Filament/{$prefix}/Sales/Reports'");
    expect(file_exists(resource_path("js/filament/pages/{$prefix}/Sales/Reports.vue")))->toBeTrue();
})->with([
    ['App\\Filament\\Admin\\Pages', 'Admin'],
    ['App\\Filament\\Clusters\\Billing\\Pages', 'Clusters/Billing'],
]);

it('overwrites page files with `--force` without changing native app configuration', function (): void {
    $filesystem = app(Filesystem::class);
    $filesystem->ensureDirectoryExists($this->inertiaDirectory . '/app');
    $filesystem->ensureDirectoryExists(resource_path('js/filament/pages'));
    file_put_contents($this->inertiaDirectory . '/app/Reports.php', 'old PHP');
    file_put_contents(resource_path('js/filament/pages/Reports.vue'), 'old component');

    foreach (['vite.config.ts', 'tsconfig.json', 'package.json'] as $path) {
        file_put_contents(base_path($path), "original {$path}");
    }

    file_put_contents(resource_path('js/ssr.ts'), 'original SSR');

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--vue' => true, '--force' => true,
        '--ssr' => true, '--no-interaction' => true,
    ])->assertSuccessful();

    expect(file_get_contents($this->inertiaDirectory . '/app/Reports.php'))->toContain('use InteractsWithInertia;');
    expect(file_get_contents(resource_path('js/filament/pages/Reports.vue')))->toContain('defineProps');

    foreach (['vite.config.ts', 'tsconfig.json', 'package.json'] as $path) {
        expect(file_get_contents(base_path($path)))->toBe("original {$path}");
    }

    expect(file_get_contents(resource_path('js/ssr.ts')))->toBe('original SSR');
});

it('does not create duplicate React components when changing language with `--force`', function (bool $typescript): void {
    $directory = resource_path('js/filament/pages');
    app(Filesystem::class)->ensureDirectoryExists($directory);
    $extension = $typescript ? 'jsx' : 'tsx';
    file_put_contents("{$directory}/Reports.{$extension}", 'existing component');

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--react' => true, '--ts' => $typescript,
        '--force' => true, '--no-interaction' => true,
    ])->expectsOutputToContain('Rename or remove it before changing the component language')
        ->assertFailed();

    expect(file_get_contents("{$directory}/Reports.{$extension}"))->toBe('existing component');
    expect(app(Filesystem::class)->allFiles($this->inertiaDirectory))->toHaveCount(1);
})->with([true, false]);

it('preserves a Blade view when the command is reused to generate an Inertia page', function (): void {
    $this->withoutMockingConsoleOutput();
    $options = ['--panel' => 'admin', '--no-interaction' => true];
    expect($this->artisan('make:filament-page', ['name' => 'Existing', ...$options]))->toBe(0);
    $viewPath = resource_path('views/filament/pages/existing.blade.php');
    file_put_contents($viewPath, 'customized Blade view');

    expect($this->artisan('make:filament-page', ['name' => 'Reports', '--vue' => true, ...$options]))->toBe(0);

    expect(file_get_contents($viewPath))->toBe('customized Blade view');
});
