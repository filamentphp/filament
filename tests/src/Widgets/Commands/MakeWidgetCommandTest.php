<?php

use Filament\Tests\TestCase;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class)->group('serial');

beforeEach(function (): void {
    Process::fake(['*' => Process::result(output: '10.0.0')]);
    Process::preventStrayProcesses();

    $this->originalSetupFiles = [];

    foreach (['vite.config.js', 'node_modules/vite/package.json', 'tsconfig.json'] as $path) {
        $this->originalSetupFiles[$path] = File::exists(base_path($path)) ? File::get(base_path($path)) : null;
    }
});

afterEach(function (): void {
    foreach ($this->originalSetupFiles as $path => $contents) {
        if ($contents === null) {
            File::delete(base_path($path));
        } else {
            File::put(base_path($path), $contents);
        }
    }
});

it('can generate a custom widget class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-widget', [
        'name' => 'CustomWidget',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Which type of widget would you like to create?', 'Filament\\Widgets\\Widget')
        ->expectsQuestion('Would you like to create this widget in a resource?', false);

    assertFileExists($path = app_path('Filament/Widgets/CustomWidget.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a custom widget view', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-widget', [
        'name' => 'CustomWidget',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Which type of widget would you like to create?', 'Filament\\Widgets\\Widget')
        ->expectsQuestion('Would you like to create this widget in a resource?', false);

    assertFileExists($path = resource_path('views/filament/widgets/custom-widget.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can run `make:filament-widget` non-interactively to generate a custom widget', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-widget', [
        'name' => 'NonInteractiveWidget',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists(app_path('Filament/Widgets/NonInteractiveWidget.php'));
});

it('can generate a chart widget class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-widget', [
        'name' => 'ChartWidget',
        '--chart' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Would you like to create this widget in a resource?', false)
        ->expectsQuestion('Which type of chart would you like to create?', 'line');

    assertFileExists($path = app_path('Filament/Widgets/ChartWidget.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a stats overview widget class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-widget', [
        'name' => 'StatsOverviewWidget',
        '--stats-overview' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Would you like to create this widget in a resource?', false);

    assertFileExists($path = app_path('Filament/Widgets/StatsOverviewWidget.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a table widget class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-widget', [
        'name' => 'TableWidget',
        '--table' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Would you like to create this widget in a resource?', false)
        ->expectsQuestion('What is the model?', app()->getNamespace() . 'Models\\User')
        ->expectsQuestion('Should the table columns be generated from the current database columns?', false);

    assertFileExists($path = app_path('Filament/Widgets/TableWidget.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a widget class in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-widget', [
        'name' => 'Custom/NestedWidget',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Which type of widget would you like to create?', 'Filament\\Widgets\\Widget')
        ->expectsQuestion('Would you like to create this widget in a resource?', false);

    assertFileExists($path = app_path('Filament/Widgets/Custom/NestedWidget.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a widget view in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->mockConsoleOutput = true;

    $this->artisan('make:filament-widget', [
        'name' => 'Custom/NestedWidget',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Which type of widget would you like to create?', 'Filament\\Widgets\\Widget')
        ->expectsQuestion('Would you like to create this widget in a resource?', false);

    assertFileExists($path = resource_path('views/filament/widgets/custom/nested-widget.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate JavaScript widget renderers', function (string $framework, bool $isTypeScript, string $extension, array $additionalFiles): void {
    $arguments = [
        'name' => 'RenderedWidget',
        '--panel' => 'admin',
        "--{$framework}" => true,
        '--skip-install' => true,
        '--skip-build' => true,
        '--no-interaction' => true,
    ];

    if ($isTypeScript) {
        $arguments['--typescript'] = true;
    }

    $this->artisan('make:filament-widget', $arguments)
        ->expectsQuestion('Would you like to create this widget in a resource?', false)
        ->assertSuccessful();

    $classPath = app_path('Filament/Widgets/RenderedWidget.php');
    assertFileExists($classPath);
    expect(file_get_contents($classPath))
        ->toContain('use HasJsRenderer;')
        ->toContain("return Vite::asset('resources/js/filament/widgets/rendered-widget.{$extension}');")
        ->not->toContain('$view');
    assertFileExists(resource_path("js/filament/widgets/rendered-widget.{$extension}"));
    foreach ($additionalFiles as $additionalFile) {
        assertFileExists(resource_path("js/filament/widgets/{$additionalFile}"));
    }
})->with([
    'JavaScript' => ['js', false, 'js', []],
    'JavaScript with TypeScript' => ['js', true, 'ts', []],
    'React' => ['react', false, 'jsx', []],
    'React with TypeScript' => ['react', true, 'tsx', []],
    'Vue' => ['vue', false, 'js', ['RenderedWidget.vue']],
    'Vue with TypeScript' => ['vue', true, 'ts', ['RenderedWidget.vue']],
    'Svelte' => ['svelte', false, 'svelte.js', ['RenderedWidget.svelte']],
    'Svelte with TypeScript' => ['svelte', true, 'svelte.ts', ['RenderedWidget.svelte']],
]);

it('rejects incompatible JavaScript widget renderer options', function (array $options): void {
    $this->artisan('make:filament-widget', [
        'name' => 'InvalidWidget',
        '--panel' => 'admin',
        '--no-interaction' => true,
        ...$options,
    ])->assertFailed();
})->with([
    'multiple frameworks' => [['--react' => true, '--vue' => true]],
    'TypeScript without a framework' => [['--typescript' => true]],
    'chart renderer' => [['--js' => true, '--chart' => true]],
    'stats renderer' => [['--js' => true, '--stats-overview' => true]],
    'table renderer' => [['--js' => true, '--table' => true]],
]);

it('preserves existing compiler imports and TypeScript aliases when configuring a widget', function (string $framework, string $import): void {
    File::put(base_path('vite.config.js'), $import . "\nexport default defineConfig({ plugins: [laravel({ input: ['resources/js/app.js'] }), frameworkPlugin()] })");
    File::put(base_path('tsconfig.json'), json_encode([
        'compilerOptions' => ['baseUrl' => 'resources/js', 'paths' => ['@/*' => ['./*']]],
    ]));

    $this->artisan('make:filament-widget', [
        'name' => 'Reports/RevenueOverview',
        '--panel' => 'admin',
        "--{$framework}" => true,
        '--ts' => true,
        '--skip-install' => true,
        '--skip-build' => true,
        '--no-interaction' => true,
    ])
        ->expectsQuestion('Would you like to create this widget in a resource?', false)
        ->assertSuccessful();

    expect(File::get(base_path('vite.config.js')))
        ->toContain($import, "preserveEntrySignatures: 'exports-only'", 'resources/js/filament/widgets/reports/revenue-overview.')
        ->not->toContain('filamentVue', 'filamentSvelte');
    expect(File::json(base_path('tsconfig.json')))
        ->toHaveKey('compilerOptions.paths.@/*', ['./*'])
        ->toHaveKey('compilerOptions.paths.@filament/widgets/js-widget', ['./../../vendor/filament/widgets/resources/js/types/js-widget.d.ts']);
    Process::assertNothingRan();
})->with([
    ['vue', "import frameworkPlugin from '@vitejs/plugin-vue'"],
    ['svelte', "import { svelte as frameworkPlugin } from '@sveltejs/vite-plugin-svelte'"],
]);

it('preserves renderer collisions unless `--force` is specified', function (): void {
    $path = resource_path('js/filament/widgets/existing-widget.js');
    File::ensureDirectoryExists(dirname($path));
    File::put($path, 'Existing renderer');
    $arguments = [
        'name' => 'ExistingWidget', '--panel' => 'admin', '--js' => true,
        '--skip-install' => true, '--skip-build' => true, '--no-interaction' => true,
    ];

    File::delete(app_path('Filament/Widgets/ExistingWidget.php'));
    $environment = $this->app['env'];
    $this->app['env'] = 'production';

    try {
        $this->artisan('make:filament-widget', $arguments)
            ->expectsQuestion('Would you like to create this widget in a resource?', false)
            ->expectsConfirmation('existing-widget.js already exists, do you want to overwrite it?', 'no')
            ->assertFailed();
    } finally {
        $this->app['env'] = $environment;
    }

    expect(File::get($path))->toBe('Existing renderer');
    $this->artisan('make:filament-widget', [...$arguments, '--force' => true])
        ->expectsQuestion('Would you like to create this widget in a resource?', false)
        ->assertSuccessful();
    expect(File::get($path))->toContain('export default function mountExistingWidget');
});

it('installs typed React widget dependencies with the chosen package manager and reports build failures', function (): void {
    File::delete(base_path('tsconfig.json'));
    File::put(base_path('vite.config.js'), "export default defineConfig({ plugins: [laravel({ input: ['resources/js/app.js'] })] })");
    Process::fake(static fn (PendingProcess $process) => Process::result(exitCode: ($process->command === ['yarn', 'run', 'build']) ? 1 : 0));

    $this->artisan('make:filament-widget', [
        'name' => 'TypedReactWidget', '--panel' => 'admin', '--react' => true, '--typescript' => true,
        '--pm' => 'yarn', '--no-interaction' => true,
    ])
        ->expectsQuestion('Would you like to create this widget in a resource?', false)
        ->expectsConfirmation('Would you like to compile the widget now?', 'yes')
        ->assertFailed();

    expect(File::json(base_path('tsconfig.json')))->toHaveKey('compilerOptions.jsx', 'react-jsx');
    Process::assertRan(static fn (PendingProcess $process): bool => $process->command === ['yarn', 'add', 'react', 'react-dom', 'typescript@^6.0', '@types/react', '@types/react-dom', '--dev']);
    Process::assertRan(static fn (PendingProcess $process): bool => $process->command === ['yarn', 'run', 'build']);
});
