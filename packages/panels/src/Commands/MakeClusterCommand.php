<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Commands\Concerns\CanIndentStrings;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeClusterCommand extends Command
{
    use CanIndentStrings;
    use CanManipulateFiles;

    protected $description = 'Create a new Filament cluster class';

    protected $signature = 'make:filament-cluster {name?} {--panel=} {--F|force}';

    public function handle(): int
    {
        $cluster = (string) str(
            $this->argument('name') ??
            text(
                label: 'What is the cluster name?',
                placeholder: 'Settings',
                required: true,
            ),
        )
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $clusterClass = (string) str($cluster)->afterLast('\\');
        $clusterNamespace = str($cluster)->contains('\\') ?
            (string) str($cluster)->beforeLast('\\') :
            '';

        $panel = $this->option('panel');

        if ($panel) {
            $panel = Filament::getPanel($panel);
        }

        if (! $panel) {
            $panels = Filament::getPanels();

            /** @var Panel $panel */
            $panel = (count($panels) > 1) ? $panels[select(
                label: 'Which panel would you like to create this in?',
                options: array_map(
                    fn (Panel $panel): string => $panel->getId(),
                    $panels,
                ),
                default: Filament::getDefaultPanel()->getId()
            )] : Arr::first($panels);
        }

        $clusterDirectories = $panel->getClusterDirectories();
        $clusterNamespaces = $panel->getClusterNamespaces();

        $namespace = (count($clusterNamespaces) > 1) ?
            select(
                label: 'Which namespace would you like to create this in?',
                options: $clusterNamespaces
            ) :
            (Arr::first($clusterNamespaces) ?? 'App\\Filament\\Clusters');
        $path = (count($clusterDirectories) > 1) ?
            $clusterDirectories[array_search($namespace, $clusterNamespaces)] :
            (Arr::first($clusterDirectories) ?? app_path('Filament/Clusters/'));

        $path = (string) str($cluster)
            ->prepend('/')
            ->prepend($path)
            ->replace('\\', '/')
            ->replace('//', '/')
            ->append('.php');

        $files = [$path];

        if (! $this->option('force') && $this->checkForCollision($files)) {
            return static::INVALID;
        }

        $this->copyStubToApp('Cluster', $path, [
            'class' => $clusterClass,
            'namespace' => $namespace . ($clusterNamespace !== '' ? "\\{$clusterNamespace}" : ''),
        ]);

        $this->components->info("Filament cluster [{$path}] created successfully.");

        if (empty($clusterNamespaces)) {
            $this->components->info('Make sure to register the cluster with `discoverClusters()` in the panel service provider.');
        }

        return static::SUCCESS;
    }
}
