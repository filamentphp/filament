<?php

namespace Filament\Support\Commands\Concerns;

use Filament\Clusters\Cluster;
use ReflectionClass;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\search;
use function Laravel\Prompts\text;

trait HasCluster
{
    /**
     * @var ?class-string<Cluster>
     */
    protected ?string $clusterFqn = null;

    protected function configureClusterFqn(string $initialQuestion, string $question): void
    {
        $this->clusterFqn = $this->askForCluster($initialQuestion, $question, $this->option('cluster'));
    }

    /**
     * @return ?class-string<Cluster>
     */
    protected function askForCluster(string $initialQuestion, string $question, ?string $initialValue = null): ?string
    {
        $clusterFqns = array_values($this->panel->getClusters());

        if (
            blank($initialValue) &&
            (empty($clusterFqns) || (! confirm(
                label: $initialQuestion,
                default: false,
            )))
        ) {
            return null;
        }

        if (is_string($initialValue)) {
            $cluster = (string) str($initialValue)
                ->trim('/')
                ->trim('\\')
                ->trim(' ')
                ->replace('/', '\\');

            if (! class_exists($cluster)) {
                $this->components->warn('The cluster class provided does not exist.');
            } elseif (! is_subclass_of($cluster, Cluster::class)) {
                $this->components->warn('The cluster class or one of its parents must extend [' . Cluster::class . '].');
            } else {
                return $cluster;
            }
        }

        if (empty($clusterFqns)) {
            $clusterFqn = (string) str(text(
                label: "No clusters were found within the [{$this->panel->getId()}] panel. {$question}",
                placeholder: app()->getNamespace() . 'Filament\\Clusters\\Blog',
                required: true,
                validate: function (string $value): ?string {
                    $value = (string) str($value)
                        ->trim('/')
                        ->trim('\\')
                        ->trim(' ')
                        ->replace('/', '\\');

                    if (
                        (! class_exists($value)) &&
                        class_exists("{$value}\\" . class_basename($value) . 'Cluster')
                    ) {
                        $value = "{$value}\\" . class_basename($value) . 'Cluster';
                    }

                    return match (true) {
                        ! class_exists($value) => 'The cluster class doesn\'t exist, please use the fully-qualified class name.',
                        ! is_subclass_of($value, Cluster::class) => 'The cluster class or one of its parents must extend [' . Cluster::class . '].',
                        default => null,
                    };
                },
                hint: 'Please provide the fully-qualified class name of the cluster.',
            ))
                ->trim('/')
                ->trim('\\')
                ->trim(' ')
                ->replace('/', '\\');

            if (
                (! class_exists($clusterFqn)) &&
                class_exists("{$clusterFqn}\\" . class_basename($clusterFqn) . 'Cluster')
            ) {
                return "{$clusterFqn}\\" . class_basename($clusterFqn) . 'Cluster';
            }

            return $clusterFqn;
        }

        return search(
            label: $question,
            options: function (?string $search) use ($clusterFqns): array {
                if (blank($search)) {
                    return $clusterFqns;
                }

                $search = str($search)->trim()->replace(['\\', '/'], '');

                return collect($clusterFqns)
                    ->filter(fn (string $fqn): bool => str($fqn)->replace(['\\', '/'], '')->contains($search, ignoreCase: true))
                    ->mapWithKeys(function (string $fqn): array {
                        $basenameBeforeCluster = (string) str($fqn)
                            ->classBasename()
                            ->beforeLast('Cluster');

                        $namespacePartBeforeBasename = (string) str($fqn)
                            ->beforeLast('\\')
                            ->classBasename();

                        if ($basenameBeforeCluster === $namespacePartBeforeBasename) {
                            return [$fqn => (string) str($fqn)->beforeLast('\\')];
                        }

                        return [$fqn => $fqn];
                    })
                    ->all();
            },
        );
    }

    protected function configureClusterResourcesLocation(): void
    {
        [
            $this->resourcesNamespace, /** @phpstan-ignore-line */
            $this->resourcesDirectory, /** @phpstan-ignore-line */
        ] = $this->getClusterResourcesLocation();
    }

    /**
     * @param  ?class-string<Cluster>  $clusterFqn
     * @return array{
     *     0: string,
     *     1: string
     * }
     */
    public function getClusterResourcesLocation(?string $clusterFqn = null): array
    {
        $clusterFqn ??= $this->clusterFqn;

        $clusterBasenameBeforeCluster = (string) str($clusterFqn)
            ->classBasename()
            ->beforeLast('Cluster');

        $clusterNamespacePartBeforeBasename = (string) str($clusterFqn)
            ->beforeLast('\\')
            ->classBasename();

        if ($clusterBasenameBeforeCluster === $clusterNamespacePartBeforeBasename) {
            return [
                (string) str($clusterFqn)
                    ->beforeLast('\\')
                    ->append('\\Resources'),
                (string) str((new ReflectionClass($clusterFqn))->getFileName())
                    ->beforeLast(DIRECTORY_SEPARATOR)
                    ->append('/Resources'),
            ];
        }

        return [
            "{$clusterFqn}\\Resources",
            (string) str((new ReflectionClass($clusterFqn))->getFileName())
                ->beforeLast('.')
                ->append('/Resources'),
        ];
    }
}
