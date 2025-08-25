<?php

namespace Filament\Support\Commands\Concerns;

use Illuminate\Support\Arr;

use function Laravel\Prompts\search;

trait HasResourcesLocation
{
    protected string $resourcesNamespace;

    protected string $resourcesDirectory;

    protected function configureResourcesLocation(string $question): void
    {
        if (filled($this->clusterFqn)) {
            return;
        }

        [
            $this->resourcesNamespace,
            $this->resourcesDirectory,
        ] = $this->getResourcesLocation($question);
    }

    /**
     * @return array{
     *     0: string,
     *     1: string,
     * }
     */
    public function getResourcesLocation(string $question): array
    {
        $directories = $this->panel->getResourceDirectories();
        $namespaces = $this->panel->getResourceNamespaces();

        foreach ($directories as $index => $directory) {
            if (str($directory)->startsWith(base_path('vendor'))) {
                unset($directories[$index]);
                unset($namespaces[$index]);
            }
        }

        if (count($namespaces) < 2) {
            return [
                (Arr::first($namespaces) ?? app()->getNamespace() . 'Filament\\Resources'),
                (Arr::first($directories) ?? app_path('Filament/Resources/')),
            ];
        }

        if ($this->option('resource-namespace')) {
            return [
                (string) $this->option('resource-namespace'),
                $directories[array_search($this->option('resource-namespace'), $namespaces)],
            ];
        }

        $keyedNamespaces = array_combine(
            $namespaces,
            $namespaces,
        );

        return [
            $namespace = search(
                label: $question,
                options: function (?string $search) use ($keyedNamespaces): array {
                    if (blank($search)) {
                        return $keyedNamespaces;
                    }

                    $search = str($search)->trim()->replace(['\\', '/'], '');

                    return array_filter($keyedNamespaces, fn (string $namespace): bool => str($namespace)->replace(['\\', '/'], '')->contains($search, ignoreCase: true));
                },
            ),
            $directories[array_search($namespace, $namespaces)],
        ];
    }
}
