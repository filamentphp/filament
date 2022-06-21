<?php

namespace Filament\Support\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Livewire\Commands\ComponentParser;
use SplFileInfo;

class DebugCommand extends Command
{
    protected $description = 'Collect debugging information for Filament';

    protected $signature = 'filament:doctor {--F|fix}';

    protected $runFix = false;

    protected $hasFixes = false;

    protected $vendorViewDirectories = [
        'filament',
        'forms',
        'tables',
    ];

    public function handle(): int
    {
        $this->runFix = $this->option('fix');

        if ($this->runFix) {
            $this->line('Running fixes');

            $this->runFixLivewireDirectory();
            $this->runFixVendorViews();

            $this->line('Fixes completed');

            $this->line('Running filament:upgrade');
            $this->call('filament:upgrade');
            $this->newLine();
            $this->info('Done');
        } else {
            $this->showVersionInfo();
            $this->checkLiverwireDirectory();
            $this->checkVendorViews();

            if ($this->hasFixes) {
                $this->line("run command with --fix flag to apply fixes");
            } else {
                $this->newLine();
                $this->info('nothing to fix. 😁 💪');
            }
        }

        return static::SUCCESS;
    }

    protected function showVersionInfo()
    {
        if ($this->runFix) return;

        $versions = $this->getVersionInfo();
        $this->table(['package', 'version'], $versions);
        $this->newLine();
    }

    protected function getVersionInfo()
    {
        $versions = [
            'php' => PHP_VERSION,
            'laravel' => \Illuminate\Foundation\Application::VERSION,
            'filament/filament' => null,
            'filament/forms' => null,
            'filament/tables' => null,
            'filament/support' => null,
        ];

        $filamentPackages = collect(\Composer\InstalledVersions::getInstalledPackages())
            ->filter(fn ($packageName) => Str::startsWith($packageName, 'filament/'))
            ->each(function ($packageName) use (&$versions) {
                $versions[$packageName] = \Composer\InstalledVersions::getPrettyVersion($packageName);
            });

        return collect($versions)->map(fn ($version, $package) => [$package, $version]);
    }

    protected function checkVendorViews()
    {
        $vendorFiles = [];
        /** @var Filesystem $filesystem */
        $filesystem = app(Filesystem::class);
        foreach ($this->vendorViewDirectories as $directory) {
            $path = resource_path('views/vendor/') . $directory;
            if (!file_exists($path)) continue;
            $files = $filesystem->allFiles($path);
            if (filled($files)) {
                $vendorFiles = array_merge($vendorFiles, $files);
            }
        }

        if (filled($vendorFiles)) {
            $this->hasFixes = true;
            $this->warn('You have published vendor views!');
            /** @param SplFileInfo $file */
            $this->table(['files'], collect($vendorFiles)->map(fn ($file) => [Str::after($file->getPathname(), base_path() . '/')])->toArray());
        }
    }

    protected function runFixVendorViews()
    {
        if (!$this->runFix) return;

        /** @var Filesystem $filesystem */
        $filesystem = app(Filesystem::class);
        foreach ($this->vendorViewDirectories as $directory) {
            $path = resource_path('views/vendor/') . $directory;
            $backupPath = $path . '_backup';
            if (!file_exists($path)) continue;
            if (file_exists($backupPath)) {
                $this->warn("{$backupPath} existed, skipping..");
                continue;
            }
            $filesystem->moveDirectory($path, $backupPath);
            $this->info("{$path} moved to {$backupPath}");
        }
    }

    protected function checkLiverwireDirectory()
    {
        if ($this->isLivewireDirectoryExists()) {
            $this->line($this->getLiverwireDirectory() . " exist");
        } else {
            $this->warn($this->getLiverwireDirectory() . " not exist");
            $this->hasFixes = true;
        }
    }

    protected function getLiverwireDirectory()
    {
        return rtrim(ComponentParser::generatePathFromNamespace(config('livewire.class_namespace')), '/');
    }

    protected function isLivewireDirectoryExists()
    {
        return file_exists($this->getLiverwireDirectory());
    }

    protected function runFixLivewireDirectory()
    {
        if (!$this->runFix) return;

        $directory = $this->getLiverwireDirectory();

        if (file_exists($directory)) {
            return;
        }

        mkdir($directory);

        if (file_exists($directory)) $this->info($directory . ' created');
        else $this->warn('Error creating ' . $directory);
    }
}
