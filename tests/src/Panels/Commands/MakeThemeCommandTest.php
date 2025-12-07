<?php

use Filament\Commands\MakeThemeCommand;
use Filament\Panel;
use Filament\Tests\TestCase;
use Illuminate\Console\OutputStyle;
use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;
use Mockery\MockInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

uses(TestCase::class);

function getFixturePath(string $type, string $name): string
{
    return __DIR__ . "/Fixture/MakeThemeCommandTest/{$type}/{$name}";
}

function getFixtureContent(string $type, string $name): string
{
    return file_get_contents(getFixturePath($type, $name));
}

function createCommandWithMockedFilesystem(string $viteConfigContent = '', string $panelProviderContent = '', string $panelId = 'admin'): array
{
    $viteConfigPath = base_path('vite.config.js');
    $panelProviderPath = app_path('Providers/Filament/AdminPanelProvider.php');

    // Use an object to hold the mutable state
    $state = new stdClass;
    $state->viteConfig = $viteConfigContent;
    $state->panelProvider = $panelProviderContent;

    $filesystem = Mockery::mock(Filesystem::class, function (MockInterface $mock) use ($viteConfigPath, $panelProviderPath, $state): void {
        // vite.config.js
        $mock->shouldReceive('exists')
            ->with($viteConfigPath)
            ->andReturnUsing(fn () => $state->viteConfig !== '');

        $mock->shouldReceive('get')
            ->with($viteConfigPath)
            ->andReturnUsing(fn () => $state->viteConfig);

        $mock->shouldReceive('put')
            ->with($viteConfigPath, Mockery::any())
            ->andReturnUsing(function ($path, $content) use ($state) {
                $state->viteConfig = $content;

                return true;
            });

        // Panel provider
        $mock->shouldReceive('exists')
            ->with($panelProviderPath)
            ->andReturnUsing(fn () => $state->panelProvider !== '');

        $mock->shouldReceive('get')
            ->with($panelProviderPath)
            ->andReturnUsing(fn () => $state->panelProvider);

        $mock->shouldReceive('put')
            ->with($panelProviderPath, Mockery::any())
            ->andReturnUsing(function ($path, $content) use ($state) {
                $state->panelProvider = $content;

                return true;
            });
    });

    $command = new MakeThemeCommand;

    // Set up the command's output and components (required for info() calls)
    $output = new OutputStyle(new ArrayInput([]), new NullOutput);
    $command->setOutput($output);

    // Set up the command with required properties using reflection
    $reflection = new ReflectionClass($command);

    // Set up components factory
    $componentsProperty = $reflection->getProperty('components');
    $componentsProperty->setValue($command, new ComponentsFactory($output));

    $filesystemProperty = $reflection->getProperty('filesystem');
    $filesystemProperty->setValue($command, $filesystem);

    $themePathProperty = $reflection->getProperty('themePath');
    $themePathProperty->setValue($command, "resources/css/filament/{$panelId}/theme.css");

    // Create a mock panel
    $panel = Panel::make()->id($panelId)->path($panelId);
    $panelProperty = $reflection->getProperty('panel');
    $panelProperty->setValue($command, $panel);

    return [
        'command' => $command,
        'filesystem' => $filesystem,
        'getViteConfig' => fn () => $state->viteConfig,
        'getPanelProvider' => fn () => $state->panelProvider,
    ];
}

function callProtectedMethod(object $object, string $method, array $args = []): mixed
{
    $reflection = new ReflectionClass($object);
    $method = $reflection->getMethod($method);

    return $method->invokeArgs($object, $args);
}

describe('Register in Vite config', function (): void {
    it('adds theme to single line input array without trailing comma', function (): void {
        $content = getFixtureContent('vite-config', 'standard.js');
        $setup = createCommandWithMockedFilesystem(viteConfigContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInViteConfig');

        expect($result)->toBeTrue();
        expect(($setup['getViteConfig'])())->toMatchSnapshot();
    });

    it('adds theme to single line input array with trailing comma', function (): void {
        $content = getFixtureContent('vite-config', 'single-line-trailing-comma.js');
        $setup = createCommandWithMockedFilesystem(viteConfigContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInViteConfig');

        expect($result)->toBeTrue();
        expect(($setup['getViteConfig'])())->toMatchSnapshot();
    });

    it('adds theme to Vite config with double quotes', function (): void {
        $content = getFixtureContent('vite-config', 'double-quotes.js');
        $setup = createCommandWithMockedFilesystem(viteConfigContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInViteConfig');

        expect($result)->toBeTrue();
        expect(($setup['getViteConfig'])())->toMatchSnapshot();
    });

    it('adds theme to multiline input array with trailing comma', function (): void {
        $content = getFixtureContent('vite-config', 'multiline-input.js');
        $setup = createCommandWithMockedFilesystem(viteConfigContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInViteConfig');

        expect($result)->toBeTrue();
        expect(($setup['getViteConfig'])())->toMatchSnapshot();
    });

    it('adds theme to multiline input array without trailing comma', function (): void {
        $content = getFixtureContent('vite-config', 'multiline-no-trailing-comma.js');
        $setup = createCommandWithMockedFilesystem(viteConfigContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInViteConfig');

        expect($result)->toBeTrue();
        expect(($setup['getViteConfig'])())->toMatchSnapshot();
    });

    it('adds theme to Vite config with refresh paths', function (): void {
        $content = getFixtureContent('vite-config', 'with-refresh-paths.js');
        $setup = createCommandWithMockedFilesystem(viteConfigContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInViteConfig');

        expect($result)->toBeTrue();
        expect(($setup['getViteConfig'])())->toMatchSnapshot();
    });

    it('returns true without modifying when theme already exists', function (): void {
        $content = getFixtureContent('vite-config', 'already-has-theme.js');
        $setup = createCommandWithMockedFilesystem(viteConfigContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInViteConfig');

        expect($result)->toBeTrue();
        // Content should be unchanged
        expect(($setup['getViteConfig'])())->toBe($content);
    });

    it('returns false when no Vite config exists', function (): void {
        $setup = createCommandWithMockedFilesystem(viteConfigContent: '');

        $result = callProtectedMethod($setup['command'], 'registerInViteConfig');

        expect($result)->toBeFalse();
    });

    it('returns false when no Laravel paths in input array', function (): void {
        $content = getFixtureContent('vite-config', 'no-laravel-paths.js');
        $setup = createCommandWithMockedFilesystem(viteConfigContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInViteConfig');

        expect($result)->toBeFalse();
    });

    it('returns false when no input array exists', function (): void {
        $content = getFixtureContent('vite-config', 'no-input-array.js');
        $setup = createCommandWithMockedFilesystem(viteConfigContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInViteConfig');

        expect($result)->toBeFalse();
    });
});

describe('Register in panel provider', function (): void {
    it('adds `viteTheme()` to standard panel provider', function (): void {
        $content = getFixtureContent('panel-provider', 'standard.php');
        $setup = createCommandWithMockedFilesystem(panelProviderContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInPanelProvider');

        expect($result)->toBeTrue();
        expect(($setup['getPanelProvider'])())->toMatchSnapshot();
    });

    it('adds `viteTheme()` to panel provider with different spacing', function (): void {
        $content = getFixtureContent('panel-provider', 'different-spacing.php');
        $setup = createCommandWithMockedFilesystem(panelProviderContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInPanelProvider');

        expect($result)->toBeTrue();
        expect(($setup['getPanelProvider'])())->toMatchSnapshot();
    });

    it('adds `viteTheme()` to panel provider with double quotes', function (): void {
        $content = getFixtureContent('panel-provider', 'double-quotes.php');
        $setup = createCommandWithMockedFilesystem(panelProviderContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInPanelProvider');

        expect($result)->toBeTrue();
        expect(($setup['getPanelProvider'])())->toMatchSnapshot();
    });

    it('adds `viteTheme()` to full-featured panel provider', function (): void {
        $content = getFixtureContent('panel-provider', 'full-featured.php');
        $setup = createCommandWithMockedFilesystem(panelProviderContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInPanelProvider');

        expect($result)->toBeTrue();
        expect(($setup['getPanelProvider'])())->toMatchSnapshot();
    });

    it('returns `true` without modifying when `viteTheme()` already exists', function (): void {
        $content = getFixtureContent('panel-provider', 'already-has-vite-theme.php');
        $setup = createCommandWithMockedFilesystem(panelProviderContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInPanelProvider');

        expect($result)->toBeTrue();
        // Content should be unchanged
        expect(($setup['getPanelProvider'])())->toBe($content);
    });

    it('returns `false` when panel provider does not exist', function (): void {
        $setup = createCommandWithMockedFilesystem(panelProviderContent: '');

        $result = callProtectedMethod($setup['command'], 'registerInPanelProvider');

        expect($result)->toBeFalse();
    });

    it('returns `false` when panel ID does not match', function (): void {
        $content = getFixtureContent('panel-provider', 'wrong-panel-id.php');
        $setup = createCommandWithMockedFilesystem(panelProviderContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInPanelProvider');

        expect($result)->toBeFalse();
    });

    it('adds `viteTheme()` after `id()` when no path method exists', function (): void {
        $content = getFixtureContent('panel-provider', 'no-path-method.php');
        $setup = createCommandWithMockedFilesystem(panelProviderContent: $content);

        $result = callProtectedMethod($setup['command'], 'registerInPanelProvider');

        expect($result)->toBeTrue();
        expect(($setup['getPanelProvider'])())->toMatchSnapshot();
    });
});
