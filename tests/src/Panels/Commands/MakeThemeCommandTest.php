<?php

use Filament\PanelRegistry;
use Filament\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;
use Laravel\Prompts\Prompt;

uses(TestCase::class);

describe('Package manager prerequisites', function (): void {
    beforeEach(function (): void {
        $this->themeCommandDirectory = sys_get_temp_dir() . '/filament-theme-' . bin2hex(random_bytes(8));

        $filesystem = app(Filesystem::class);
        $filesystem->ensureDirectoryExists($this->themeCommandDirectory . '/app/Providers/Filament');
        $filesystem->ensureDirectoryExists($this->themeCommandDirectory . '/resources');
        $filesystem->copy(
            __DIR__ . '/Fixture/MakeThemeCommandTest/vite-config/standard.js',
            $this->themeCommandDirectory . '/vite.config.js',
        );
        $filesystem->copy(
            __DIR__ . '/Fixture/MakeThemeCommandTest/panel-provider/standard.php',
            $this->themeCommandDirectory . '/app/Providers/Filament/AdminPanelProvider.php',
        );

        $this->app->setBasePath($this->themeCommandDirectory);
        $this->app->useAppPath($this->themeCommandDirectory . '/app');
    });

    afterEach(function (): void {
        app(Filesystem::class)->deleteDirectory($this->themeCommandDirectory);
    });

    it('fails without changing theme inputs when the requested package manager is unavailable', function (array $panelArguments): void {
        $viteConfig = file_get_contents(base_path('vite.config.js'));
        $panelProvider = file_get_contents(app_path('Providers/Filament/AdminPanelProvider.php'));
        $packageManager = $this->themeCommandDirectory . '/missing-package-manager';

        $this->artisan('make:filament-theme', [
            ...$panelArguments,
            '--pm' => $packageManager,
            '--no-interaction' => true,
        ])
            ->expectsOutput("The [{$packageManager}] package manager is not installed. Please install it before continuing.")
            ->assertFailed();

        expect(file_get_contents(base_path('vite.config.js')))->toBe($viteConfig)
            ->and(file_get_contents(app_path('Providers/Filament/AdminPanelProvider.php')))->toBe($panelProvider)
            ->and(file_exists(resource_path('css/filament/admin/theme.css')))->toBeFalse()
            ->and(file_exists(base_path('package.json')))->toBeFalse()
            ->and(file_exists(base_path('node_modules')))->toBeFalse();
    })->with([
        'panel argument' => [['panel' => 'admin']],
        'panel option' => [['--panel' => 'admin']],
    ]);

    it('preserves an existing theme when overwrite is declined or installation fails', function (bool $shouldOverwrite): void {
        $path = resource_path('css/filament/admin/theme.css');
        $filesystem = app(Filesystem::class);
        $filesystem->ensureDirectoryExists(dirname($path));
        $filesystem->put($path, '/* Existing custom theme */');
        $viteConfig = $filesystem->get(base_path('vite.config.js'));
        $panelProvider = $filesystem->get(app_path('Providers/Filament/AdminPanelProvider.php'));
        $environment = app()['env'];

        Process::fake(static fn (PendingProcess $process) => Process::result(exitCode: ($process->command === ['npm', '--version']) ? 0 : 1));
        Process::preventStrayProcesses();

        try {
            app()['env'] = 'local';

            // Keep console expectations active while exercising the non-test collision check.
            Prompt::fallbackWhen(true);

            $command = $this->artisan('make:filament-theme', [
                '--panel' => 'admin',
                '--skip-build' => true,
            ])->expectsConfirmation('theme.css already exists, do you want to overwrite it?', $shouldOverwrite ? 'yes' : 'no');

            if ($shouldOverwrite) {
                $command->expectsOutputToContain('Failed to install JavaScript dependencies.');
            }

            $command->assertFailed();
            $command->run();

            expect($filesystem->get($path))->toBe('/* Existing custom theme */')
                ->and($filesystem->get(base_path('vite.config.js')))->toBe($viteConfig)
                ->and($filesystem->get(app_path('Providers/Filament/AdminPanelProvider.php')))->toBe($panelProvider);

            if ($shouldOverwrite) {
                Process::assertRan(static fn (PendingProcess $process): bool => $process->command === ['npm', 'install', 'tailwindcss@latest', '@tailwindcss/vite', '--save-dev']);
            } else {
                Process::assertNothingRan();
            }
        } finally {
            app()['env'] = $environment;
        }
    })->with([false, true]);
});

describe('Panel prerequisites', function (): void {
    it('fails before running the package manager when no panels are registered', function (): void {
        $this->app->instance(PanelRegistry::class, new PanelRegistry);

        $this->artisan('make:filament-theme', [
            '--pm' => '/missing-filament-theme-package-manager',
            '--no-interaction' => true,
        ])
            ->expectsOutputToContain('Filament has not been installed yet: php artisan filament:install --panels')
            ->doesntExpectOutputToContain('package manager is not installed')
            ->assertFailed();
    });
});
