<?php

use Filament\Tests\TestCase;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

uses(TestCase::class)->group('serial');

beforeEach(function (): void {
    Process::fake(['*' => Process::result(output: '10.0.0')]);
    Process::preventStrayProcesses();
    $this->originalSetupFiles = [];

    foreach (['vite.config.js', 'tsconfig.json', 'package.json', 'node_modules/vite/package.json'] as $path) {
        $this->originalSetupFiles[$path] = File::exists(base_path($path)) ? File::get(base_path($path)) : null;
    }

    File::put(base_path('package.json'), '{}');
    File::ensureDirectoryExists(base_path('node_modules/vite'));
    File::put(base_path('node_modules/vite/package.json'), '{"version":"8.0.0"}');
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

it('generates JavaScript components with framework and TypeScript options', function (string $framework, string $extension, bool $isTypeScript): void {
    File::delete(base_path('tsconfig.json'));
    $extension = $isTypeScript ? str_replace(['jsx', 'js'], ['tsx', 'ts'], $extension) : $extension;
    $this->artisan('make:filament-schema-component', [
        'name' => 'SalesChart',
        "--{$framework}" => true,
        '--ts' => $isTypeScript,
        '--skip-install' => true,
        '--skip-build' => true,
        '--no-interaction' => true,
    ])->assertSuccessful();

    $entry = "resources/js/filament/schemas/components/sales-chart.{$extension}";
    expect(File::get(app_path('Filament/Schemas/Components/SalesChart.php')))
        ->toContain('extends Component', 'use HasJsRenderer;', "Vite::asset('{$entry}')", 'public static function make(): static')
        ->not->toContain('protected string $view', 'Filament\\Forms');
    expect(File::exists(base_path($entry)))->toBeTrue();
    expect(File::exists(resource_path('views/filament/schemas/components/sales-chart.blade.php')))->toBeFalse();
    if (in_array($framework, ['vue', 'svelte'])) {
        expect(File::get(resource_path("js/filament/schemas/components/SalesChart.{$framework}")))
            ->toContain('configuration.message')->not->toContain('<input', 'onChange');
    }
    if ($isTypeScript) {
        expect(File::json(base_path('tsconfig.json')))
            ->toHaveKey('compilerOptions.jsx', 'react-jsx')
            ->toHaveKey('compilerOptions.paths.@filament/schemas/js-component', ['./vendor/filament/schemas/resources/js/types/js-component.d.ts']);
    }
    Process::assertNothingRan();
})->with([['js', 'js'], ['react', 'jsx'], ['vue', 'js'], ['svelte', 'svelte.js']])->with([false, true]);

it('generates a nested two-file renderer', function (): void {
    $this->artisan('make:filament-schema-component', [
        'name' => 'Reports/SalesChart',
        '--vue' => true,
        '--skip-install' => true,
        '--skip-build' => true,
        '--no-interaction' => true,
    ])->assertSuccessful();

    expect(File::get(app_path('Filament/Schemas/Components/Reports/SalesChart.php')))
        ->toContain("Vite::asset('resources/js/filament/schemas/components/reports/sales-chart.js')");
    expect(File::get(resource_path('js/filament/schemas/components/reports/sales-chart.js')))
        ->toContain("from './SalesChart.vue'");
    expect(File::exists(resource_path('js/filament/schemas/components/reports/SalesChart.vue')))->toBeTrue();
});

it('rejects conflicting frameworks and TypeScript without a renderer', function (array $options): void {
    $this->artisan('make:filament-schema-component', ['name' => 'InvalidChart', '--no-interaction' => true, ...$options])->assertFailed();
    expect(File::exists(app_path('Filament/Schemas/Components/InvalidChart.php')))->toBeFalse();
    Process::assertNothingRan();
})->with([[['--react' => true, '--vue' => true]], [['--typescript' => true]]]);

it('installs dependencies and configures Vite for a typed React component', function (): void {
    File::put(base_path('vite.config.js'), file_get_contents(__DIR__ . '/../../Panels/Commands/Fixture/MakeThemeCommandTest/vite-config/standard.js'));
    $this->artisan('make:filament-schema-component', ['name' => 'SalesChart', '--react' => true, '--typescript' => true, '--pm' => 'yarn', '--no-interaction' => true])
        ->expectsConfirmation('Would you like to compile the component now?', 'yes')
        ->assertSuccessful();

    Process::assertRan(static fn (PendingProcess $process): bool => $process->command === ['yarn', 'add', 'react@^19.0', 'react-dom@^19.0', 'typescript@^6.0', '@types/react@^19.0', '@types/react-dom@^19.0', '--dev']);
    expect(File::get(base_path('vite.config.js')))->toContain('preserveEntrySignatures', 'resources/js/filament/schemas/components/sales-chart.tsx');
});

it('preserves customized build and TypeScript configurations with setup instructions', function (): void {
    $configuration = 'export default defineConfig({ plugins: customPlugins, build: { minify: false } })';
    File::put(base_path('vite.config.js'), $configuration);
    File::put(base_path('tsconfig.json'), '{ "extends": "./base.json" }');
    $this->artisan('make:filament-schema-component', ['name' => 'ManualChart', '--vue' => true, '--ts' => true, '--skip-install' => true, '--skip-build' => true, '--no-interaction' => true])
        ->expectsOutputToContain('Configure the @filament/schemas/js-component type alias')
        ->expectsOutputToContain('Action is required to complete the component setup:')
        ->assertSuccessful();
    expect(File::get(base_path('vite.config.js')))->toBe($configuration);
    expect(File::get(base_path('tsconfig.json')))->toBe('{ "extends": "./base.json" }');
});

it('installs the JavaScript compiler API required by typed framework components', function (string $framework): void {
    $this->artisan('make:filament-schema-component', ['name' => 'TypedSummary', "--{$framework}" => true, '--ts' => true, '--skip-build' => true, '--no-interaction' => true])
        ->assertSuccessful();

    Process::assertRan(static fn (PendingProcess $process): bool => is_array($process->command) && in_array('typescript@^6.0', $process->command, strict: true));
})->with(['js', 'vue', 'svelte']);

it('preserves an existing JSX transform when adding the renderer type alias', function (): void {
    File::put(base_path('tsconfig.json'), json_encode(['compilerOptions' => ['jsx' => 'preserve']]));

    $this->artisan('make:filament-schema-component', ['name' => 'CustomJsxSummary', '--react' => true, '--ts' => true, '--skip-install' => true, '--skip-build' => true, '--no-interaction' => true])
        ->assertSuccessful();

    expect(File::json(base_path('tsconfig.json')))
        ->toHaveKey('compilerOptions.jsx', 'preserve')
        ->toHaveKey('compilerOptions.paths.@filament/schemas/js-component');
});

it('overwrites existing renderers with `--force`', function (): void {
    $path = resource_path('js/filament/schemas/components/existing-chart.js');
    File::ensureDirectoryExists(dirname($path));
    File::put($path, '// Existing renderer');
    $options = ['name' => 'ExistingChart', '--js' => true, '--skip-install' => true, '--skip-build' => true, '--no-interaction' => true];

    $this->artisan('make:filament-schema-component', [...$options, '--force' => true])->assertSuccessful();
    expect(File::get($path))->toContain('export default function mountExistingChart');
});

it('preserves every component file without installing dependencies when an overwrite is declined', function (): void {
    $paths = [
        app_path('Filament/Schemas/Components/CancelledChart.php'),
        resource_path('js/filament/schemas/components/cancelled-chart.js'),
        resource_path('js/filament/schemas/components/CancelledChart.vue'),
    ];

    foreach ($paths as $path) {
        File::ensureDirectoryExists(dirname($path));
        File::put($path, 'Original ' . basename($path));
    }

    $environment = app()['env'];

    try {
        app()['env'] = 'local';

        $this->artisan('make:filament-schema-component', [
            'name' => 'CancelledChart',
            '--vue' => true,
            '--skip-build' => true,
        ])
            ->expectsConfirmation('CancelledChart.php already exists, do you want to overwrite it?', 'yes')
            ->expectsConfirmation('cancelled-chart.js already exists, do you want to overwrite it?', 'yes')
            ->expectsConfirmation('CancelledChart.vue already exists, do you want to overwrite it?', 'no')
            ->assertFailed();

        foreach ($paths as $path) {
            expect(File::get($path))->toBe('Original ' . basename($path));
        }

        Process::assertNothingRan();
    } finally {
        app()['env'] = $environment;
        File::delete($paths);
    }
});

it('preserves approved component files when dependency installation fails', function (): void {
    $paths = [
        app_path('Filament/Schemas/Components/FailedInstallChart.php'),
        resource_path('js/filament/schemas/components/failed-install-chart.js'),
        resource_path('js/filament/schemas/components/FailedInstallChart.vue'),
    ];

    foreach ($paths as $path) {
        File::ensureDirectoryExists(dirname($path));
        File::put($path, 'Original ' . basename($path));
    }

    Process::fake(static fn (PendingProcess $process) => Process::result(exitCode: ($process->command === ['npm', '--version']) ? 0 : 1));
    $environment = app()['env'];

    try {
        app()['env'] = 'local';

        $this->artisan('make:filament-schema-component', [
            'name' => 'FailedInstallChart',
            '--vue' => true,
            '--skip-build' => true,
        ])
            ->expectsConfirmation('FailedInstallChart.php already exists, do you want to overwrite it?', 'yes')
            ->expectsConfirmation('failed-install-chart.js already exists, do you want to overwrite it?', 'yes')
            ->expectsConfirmation('FailedInstallChart.vue already exists, do you want to overwrite it?', 'yes')
            ->expectsOutputToContain('Failed to install JavaScript dependencies.')
            ->assertFailed();

        foreach ($paths as $path) {
            expect(File::get($path))->toBe('Original ' . basename($path));
        }

        Process::assertRan(static fn (PendingProcess $process): bool => in_array('install', $process->command));
    } finally {
        app()['env'] = $environment;
        File::delete($paths);
    }
});

it('requires installed Vite before selecting renderer dependencies', function (bool $skipInstall): void {
    File::delete(base_path('node_modules/vite/package.json'));
    $componentPath = app_path('Filament/Schemas/Components/UninstalledViteChart.php');
    $rendererPath = resource_path('js/filament/schemas/components/uninstalled-vite-chart.svelte.js');
    File::delete([$componentPath, $rendererPath]);

    $this->artisan('make:filament-schema-component', [
        'name' => 'UninstalledViteChart',
        '--svelte' => true,
        '--skip-install' => $skipInstall,
        '--skip-build' => true,
        '--no-interaction' => true,
    ])
        ->expectsOutputToContain('Install your application\'s existing JavaScript dependencies, including Vite, before generating a JavaScript renderer.')
        ->assertFailed();

    expect(File::exists($componentPath))->toBeFalse();
    expect(File::exists($rendererPath))->toBeFalse();
    Process::assertNotRan(static fn (PendingProcess $process): bool => in_array('install', $process->command));
})->with([false, true]);

it('returns failure when compiling the generated component fails', function (): void {
    File::put(base_path('vite.config.js'), file_get_contents(__DIR__ . '/../../Panels/Commands/Fixture/MakeThemeCommandTest/vite-config/standard.js'));
    Process::fake(static fn (PendingProcess $process) => Process::result(exitCode: ($process->command === ['npm', 'run', 'build']) ? 1 : 0));

    $this->artisan('make:filament-schema-component', ['name' => 'FailedChart', '--react' => true, '--no-interaction' => true])
        ->expectsConfirmation('Would you like to compile the component now?', 'yes')
        ->expectsOutputToContain('Failed to compile the component.')
        ->assertFailed();
});
