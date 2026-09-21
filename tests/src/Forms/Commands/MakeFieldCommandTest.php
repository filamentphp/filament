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

    foreach (['vite.config.js', 'node_modules/vite/package.json', 'tsconfig.json', 'package.json', ...array_map(static fn (string $package): string => "node_modules/{$package}/package.json", ['react', 'react-dom', 'typescript', 'vue', 'svelte', '@sveltejs/vite-plugin-svelte'])] as $path) {
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

it('can generate a JavaScript field without a Blade view', function (string $framework, string $extension, ?string $typeScriptOption, string $name = 'GeneratedField', string $directory = '', string $basename = 'generated-field'): void {
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
    'nested Vue' => ['vue', 'ts', 'ts', 'Custom/NestedField', 'custom/', 'nested-field'],
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
    [['--vue' => true, '--svelte' => true]],
    [['--js' => true, '--react' => true]],
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

it('preserves every field file when a renderer overwrite is declined', function (): void {
    $paths = [
        app_path('Filament/Forms/Components/CancelledField.php'),
        resource_path('js/filament/forms/components/cancelled-field.js'),
        resource_path('js/filament/forms/components/CancelledField.vue'),
    ];

    foreach ($paths as $path) {
        File::ensureDirectoryExists(dirname($path));
        File::put($path, 'Original ' . basename($path));
    }

    $environment = app()['env'];

    try {
        app()['env'] = 'local';

        $this->artisan('make:filament-form-field', [
            'name' => 'CancelledField',
            '--vue' => true,
            '--skip-build' => true,
        ])
            ->expectsConfirmation('CancelledField.php already exists, do you want to overwrite it?', 'yes')
            ->expectsConfirmation('cancelled-field.js already exists, do you want to overwrite it?', 'yes')
            ->expectsConfirmation('CancelledField.vue already exists, do you want to overwrite it?', 'no')
            ->assertFailed();

        Process::assertNothingRan();

        foreach ($paths as $path) {
            expect(File::get($path))->toBe('Original ' . basename($path));
        }
    } finally {
        app()['env'] = $environment;
        File::delete($paths);
    }
});

it('preserves approved field files when dependency installation fails', function (): void {
    $paths = [
        app_path('Filament/Forms/Components/FailedInstallField.php'),
        resource_path('js/filament/forms/components/failed-install-field.js'),
        resource_path('js/filament/forms/components/FailedInstallField.vue'),
    ];

    foreach ($paths as $path) {
        File::ensureDirectoryExists(dirname($path));
        File::put($path, 'Original ' . basename($path));
    }

    Process::fake(static fn (PendingProcess $process) => Process::result(exitCode: ($process->command === ['npm', '--version']) ? 0 : 1));
    $environment = app()['env'];

    try {
        app()['env'] = 'local';

        $this->artisan('make:filament-form-field', [
            'name' => 'FailedInstallField',
            '--vue' => true,
            '--skip-build' => true,
        ])
            ->expectsConfirmation('FailedInstallField.php already exists, do you want to overwrite it?', 'yes')
            ->expectsConfirmation('failed-install-field.js already exists, do you want to overwrite it?', 'yes')
            ->expectsConfirmation('FailedInstallField.vue already exists, do you want to overwrite it?', 'yes')
            ->expectsOutputToContain('Failed to install JavaScript dependencies.')
            ->assertFailed();

        Process::assertRan(static fn (PendingProcess $process): bool => $process->command === ['npm', 'install', 'vue@^3.3', '@vitejs/plugin-vue@^6.0', '--save-dev']);

        foreach ($paths as $path) {
            expect(File::get($path))->toBe('Original ' . basename($path));
        }
    } finally {
        app()['env'] = $environment;
        File::delete($paths);
    }
});

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
        '--skip-build' => true,
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
    ['react', 'jsx', ['react@^19.0', 'react-dom@^19.0'], null, null],
    ['vue', 'js', ['vue@^3.3', '@vitejs/plugin-vue@^6.0'], 'filamentVue', null],
    ['svelte', 'svelte.js', ['svelte@^5.46.4', '@sveltejs/vite-plugin-svelte@^7.0'], 'filamentSvelte', null],
    ['js', 'ts', ['typescript@^6.0'], null, 'typescript'],
    ['react', 'tsx', ['react@^19.0', 'react-dom@^19.0', 'typescript@^6.0', '@types/react@^19.0', '@types/react-dom@^19.0'], null, 'ts'],
    ['vue', 'ts', ['vue@^3.3', '@vitejs/plugin-vue@^6.0', 'typescript@^6.0'], 'filamentVue', 'typescript'],
    ['svelte', 'svelte.ts', ['svelte@^5.46.4', '@sveltejs/vite-plugin-svelte@^7.0', 'typescript@^6.0'], 'filamentSvelte', 'ts'],
]);

it('preserves existing dependencies while installing compatible missing React packages', function (string $packageManager, bool $hasReactDom): void {
    $manifest = json_encode([
        'dependencies' => ['react' => '~18.2.0', ...($hasReactDom ? ['react-dom' => '~18.2.0'] : [])],
        'devDependencies' => ['typescript' => '~5.8.0'],
    ]);
    File::put(base_path('package.json'), $manifest);

    foreach (['react' => '18.2.0', 'react-dom' => '18.2.0', 'typescript' => '5.8.3'] as $package => $version) {
        File::ensureDirectoryExists(base_path("node_modules/{$package}"));
        File::put(base_path("node_modules/{$package}/package.json"), json_encode(['version' => $version]));
    }

    $this->artisan('make:filament-form-field', [
        'name' => 'ExistingReactField',
        '--react' => true,
        '--ts' => true,
        '--pm' => $packageManager,
        '--skip-build' => true,
        '--no-interaction' => true,
    ])->assertSuccessful();

    $missingPackages = [...($hasReactDom ? [] : ['react-dom@18.2.0']), '@types/react@^18.0', '@types/react-dom@^18.0'];
    $expectedCommand = $packageManager === 'npm'
        ? ['npm', 'install', ...$missingPackages, '--save-dev']
        : ['yarn', 'add', ...$missingPackages, '--dev'];
    Process::assertRan(static fn (PendingProcess $process): bool => $process->command === $expectedCommand);
    expect(File::get(base_path('package.json')))->toBe($manifest);
})->with(['npm', 'yarn'])->with([true, false]);

it('does not implicitly upgrade incompatible renderer dependencies', function (string $framework, string $package, string $version): void {
    $manifest = json_encode(['dependencies' => [$package => '^' . $version]]);
    File::put(base_path('package.json'), $manifest);
    File::ensureDirectoryExists(base_path("node_modules/{$package}"));
    File::put(base_path("node_modules/{$package}/package.json"), json_encode(['version' => $version]));
    $fieldPath = app_path('Filament/Forms/Components/IncompatibleField.php');
    File::delete($fieldPath);

    $this->artisan('make:filament-form-field', [
        'name' => 'IncompatibleField',
        "--{$framework}" => true,
        '--ts' => true,
        '--skip-build' => true,
        '--no-interaction' => true,
    ])
        ->expectsOutputToContain("The existing [{$package}] version [{$version}] is not supported by this renderer.")
        ->assertFailed();

    Process::assertNotRan(static fn (PendingProcess $process): bool => in_array('install', $process->command));
    expect(File::get(base_path('package.json')))->toBe($manifest)
        ->and(File::exists($fieldPath))->toBeFalse();
})->with([
    ['react', 'react', '17.0.2'],
    ['vue', 'vue', '2.7.16'],
    ['svelte', 'svelte', '4.2.20'],
    ['svelte', 'svelte', '5.40.0'],
    ['js', 'typescript', '7.0.2'],
]);

it('preserves a compatible existing Svelte plugin on Vite 6.3', function (string $pluginVersion): void {
    $manifest = json_encode(['devDependencies' => ['svelte' => '5.40.0', '@sveltejs/vite-plugin-svelte' => $pluginVersion]]);
    File::put(base_path('package.json'), $manifest);

    foreach (['vite' => '6.3.0', 'svelte' => '5.40.0', '@sveltejs/vite-plugin-svelte' => $pluginVersion] as $package => $version) {
        File::ensureDirectoryExists(base_path("node_modules/{$package}"));
        File::put(base_path("node_modules/{$package}/package.json"), json_encode(['version' => $version]));
    }

    $this->artisan('make:filament-form-field', [
        'name' => 'ExistingSvelteField',
        '--svelte' => true,
        '--skip-build' => true,
        '--no-interaction' => true,
    ])->assertSuccessful();

    Process::assertNotRan(static fn (PendingProcess $process): bool => in_array('install', $process->command));
    expect(File::get(base_path('package.json')))->toBe($manifest);
})->with(['5.1.1', '6.0.0']);

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
        ->toHaveKey('compilerOptions.jsx', 'react-jsx')
        ->toHaveKey('compilerOptions.paths.@filament/forms/js-field', ['./vendor/filament/forms/resources/js/types/js-field.d.ts'])
        ->toHaveKey('include', ['resources/js/**/*']);
});

it('merges the field type alias into TypeScript configuration while preserving aliases and `baseUrl`', function (): void {
    File::put(base_path('tsconfig.json'), json_encode([
        'compilerOptions' => [
            'baseUrl' => 'resources/js',
            'jsx' => 'preserve',
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
        ->toHaveKey('compilerOptions.jsx', 'preserve')
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

it('requires installed Vite before selecting renderer dependencies', function (bool $skipInstall): void {
    File::delete(base_path('node_modules/vite/package.json'));
    $fieldPath = app_path('Filament/Forms/Components/UninstalledViteField.php');
    File::delete($fieldPath);

    $this->artisan('make:filament-form-field', [
        'name' => 'UninstalledViteField',
        '--svelte' => true,
        '--skip-install' => $skipInstall,
        '--skip-build' => true,
        '--no-interaction' => true,
    ])
        ->expectsOutputToContain('Install your application\'s existing JavaScript dependencies, including Vite, before generating a JavaScript renderer.')
        ->assertFailed();

    expect(File::exists($fieldPath))->toBeFalse();
    Process::assertNotRan(static fn (PendingProcess $process): bool => in_array('install', $process->command));
})->with([false, true]);

it('uses a compiler compatible with the installed Vite and its supported Node versions', function (string $framework, string $version, array $dependencies): void {
    File::put(base_path('vite.config.js'), file_get_contents(__DIR__ . '/../../Panels/Commands/Fixture/MakeThemeCommandTest/vite-config/standard.js'));
    File::ensureDirectoryExists(base_path('node_modules/vite'));
    File::put(base_path('node_modules/vite/package.json'), json_encode(['version' => $version]));

    $this->artisan('make:filament-form-field', ['name' => 'CompilerField', "--{$framework}" => true, '--pm' => 'yarn', '--no-interaction' => true])
        ->expectsConfirmation('Would you like to compile the field now?', 'yes')
        ->assertSuccessful();

    Process::assertRan(static fn (PendingProcess $process): bool => $process->command === ['yarn', 'add', ...$dependencies, '--dev']);
    expect(File::get(base_path('vite.config.js')))->toContain("rollupOptions: { preserveEntrySignatures: 'exports-only' }");
})->with([
    ['svelte', '5.4.0', ['svelte@^5.0.0', '@sveltejs/vite-plugin-svelte@^4.0']],
    ['svelte', '6.2.0', ['svelte@^5.0.0', '@sveltejs/vite-plugin-svelte@^5.0']],
    ['svelte', '6.3.0', ['svelte@^5.0.0', '@sveltejs/vite-plugin-svelte@^5.0']],
    ['svelte', '7.0.0', ['svelte@^5.0.0', '@sveltejs/vite-plugin-svelte@^6.0']],
    ['vue', '5.4.0', ['vue@^3.3', '@vitejs/plugin-vue@^5.2.4']],
    ['vue', '6.3.0', ['vue@^3.3', '@vitejs/plugin-vue@^5.2.4']],
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
