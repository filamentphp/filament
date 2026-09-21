<?php

namespace Filament\Support\Commands\Concerns;

use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\confirm;

trait CanManageJavaScriptPackages
{
    protected string $packageManager;

    /**
     * @param  'js' | 'react' | 'vue' | 'svelte'  $framework
     * @return array<string>
     */
    protected function getJavaScriptRendererDependencies(string $framework, bool $isTypeScript, string $viteVersion): array
    {
        $reactVersion = ($framework === 'react')
            ? ($this->getInstalledJavaScriptDependencyVersion('react') ?? $this->getInstalledJavaScriptDependencyVersion('react-dom'))
            : null;
        $reactMajor = (int) explode('.', $reactVersion ?? '19')[0];
        $reactConstraint = $reactVersion ?? '^19.0';
        $sveltePluginMajor = match (true) {
            version_compare($viteVersion, '8.0.0', '>=') => 7,
            version_compare($viteVersion, '7.0.0', '>=') => 6,
            version_compare($viteVersion, '6.0.0', '>=') => 5,
            default => 4,
        };
        $svelteMinimum = ($sveltePluginMajor === 7) ? '5.46.4' : '5.0.0';

        $dependencies = match ($framework) {
            'js' => [],
            'react' => ["react@{$reactConstraint}", "react-dom@{$reactConstraint}"],
            'vue' => ['vue@^3.3', '@vitejs/plugin-vue@' . (version_compare($viteVersion, '7.0.0', '>=') ? '^6.0' : '^5.2.4')],
            'svelte' => ["svelte@^{$svelteMinimum}", "@sveltejs/vite-plugin-svelte@^{$sveltePluginMajor}.0"],
        };

        if ($isTypeScript) {
            $dependencies = [...$dependencies, 'typescript@^6.0'];

            if ($framework === 'react') {
                $dependencies = [...$dependencies, "@types/react@^{$reactMajor}.0", "@types/react-dom@^{$reactMajor}.0"];
            }
        }

        $supportedVersions = [
            'react' => [max(18, $reactMajor) . '.0.0', ($reactMajor + 1) . '.0.0'],
            'react-dom' => [max(18, $reactMajor) . '.0.0', ($reactMajor + 1) . '.0.0'],
            '@types/react' => ["{$reactMajor}.0.0", ($reactMajor + 1) . '.0.0'],
            '@types/react-dom' => ["{$reactMajor}.0.0", ($reactMajor + 1) . '.0.0'],
            'vue' => ['3.3.0', '4.0.0'],
            '@vitejs/plugin-vue' => [match (true) {
                version_compare($viteVersion, '8.0.0', '>=') => '6.0.3',
                version_compare($viteVersion, '7.0.0', '>=') => '6.0.0',
                version_compare($viteVersion, '6.0.0', '>=') => '5.2.1',
                default => '5.0.0',
            }, '7.0.0'],
            'svelte' => [$svelteMinimum, '6.0.0'],
            '@sveltejs/vite-plugin-svelte' => [
                "{$sveltePluginMajor}.0.0",
                (($sveltePluginMajor === 5) && version_compare($viteVersion, '6.3.0', '>=')) ? '7.0.0' : ($sveltePluginMajor + 1) . '.0.0',
            ],
            'typescript' => ['5.0.0', '7.0.0'],
        ];

        return array_values(array_filter($dependencies, function (string $dependency) use ($supportedVersions): bool {
            $package = substr($dependency, 0, strrpos($dependency, '@'));
            $version = $this->getInstalledJavaScriptDependencyVersion($package);

            if ($version === null) {
                return true;
            }

            [$minimum, $maximum] = $supportedVersions[$package];

            if (version_compare($version, $minimum, '<') || version_compare($version, $maximum, '>=')) {
                $this->components->error("The existing [{$package}] version [{$version}] is not supported by this renderer. Resolve its compatibility manually before generating the renderer.");

                throw new FailureCommandOutput;
            }

            return false;
        }));
    }

    protected function getInstalledJavaScriptDependencyVersion(string $package): ?string
    {
        $manifest = File::exists(base_path('package.json')) ? File::json(base_path('package.json')) : [];
        $dependencies = [
            ...($manifest['dependencies'] ?? []),
            ...($manifest['devDependencies'] ?? []),
            ...($manifest['optionalDependencies'] ?? []),
            ...($manifest['peerDependencies'] ?? []),
        ];

        if (! array_key_exists($package, $dependencies)) {
            return null;
        }

        $path = base_path("node_modules/{$package}/package.json");

        if (! File::exists($path)) {
            $this->components->error("Install your application's existing JavaScript dependencies before generating a renderer: [{$package}] is declared but not installed.");

            throw new FailureCommandOutput;
        }

        return File::json($path)['version'];
    }

    protected function configurePackageManager(): void
    {
        $packageManagerOption = $this->option('pm');
        $this->packageManager = $packageManagerOption ?? 'npm';

        if ($this->option('skip-install') && $this->option('skip-build')) {
            return;
        }

        $processResult = Process::path(base_path())->run([$this->packageManager, '--version']);

        if ($processResult->failed()) {
            if (filled($packageManagerOption)) {
                $this->error("The [{$packageManagerOption}] package manager is not installed. Please install it before continuing.");
            } else {
                $this->error('Node.js is not installed. Please install before continuing.');
            }

            throw new FailureCommandOutput;
        }

        $this->info("Using {$this->packageManager} v" . trim($processResult->output()));
    }

    /**
     * @param  array<string>  $dependencies
     */
    protected function installJavaScriptDependencies(array $dependencies): void
    {
        if ($dependencies === []) {
            return;
        }

        $arguments = match ($this->packageManager) {
            'yarn' => [$this->packageManager, 'add', ...$dependencies, '--dev'],
            default => [$this->packageManager, 'install', ...$dependencies, '--save-dev'],
        };

        if ($this->option('skip-install')) {
            $this->components->info('Run `' . implode(' ', $arguments) . '` to install the JavaScript dependencies.');

            return;
        }

        $processResult = Process::path(base_path())->run(
            $arguments,
            function (string $type, string $output): void {
                $this->output->write($output);
            },
        );

        if ($processResult->failed()) {
            $this->components->error('Failed to install JavaScript dependencies.');

            throw new FailureCommandOutput;
        }

        $this->components->info('Dependencies installed successfully.');
    }

    protected function buildJavaScriptAssets(string $subject): bool
    {
        if ($this->option('skip-build')) {
            $this->components->info("Run `{$this->packageManager} run build` to compile the {$subject}.");

            return true;
        }

        if (! confirm("Would you like to compile the {$subject} now?", default: true)) {
            $this->components->info("Run `{$this->packageManager} run build` to compile the {$subject}.");

            return true;
        }

        $this->components->info("Compiling {$subject}...");

        $processResult = Process::path(base_path())->run(
            [$this->packageManager, 'run', 'build'],
            function (string $type, string $output): void {
                $this->output->write($output);
            },
        );

        if ($processResult->failed()) {
            $this->components->error("Failed to compile the {$subject}.");

            return false;
        }

        return true;
    }
}
