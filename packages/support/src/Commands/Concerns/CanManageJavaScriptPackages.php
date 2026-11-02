<?php

namespace Filament\Support\Commands\Concerns;

use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\confirm;

trait CanManageJavaScriptPackages
{
    protected string $packageManager;

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
