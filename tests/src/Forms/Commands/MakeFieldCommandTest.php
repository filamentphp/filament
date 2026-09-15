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

it('can generate a field class', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-form-field', [
        'name' => 'CustomField',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Forms/Components/CustomField.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();

    Process::assertNothingRan();
});

it('can generate a field view', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-form-field', [
        'name' => 'CustomField',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = resource_path('views/filament/forms/components/custom-field.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a field class in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-form-field', [
        'name' => 'Custom/NestedField',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Forms/Components/Custom/NestedField.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a field view in a nested directory', function (): void {
    $this->withoutMockingConsoleOutput();

    $this->artisan('make:filament-form-field', [
        'name' => 'Custom/NestedField',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = resource_path('views/filament/forms/components/custom/nested-field.blade.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a JavaScript field without a Blade view', function (string $framework, string $extension, ?string $typeScriptOption, string $name, string $directory, string $basename): void {
    $viewPath = resource_path("views/filament/forms/components/{$directory}{$basename}.blade.php");
    File::delete($viewPath);

    $options = [
        'name' => $name,
        "--{$framework}" => true,
        '--no-interaction' => true,
        '--skip-install' => true,
        '--skip-build' => true,
    ];

    if ($typeScriptOption) {
        $options["--{$typeScriptOption}"] = true;
    }

    $this->artisan('make:filament-form-field', $options)->assertSuccessful();

    $renderer = "resources/js/filament/forms/components/{$directory}{$basename}.{$extension}";
    $class = file_get_contents(app_path("Filament/Forms/Components/{$name}.php"));

    expect($class)
        ->toContain('extends Field', 'use HasJsRenderer;', "return Vite::asset('{$renderer}');")
        ->not->toContain('HasEmbeddedView', 'protected string $view');
    expect(file_exists($viewPath))->toBeFalse();

    $componentName = basename($name);
    $stubPrefix = ucfirst($framework) . ($typeScriptOption ? 'TypeScript' : '');
    expect(file_get_contents(base_path($renderer)))
        ->toBe(str_replace('{{ componentName }}', $componentName, file_get_contents(dirname(__DIR__, 4) . "/packages/forms/stubs/{$stubPrefix}FieldRenderer.stub")));

    if (in_array($framework, ['vue', 'svelte'])) {
        expect(file_get_contents(resource_path("js/filament/forms/components/{$directory}{$componentName}.{$framework}")))
            ->toBe(file_get_contents(dirname(__DIR__, 4) . "/packages/forms/stubs/{$stubPrefix}FieldComponent.stub"));
    }
})->with([
    'JavaScript' => ['js', 'js', null],
    'React' => ['react', 'jsx', null],
    'Vue' => ['vue', 'js', null],
    'Svelte' => ['svelte', 'svelte.js', null],
    'JavaScript with `--typescript`' => ['js', 'ts', 'typescript'],
    'React with `--ts`' => ['react', 'tsx', 'ts'],
    'Vue with `--typescript`' => ['vue', 'ts', 'typescript'],
    'Svelte with `--ts`' => ['svelte', 'svelte.ts', 'ts'],
])->with([
    'root' => ['GeneratedField', '', 'generated-field'],
    'nested' => ['Custom/NestedField', 'custom/', 'nested-field'],
]);

it('rejects conflicting framework flags before writing files', function (array $options): void {
    $this->artisan('make:filament-form-field', [
        'name' => 'ConflictingField',
        '--no-interaction' => true,
        ...$options,
    ])
        ->expectsOutputToContain('Only one of --js, --react, --vue, or --svelte may be specified.')
        ->assertFailed();

    expect(file_exists(app_path('Filament/Forms/Components/ConflictingField.php')))->toBeFalse();
})->with([
    [['--react' => true, '--vue' => true]],
    [['--react' => true, '--svelte' => true]],
    [['--vue' => true, '--svelte' => true]],
    [['--js' => true, '--react' => true]],
    [['--js' => true, '--vue' => true]],
    [['--js' => true, '--svelte' => true]],
]);

it('rejects TypeScript flags without a renderer before writing files', function (string $option): void {
    $this->artisan('make:filament-form-field', [
        'name' => 'ConflictingField',
        "--{$option}" => true,
        '--no-interaction' => true,
    ])
        ->expectsOutputToContain('Use --typescript or --ts with --js, --react, --vue, or --svelte.')
        ->assertFailed();

    expect(File::exists(app_path('Filament/Forms/Components/ConflictingField.php')))->toBeFalse();
})->with(['typescript', 'ts']);

it('can overwrite all generated Vue files with `--force`', function (): void {
    $paths = [
        app_path('Filament/Forms/Components/GeneratedField.php'),
        resource_path('js/filament/forms/components/generated-field.js'),
        resource_path('js/filament/forms/components/GeneratedField.vue'),
    ];

    foreach ($paths as $path) {
        File::ensureDirectoryExists(dirname($path));
        File::put($path, 'Existing contents');
    }

    $this->artisan('make:filament-form-field', [
        'name' => 'GeneratedField',
        '--vue' => true,
        '--force' => true,
        '--no-interaction' => true,
    ])->assertSuccessful();

    foreach ($paths as $path) {
        expect(file_get_contents($path))->not->toContain('Existing contents');
    }
});

it('installs dependencies and configures Vite for a JavaScript field without duplicating setup', function (string $framework, string $extension, array $dependencies, ?string $plugin, ?string $typeScriptOption): void {
    File::put(base_path('vite.config.js'), file_get_contents(__DIR__ . '/../../Panels/Commands/Fixture/MakeThemeCommandTest/vite-config/standard.js'));
    File::ensureDirectoryExists(base_path('node_modules/vite'));
    File::put(base_path('node_modules/vite/package.json'), '{"version":"8.3.0"}');

    $arguments = ['name' => 'SetupField', "--{$framework}" => true, '--no-interaction' => true];

    if ($typeScriptOption) {
        $arguments["--{$typeScriptOption}"] = true;
    }
    $this->artisan('make:filament-form-field', $arguments)
        ->expectsConfirmation('Would you like to compile the field now?', 'yes')
        ->assertSuccessful();

    $config = File::get(base_path('vite.config.js'));
    expect($config)
        ->toContain("resources/js/filament/forms/components/setup-field.{$extension}")
        ->toContain("rolldownOptions: { preserveEntrySignatures: 'exports-only' }")
        ->toContain('refresh: true');

    if ($plugin) {
        expect($config)->toContain("{$plugin}()");
    } else {
        expect($config)->not->toContain('@vitejs/plugin-react');
    }

    if ($dependencies === []) {
        Process::assertDidntRun(static fn (PendingProcess $process): bool => str_contains(implode(' ', $process->command), 'install'));
    } else {
        Process::assertRan(static fn (PendingProcess $process): bool => $process->command === ['npm', 'install', ...$dependencies, '--save-dev']);
    }
    Process::assertRan(static fn (PendingProcess $process): bool => $process->command === ['npm', 'run', 'build']);

    $this->artisan('make:filament-form-field', $arguments)
        ->expectsConfirmation('Would you like to compile the field now?', 'yes')
        ->assertSuccessful();
    expect(File::get(base_path('vite.config.js')))->toBe($config);
})->with([
    ['js', 'js', [], null, null],
    ['react', 'jsx', ['react', 'react-dom'], null, null],
    ['vue', 'js', ['vue', '@vitejs/plugin-vue'], 'filamentVue', null],
    ['svelte', 'svelte.js', ['svelte', '@sveltejs/vite-plugin-svelte'], 'filamentSvelte', null],
    ['js', 'ts', ['typescript'], null, 'typescript'],
    ['react', 'tsx', ['react', 'react-dom', 'typescript', '@types/react', '@types/react-dom'], null, 'ts'],
    ['vue', 'ts', ['vue', '@vitejs/plugin-vue', 'typescript'], 'filamentVue', 'typescript'],
    ['svelte', 'svelte.ts', ['svelte', '@sveltejs/vite-plugin-svelte', 'typescript'], 'filamentSvelte', 'ts'],
]);

it('does not run processes when installation and building are skipped', function (): void {
    $this->artisan('make:filament-form-field', [
        'name' => 'SkippedProcessesField',
        '--react' => true,
        '--typescript' => true,
        '--skip-install' => true,
        '--skip-build' => true,
        '--no-interaction' => true,
    ])->assertSuccessful();

    Process::assertNothingRan();
});

it('creates a TypeScript configuration for typed renderers', function (): void {
    File::delete(base_path('tsconfig.json'));

    $this->artisan('make:filament-form-field', [
        'name' => 'TypedField',
        '--js' => true,
        '--typescript' => true,
        '--skip-install' => true,
        '--skip-build' => true,
        '--no-interaction' => true,
    ])->assertSuccessful();

    $configuration = File::json(base_path('tsconfig.json'));
    expect($configuration)
        ->toHaveKey('compilerOptions.target', 'ES2020')
        ->toHaveKey('compilerOptions.paths.@filament/forms/js-field', ['./vendor/filament/forms/resources/js/types/js-field.d.ts'])
        ->toHaveKey('include', ['resources/js/**/*']);
});

it('merges the field type alias into TypeScript configuration while preserving aliases and `baseUrl`', function (): void {
    File::put(base_path('tsconfig.json'), json_encode([
        'compilerOptions' => [
            'baseUrl' => 'resources/js',
            'paths' => ['@/*' => ['./*']],
        ],
        'include' => ['resources/js/app.ts'],
    ], JSON_PRETTY_PRINT));

    $this->artisan('make:filament-form-field', [
        'name' => 'MergedTypedField',
        '--vue' => true,
        '--ts' => true,
        '--skip-install' => true,
        '--skip-build' => true,
        '--no-interaction' => true,
    ])->assertSuccessful();

    expect(File::json(base_path('tsconfig.json')))
        ->toHaveKey('compilerOptions.baseUrl', 'resources/js')
        ->toHaveKey('compilerOptions.paths.@/*', ['./*'])
        ->toHaveKey('compilerOptions.paths.@filament/forms/js-field', ['./../../vendor/filament/forms/resources/js/types/js-field.d.ts'])
        ->toHaveKey('include', ['resources/js/app.ts']);
});

it('leaves TypeScript configurations requiring manual setup unchanged', function (string $configuration): void {
    File::put(base_path('tsconfig.json'), $configuration);

    $this->artisan('make:filament-form-field', [
        'name' => 'ManualTypedField',
        '--svelte' => true,
        '--typescript' => true,
        '--skip-install' => true,
        '--skip-build' => true,
        '--no-interaction' => true,
    ])
        ->expectsOutputToContain('Configure the @filament/forms/js-field type alias in tsconfig.json')
        ->assertSuccessful();

    expect(File::get(base_path('tsconfig.json')))->toBe($configuration);
})->with([
    'JSONC' => ["{\n    // Shared TypeScript settings.\n    \"compilerOptions\": {}\n}\n"],
    'inherited configuration' => [json_encode(['extends' => './tsconfig.base.json', 'compilerOptions' => []], JSON_PRETTY_PRINT)],
]);

it('uses a Svelte compiler compatible with the installed Vite version', function (string $version, string $plugin): void {
    File::put(base_path('vite.config.js'), file_get_contents(__DIR__ . '/../../Panels/Commands/Fixture/MakeThemeCommandTest/vite-config/standard.js'));
    File::ensureDirectoryExists(base_path('node_modules/vite'));
    File::put(base_path('node_modules/vite/package.json'), json_encode(['version' => $version]));

    $this->artisan('make:filament-form-field', ['name' => 'SvelteField', '--svelte' => true, '--pm' => 'yarn', '--no-interaction' => true])
        ->expectsConfirmation('Would you like to compile the field now?', 'yes')
        ->assertSuccessful();

    Process::assertRan(static fn (PendingProcess $process): bool => $process->command === ['yarn', 'add', 'svelte', $plugin, '--dev']);
    expect(File::get(base_path('vite.config.js')))->toContain("rollupOptions: { preserveEntrySignatures: 'exports-only' }");
})->with([
    ['5.4.0', '@sveltejs/vite-plugin-svelte@^4.0'],
    ['6.2.0', '@sveltejs/vite-plugin-svelte@^5.0'],
    ['6.3.0', '@sveltejs/vite-plugin-svelte@^6.0'],
    ['7.0.0', '@sveltejs/vite-plugin-svelte@^6.0'],
]);

it('preserves customized Vite configuration and reports the remaining setup', function (): void {
    $config = 'export default defineConfig({ plugins: customPlugins, build: { minify: false } })';
    File::put(base_path('vite.config.js'), $config);

    $this->artisan('make:filament-form-field', ['name' => 'ManualField', '--vue' => true, '--no-interaction' => true])
        ->expectsOutputToContain('Action is required to complete the field setup:')
        ->expectsConfirmation('Would you like to compile the field now?', 'no')
        ->assertSuccessful();

    expect(File::get(base_path('vite.config.js')))->toBe($config);
});

it('does not generate files when dependency installation fails', function (): void {
    Process::fake(static fn (PendingProcess $process) => Process::result(exitCode: ($process->command === ['npm', '--version']) ? 0 : 1));

    $this->artisan('make:filament-form-field', ['name' => 'FailedInstallField', '--react' => true, '--no-interaction' => true])
        ->expectsOutputToContain('Failed to install JavaScript dependencies.')
        ->assertFailed();

    expect(File::exists(app_path('Filament/Forms/Components/FailedInstallField.php')))->toBeFalse();
});

it('reuses framework plugin imports without confusing calls outside the plugin array', function (string $framework, string $import, bool $isRegistered): void {
    $registration = $isRegistered ? 'frameworkPlugin(),' : '';
    File::put(base_path('vite.config.js'), <<<JS
        import { defineConfig } from 'vite'
        import laravel from 'laravel-vite-plugin'
        {$import}
        const unused = frameworkPlugin()
        export default defineConfig({
            plugins: [laravel({ input: ['resources/js/app.js'] }), {$registration}],
            build: { rollupOptions: { preserveEntrySignatures: 'strict' } },
        })
        JS);

    $this->artisan('make:filament-form-field', ['name' => 'ExistingPluginField', "--{$framework}" => true, '--no-interaction' => true])
        ->expectsConfirmation('Would you like to compile the field now?', 'no')
        ->assertSuccessful();

    $contents = File::get(base_path('vite.config.js'));
    expect(substr_count($contents, $import))->toBe(1);
    expect(substr_count($contents, 'frameworkPlugin()'))->toBe(2);
    expect($contents)->toContain("preserveEntrySignatures: 'strict'")->not->toContain('filamentVue', 'filamentSvelte');
})->with([
    ['vue', "import frameworkPlugin from '@vitejs/plugin-vue'"],
    ['svelte', "import { svelte as frameworkPlugin } from '@sveltejs/vite-plugin-svelte'"],
])->with([false, true]);

it('returns failure when compiling the generated field fails', function (): void {
    File::put(base_path('vite.config.js'), file_get_contents(__DIR__ . '/../../Panels/Commands/Fixture/MakeThemeCommandTest/vite-config/standard.js'));
    Process::fake(static fn (PendingProcess $process) => Process::result(exitCode: ($process->command === ['npm', 'run', 'build']) ? 1 : 0));

    $this->artisan('make:filament-form-field', ['name' => 'FailedBuildField', '--react' => true, '--no-interaction' => true])
        ->expectsConfirmation('Would you like to compile the field now?', 'yes')
        ->expectsOutputToContain('Failed to compile the field.')
        ->assertFailed();
});
