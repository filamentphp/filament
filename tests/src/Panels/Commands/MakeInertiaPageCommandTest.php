<?php

use Filament\Commands\MakePageCommand;
use Filament\Facades\Filament;
use Filament\Inertia\InertiaPlugin;
use Filament\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Vite;
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

it('resolves `rendererEntry()` through Vite and clears metadata when `renderer()` changes', function (): void {
    $this->mock(Vite::class)->shouldReceive('asset')->once()
        ->with('resources/js/admin/inertia.ts')->andReturn('/build/renderer.js');
    $plugin = InertiaPlugin::make()->renderer('/old.js')->rendererEntry('resources/js/admin/inertia.ts');

    expect($plugin->getRendererEntry())->toBe('resources/js/admin/inertia.ts');
    expect($plugin->getRenderer())->toBe('/build/renderer.js');
    $plugin->renderer(static fn (): string => '/custom.js');
    expect($plugin->getRendererEntry())->toBeNull();
    expect($plugin->getRenderer())->toBe('/custom.js');
    expect(fn () => $plugin->rendererEntry(null)->getRenderer())->toThrow(LogicException::class);
});

it('reuses the registered `rendererEntry()` and reports only unverified build configuration', function (): void {
    $options = ['--panel' => 'admin', '--vue' => true, '--no-interaction' => true];
    $this->artisan('make:filament-page', ['name' => 'Reports', ...$options])->assertSuccessful();
    Filament::getPanel('admin')->plugin(InertiaPlugin::make()->rendererEntry('resources/js/filament/inertia.js'));
    $filesystem = app(Filesystem::class);
    $filesystem->ensureDirectoryExists(public_path('build'));
    file_put_contents(public_path('build/manifest.json'), json_encode(['resources/js/filament/inertia.js' => ['isEntry' => true]]));
    $renderer = file_get_contents(resource_path('js/filament/inertia.js'));

    $this->artisan('make:filament-page', ['name' => 'Orders', ...$options])
        ->expectsOutputToContain('Panel rendererEntry() already selects')
        ->expectsOutputToContain('is present in the last Vite build')
        ->doesntExpectOutputToContain('Register InertiaPlugin')
        ->assertSuccessful();

    expect(file_get_contents(resource_path('js/filament/inertia.js')))->toBe($renderer);
    expect(file_exists(resource_path('js/filament/pages/Orders.vue')))->toBeTrue();
});

it('does not evaluate an opaque `renderer()` in the command', function (): void {
    Filament::getPanel('admin')->plugin(InertiaPlugin::make()->renderer(static function (): never {
        throw new RuntimeException('Do not evaluate renderer callbacks in the command');
    }));

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--vue' => true, '--no-interaction' => true,
    ])->expectsOutputToContain('Cannot determine the configured renderer source')->assertFailed();
    expect(is_dir($this->inertiaDirectory))->toBeFalse();

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--vue' => true, '--no-interaction' => true,
        '--inertia-entry' => 'resources/js/admin/inertia.js', '--inertia-pages' => 'resources/js/admin/components',
    ])->expectsOutputToContain('renderer binding is unverified')->assertSuccessful();
});

it('generates custom paths with a resolver relative to the entry for each framework', function (string $framework, string $extension): void {
    $this->artisan('make:filament-page', [
        'name' => 'Sales/Reports', '--panel' => 'admin', "--{$framework}" => true, '--ts' => true,
        '--inertia-entry' => 'resources/js/admin/entries/inertia.ts',
        '--inertia-pages' => 'resources/js/admin/components', '--no-ssr' => true, '--no-interaction' => true,
    ])->assertSuccessful();

    expect(file_exists(resource_path("js/admin/components/Sales/Reports.{$extension}")))->toBeTrue();
    expect(file_get_contents(resource_path('js/admin/entries/resolve.ts')))->toContain("'../components/**/*", '`../components/${')->not->toContain('{{ pages }}');
    expect(file_get_contents(resource_path('js/admin/entries/inertia.ts')))->toContain("'./resolve.ts'");
    expect(is_dir(resource_path('js/filament')))->toBeFalse();
})->with([['react', 'tsx'], ['vue', 'vue'], ['svelte', 'svelte']]);

it('rejects a conflicting configured entry before writing files', function (): void {
    Filament::getPanel('admin')->plugin(InertiaPlugin::make()->rendererEntry('resources/js/admin/inertia.js'));

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--vue' => true, '--no-interaction' => true,
        '--inertia-entry' => 'resources/js/other/inertia.js', '--force' => true,
    ])->expectsOutputToContain('differs from the panel rendererEntry()')->assertFailed();
    expect(is_dir($this->inertiaDirectory))->toBeFalse();
});

it('requires an explicit component directory for an existing custom renderer', function (): void {
    app(Filesystem::class)->ensureDirectoryExists(resource_path('js/admin'));
    file_put_contents(resource_path('js/admin/inertia.js'), '// custom renderer');
    Filament::getPanel('admin')->plugin(InertiaPlugin::make()->rendererEntry('resources/js/admin/inertia.js'));

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--vue' => true, '--no-interaction' => true,
    ])->expectsOutputToContain('Pass --inertia-pages')->assertFailed();
    expect(app(Filesystem::class)->allFiles($this->inertiaDirectory))->toHaveCount(1);
});

it('preserves a detected or explicitly selected native SSR entry instead of generating a second server', function (string $server, bool $explicit): void {
    app(Filesystem::class)->ensureDirectoryExists(dirname(base_path($server)));
    file_put_contents(base_path($server), '// native SSR');

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--react' => true, '--ssr' => true,
        '--no-interaction' => true, ...($explicit ? ['--inertia-ssr-entry' => $server] : []),
    ])->expectsOutputToContain('no second server was generated')
        ->expectsOutputToContain("page.component.startsWith('Filament/')")
        ->assertSuccessful();

    expect(file_get_contents(base_path($server)))->toBe('// native SSR');
    expect(file_exists(resource_path('js/filament/ssr.js')))->toBeFalse();
    expect(file_exists(resource_path('js/filament/resolve.js')))->toBeTrue();
})->with([['resources/js/ssr.ts', false], ['resources/js/ssr.tsx', false], ['resources/js/ssr.jsx', false], ['resources/server/render.ts', true]]);

it('rejects invalid setup options before writing files', function (array $options): void {
    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--vue' => true, '--no-interaction' => true, ...$options,
    ])->assertFailed();
    expect(is_dir($this->inertiaDirectory))->toBeFalse();
})->with([
    [['--no-ssr' => true, '--ssr' => true]],
    [['--no-ssr' => true, '--inertia-ssr-entry' => 'resources/js/ssr.ts']],
    [['--inertia-ssr-entry' => 'resources/js/missing.ts']],
    [['--inertia-entry' => '../outside.js']],
    [['--inertia-entry' => 'resources/js/resolve.js']],
    [['--inertia-pages' => "resources/js/quote'pages"]],
]);

it('offers SSR on first interactive setup', function (bool $server): void {
    $this->artisan('make:filament-page', ['name' => 'Reports', '--panel' => 'admin', '--vue' => true])
        ->expectsConfirmation('Generate an Inertia SSR entry?', $server ? 'yes' : 'no')
        ->assertSuccessful();
    expect(file_exists(resource_path('js/filament/ssr.js')))->toBe($server);
})->with([true, false]);

it('creates only a page and component when the existing renderer has no conventional resolver', function (): void {
    app(Filesystem::class)->ensureDirectoryExists(resource_path('js/admin'));
    file_put_contents(resource_path('js/admin/inertia.js'), '// existing custom renderer');

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--vue' => true, '--ssr' => true,
        '--inertia-entry' => 'resources/js/admin/inertia.js', '--inertia-pages' => 'resources/js/admin/components',
        '--force' => true, '--no-interaction' => true,
    ])->expectsOutputToContain('Custom resolver location is unknown; no resolver or server was generated')->assertSuccessful();

    expect(file_get_contents(resource_path('js/admin/inertia.js')))->toBe('// existing custom renderer');
    expect(file_exists(resource_path('js/admin/components/Reports.vue')))->toBeTrue();
    expect(file_exists(base_path('app/Reports.php')))->toBeTrue();
    expect(app(Filesystem::class)->allFiles($this->inertiaDirectory))->toHaveCount(3);
});

it('preserves existing SSR with `--no-ssr` without pretending to disable it', function (): void {
    app(Filesystem::class)->ensureDirectoryExists(resource_path('js'));
    file_put_contents(resource_path('js/ssr.js'), '// native server');

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--vue' => true, '--no-ssr' => true, '--no-interaction' => true,
    ])->expectsOutputToContain('Preserved existing SSR entry')
        ->expectsOutputToContain("page.component.startsWith('Filament/')")->assertSuccessful();

    expect(file_get_contents(resource_path('js/ssr.js')))->toBe('// native server');
    expect(file_exists(resource_path('js/filament/ssr.js')))->toBeFalse();
});

it('prints an installation command for only the missing released packages', function (): void {
    $this->missingInertiaPackages = [base_path('node_modules/typescript/package.json'), base_path('node_modules/@types/react/package.json')];

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--react' => true, '--ts' => true, '--no-interaction' => true,
    ])->expectsOutputToContain('npm install typescript @types/react')->assertFailed();

    expect(is_dir($this->inertiaDirectory))->toBeFalse();
});

it('uses matching relative glob and lookup specifiers for every component layout', function (string $framework, ?string $directory, string $specifier): void {
    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', "--{$framework}" => true, '--no-ssr' => true, '--no-interaction' => true,
        ...(($directory !== null) ? ['--inertia-pages' => $directory] : []),
    ])->assertSuccessful();

    $resolver = file_get_contents(resource_path('js/filament/resolve.js'));
    expect($resolver)->toContain("import.meta.glob('{$specifier}/**/*", '`' . $specifier . '/${');
})->with(['react', 'vue', 'svelte'])->with([
    [null, './pages'],
    ['resources/js/filament/components', './components'],
    ['resources/js/components', '../components'],
    ['resources/js/filament', '.'],
    ['resources/js', '..'],
    ['resources', '../..'],
]);

it('rejects file and directory conflicts before replacing any page with `--force`', function (string $entry, string $pages, string $fixture, bool $isDirectory): void {
    $filesystem = app(Filesystem::class);
    $filesystem->ensureDirectoryExists(base_path('app'));
    file_put_contents(base_path('app/Reports.php'), 'original PHP page');
    if ($isDirectory) {
        $filesystem->ensureDirectoryExists(base_path($fixture));
    } else {
        $filesystem->ensureDirectoryExists(dirname(base_path($fixture)));
        file_put_contents(base_path($fixture), 'original source');
    }
    $snapshot = static fn (): array => collect(app(Filesystem::class)->allFiles(base_path()))
        ->mapWithKeys(static fn ($file): array => [$file->getPathname() => $file->getContents()])->all();
    $before = $snapshot();

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--vue' => true, '--force' => true, '--no-ssr' => true, '--no-interaction' => true,
        '--inertia-entry' => $entry, '--inertia-pages' => $pages,
    ])->assertFailed();

    expect($snapshot())->toBe($before);
})->with([
    ['resources/js/filament/inertia.js', 'resources/js/app.js', 'resources/js/app.js', false],
    ['resources/js/filament/inertia.js', 'resources/js/app.js/nested', 'resources/js/app.js', false],
    ['resources/js/filament/inertia.js', 'resources/js/filament/inertia.js', 'resources/js/existing.js', false],
    ['resources/js/filament/inertia.js', 'resources/js/filament/pages', 'resources/js/filament/inertia.js', true],
    ['resources/js/filament/inertia.js', 'resources/js/filament/pages', 'resources/js/filament/pages/Reports.vue', true],
]);

it('does not overwrite an SSR entry that is also the component destination with `--force`', function (): void {
    app(Filesystem::class)->ensureDirectoryExists(resource_path('server'));
    file_put_contents(resource_path('server/Reports.tsx'), '// original SSR');

    $this->artisan('make:filament-page', [
        'name' => 'Reports', '--panel' => 'admin', '--react' => true, '--ts' => true, '--force' => true, '--no-interaction' => true,
        '--inertia-pages' => 'resources/server', '--inertia-ssr-entry' => 'resources/server/Reports.tsx',
    ])->expectsOutputToContain('--force cannot overwrite shared files')->assertFailed();

    expect(file_get_contents(resource_path('server/Reports.tsx')))->toBe('// original SSR');
    expect(app(Filesystem::class)->allFiles(base_path()))->toHaveCount(1);
});

it('uses a relative import specifier when the helper is below the entry directory', function (): void {
    $command = app(MakePageCommand::class);
    expect(invade($command)->getInertiaImportPath('/app/vendor/filament/filament/resources/js/inertia/vue.js', '/app'))
        ->toBe('./vendor/filament/filament/resources/js/inertia/vue.js');
});
