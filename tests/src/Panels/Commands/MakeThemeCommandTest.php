<?php

use Filament\PanelRegistry;
use Filament\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;

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
