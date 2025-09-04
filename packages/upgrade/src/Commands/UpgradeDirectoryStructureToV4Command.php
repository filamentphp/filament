<?php

namespace Filament\Upgrade\Commands;

use Exception;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'filament:upgrade-directory-structure-to-v4')]
class UpgradeDirectoryStructureToV4Command extends Command
{
    protected $description = 'Upgrade Filament directory structure from v3 to v4';

    protected $name = 'filament:upgrade-directory-structure-to-v4';

    protected string $phpactorPath;

    /**
     * @var array<string, array<string, string>>
     */
    protected array $movedFiles = [];

    protected ?string $currentResource = null;

    /**
     * @return array<InputOption>
     */
    protected function getOptions(): array
    {
        return [
            new InputOption(
                name: 'dry-run',
                shortcut: 'D',
                mode: InputOption::VALUE_NONE,
                description: 'Preview changes without executing them',
            ),
        ];
    }

    protected function formatPath(string $path): string
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
    }

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if (! $isDryRun && ! $this->components->confirm('This command will modify your Filament resources and clusters to match the new v4 directory structure. Please commit any changes you have made to your project before continuing. Do you want to continue?', default: true)) {
            $this->components->info('Migration cancelled.');

            return self::FAILURE;
        }

        $this->components->info('Starting migration from Filament v3 to v4...');

        if ($isDryRun) {
            $this->newLine();
            $this->components->info('Running in dry-run mode. No changes will be made.');
            $this->newLine();
        }

        if (! $isDryRun) {
            $this->downloadPhpactor();
        } else {
            $this->phpactorPath = base_path('vendor' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'phpactor.phar');
        }

        $panels = Filament::getPanels();
        foreach ($panels as $panel) {
            $resources = $panel->getResources();

            if (count($resources) > 0) {
                $this->components->info('Processing resources in ' . $panel->getId() . ' panel');

                foreach ($resources as $resourceClass) {
                    $this->processResource($resourceClass, $isDryRun);
                }

                $this->newLine();
            }

            $clusters = $panel->getClusters();

            if (count($clusters) > 0) {
                $this->components->info('Processing resources in ' . $panel->getId() . ' panel');

                foreach ($clusters as $clusterClass) {
                    $this->processCluster($clusterClass, $isDryRun);
                }

                $this->newLine();
            }
        }

        if ($isDryRun) {
            $this->components->info('Dry run completed. Run without --dry-run to apply changes.');
        } else {
            $this->components->info('Migration completed successfully!');
        }

        return self::SUCCESS;
    }

    protected function downloadPhpactor(): void
    {
        $this->phpactorPath = base_path('vendor' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'phpactor.phar');

        if (File::exists($this->phpactorPath)) {
            $this->components->info('Phpactor already exists at: ' . $this->formatPath($this->phpactorPath));

            return;
        }

        $this->components->task('Downloading phpactor', function () {
            $process = Process::command(
                'curl -Lo ' . $this->phpactorPath . ' https://github.com/phpactor/phpactor/releases/latest/download/phpactor.phar'
            );
            $processOutput = $process->run();

            if (! $processOutput->successful()) {
                $this->error('Failed to download phpactor: ' . $processOutput->errorOutput());

                throw new RuntimeException('Failed to download phpactor');
            }

            chmod($this->phpactorPath, 0755);

            return true;
        });
    }

    /**
     * @param  class-string  $resourceClass
     */
    protected function processResource(string $resourceClass, bool $isDryRun = false): void
    {
        $resourceReflection = new ReflectionClass($resourceClass);
        $resourcePath = $resourceReflection->getFileName();

        if ($resourcePath === false) {
            $this->components->warn("Could not get file path for resource: {$resourceClass}");

            return;
        }

        if ($this->isVendorPath($resourcePath)) {
            $this->components->warn('Skipping resource in vendor directory');

            return;
        }

        $resourceBaseName = class_basename($resourceClass);
        $resourceDirectory = dirname($resourcePath);

        $this->currentResource = $resourceBaseName;

        $resourceName = Str::replaceEnd('Resource', '', $resourceBaseName);
        $pluralizedName = Str::plural($resourceName);

        $newResourceDirectory = $resourceDirectory . DIRECTORY_SEPARATOR . $pluralizedName;

        if ($this->isVendorPath($newResourceDirectory)) {
            $this->components->warn('Skipping resource with destination in vendor directory');

            return;
        }

        $this->findAndMoveRelatedClasses($resourceClass, $resourceDirectory, $newResourceDirectory, $resourceBaseName, $isDryRun);

        $newResourcePath = $newResourceDirectory . DIRECTORY_SEPARATOR . $resourceBaseName . '.php';
        $this->moveClass($resourcePath, $newResourcePath, $isDryRun);
    }

    /**
     * @param  class-string  $clusterClass
     */
    protected function processCluster(string $clusterClass, bool $isDryRun = false): void
    {
        $clusterReflection = new ReflectionClass($clusterClass);
        $clusterPath = $clusterReflection->getFileName();

        if ($clusterPath === false) {
            $this->components->warn("Could not get file path for cluster: {$clusterClass}");

            return;
        }

        if ($this->isVendorPath($clusterPath)) {
            $this->components->warn('Skipping cluster in vendor directory');

            return;
        }

        $clusterBaseName = class_basename($clusterClass);
        $clusterDirectory = dirname($clusterPath);

        $this->currentResource = $clusterBaseName;

        $endsWithCluster = Str::endsWith($clusterBaseName, 'Cluster');

        if ($endsWithCluster) {
            $newClusterBaseName = $clusterBaseName;
            $newClusterDirectory = $clusterDirectory . DIRECTORY_SEPARATOR . $clusterBaseName;
        } else {
            $newClusterBaseName = $clusterBaseName . 'Cluster';
            $newClusterDirectory = $clusterDirectory . DIRECTORY_SEPARATOR . $clusterBaseName;
        }

        if ($this->isVendorPath($newClusterDirectory)) {
            $this->components->warn('Skipping cluster with destination in vendor directory');

            return;
        }

        $newClusterPath = $newClusterDirectory . DIRECTORY_SEPARATOR . $newClusterBaseName . '.php';
        $this->moveClass($clusterPath, $newClusterPath, $isDryRun);
    }

    protected function findAndMoveRelatedClasses(string $resourceClass, string $resourceDirectory, string $newResourceDirectory, string $resourceBaseName, bool $isDryRun = false): void
    {
        $files = $this->findPhpFiles($resourceDirectory);

        $relatedFiles = [];
        foreach ($files as $file) {
            if (basename($file) === $resourceBaseName . '.php') {
                continue;
            }

            if (strpos($file, $resourceDirectory . DIRECTORY_SEPARATOR . $resourceBaseName) === false) {
                continue;
            }

            $relatedFiles[] = $file;
        }

        foreach ($relatedFiles as $file) {
            $relativePath = str_replace($resourceDirectory, '', $file);

            $resourceDirectoryPattern = DIRECTORY_SEPARATOR . $resourceBaseName . DIRECTORY_SEPARATOR;
            if (strpos($relativePath, $resourceDirectoryPattern) === 0) {
                $relativePath = substr($relativePath, strlen($resourceDirectoryPattern));
                $relativePath = DIRECTORY_SEPARATOR . $relativePath;
            }

            $newPath = $newResourceDirectory . $relativePath;

            $this->moveClass($file, $newPath, $isDryRun);
        }
    }

    protected function isVendorPath(string $path): bool
    {
        return str_contains($path, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);
    }

    /**
     * @return array<int, string>
     */
    protected function findPhpFiles(string $directory): array
    {
        $files = [];

        if (! File::exists($directory)) {
            return $files;
        }

        if ($this->isVendorPath($directory)) {
            return $files;
        }

        $items = File::allFiles($directory);

        foreach ($items as $item) {
            $pathname = $item->getPathname();

            if ($this->isVendorPath($pathname)) {
                continue;
            }

            if ($item->getExtension() === 'php') {
                $files[] = $pathname;
            }
        }

        return $files;
    }

    protected function moveClass(string $sourcePath, string $destinationPath, bool $isDryRun = false): void
    {
        if ($this->isVendorPath($sourcePath)) {
            $this->components->warn('Skipping file in vendor directory');

            return;
        }

        if ($this->isVendorPath($destinationPath)) {
            $this->components->warn('Skipping move to vendor directory');

            return;
        }

        if ($isDryRun) {
            if ($this->currentResource && (! isset($this->movedFiles[$this->currentResource]) || empty($this->movedFiles[$this->currentResource]))) {
                $this->line('  <fg=yellow;options=bold>' . $this->currentResource . '</>');
                $this->movedFiles[$this->currentResource] = [];
            } elseif (! $this->currentResource && (! isset($this->movedFiles['Other']) || empty($this->movedFiles['Other']))) {
                $this->line('  <fg=yellow;options=bold>Other</>');
                $this->movedFiles['Other'] = [];
            }

            $this->line('    • ' . $this->formatPath($sourcePath) . ' → ' . $this->formatPath($destinationPath));

            if ($this->currentResource) {
                $this->movedFiles[$this->currentResource][$sourcePath] = $destinationPath;
            } else {
                $this->movedFiles['Other'][$sourcePath] = $destinationPath;
            }

            return;
        }

        try {
            $process = Process::command(
                "php {$this->phpactorPath} class:move {$sourcePath} {$destinationPath}"
            );
            $process->timeout(60);
            $processOutput = $process->run();

            if ($processOutput->successful()) {
                if ($this->currentResource && (! isset($this->movedFiles[$this->currentResource]) || empty($this->movedFiles[$this->currentResource]))) {
                    $this->line('  <fg=yellow;options=bold>' . $this->currentResource . '</>');
                    $this->movedFiles[$this->currentResource] = [];
                } elseif (! $this->currentResource && (! isset($this->movedFiles['Other']) || empty($this->movedFiles['Other']))) {
                    $this->line('  <fg=yellow;options=bold>Other</>');
                    $this->movedFiles['Other'] = [];
                }

                $this->line('    • ' . $this->formatPath($sourcePath) . ' → ' . $this->formatPath($destinationPath));

                if ($this->currentResource) {
                    $this->movedFiles[$this->currentResource][$sourcePath] = $destinationPath;
                } else {
                    $this->movedFiles['Other'][$sourcePath] = $destinationPath;
                }
            } else {
                $this->components->error('Failed to move class: ' . $processOutput->errorOutput());
            }
        } catch (Exception $exception) {
            $this->components->error('Exception occurred while moving class: ' . $exception->getMessage());
        }
    }
}
