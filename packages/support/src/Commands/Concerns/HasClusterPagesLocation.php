<?php

namespace Filament\Support\Commands\Concerns;

use ReflectionClass;

trait HasClusterPagesLocation
{
    protected function configureClusterPagesLocation(): void
    {
        $clusterBasenameBeforeCluster = (string) str($this->clusterFqn)
            ->classBasename()
            ->beforeLast('Cluster');

        $clusterNamespacePartBeforeBasename = (string) str($this->clusterFqn)
            ->beforeLast('\\')
            ->classBasename();

        if ($clusterBasenameBeforeCluster === $clusterNamespacePartBeforeBasename) {
            $this->pagesNamespace = (string) str($this->clusterFqn)
                ->beforeLast('\\')
                ->append('\\Pages');
            $this->pagesDirectory = (string) str((new ReflectionClass($this->clusterFqn))->getFileName())
                ->beforeLast(DIRECTORY_SEPARATOR)
                ->append('/Pages');

            return;
        }

        $this->pagesNamespace = "{$this->clusterFqn}\\Pages";
        $this->pagesDirectory = (string) str((new ReflectionClass($this->clusterFqn))->getFileName())
            ->beforeLast('.')
            ->append('/Pages');
    }
}
