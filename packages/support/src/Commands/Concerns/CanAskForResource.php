<?php

namespace Filament\Support\Commands\Concerns;

use Filament\Resources\Resource;
use Illuminate\Support\Collection;

use function Laravel\Prompts\search;
use function Laravel\Prompts\text;

trait CanAskForResource
{
    /**
     * @return class-string
     */
    protected function askForResource(string $question, ?string $initialResource = null, ?string $resourcesNamespace = null): string
    {
        $resourcesNamespace ??= $this->resourcesNamespace;

        if (is_string($initialResource)) {
            $initialResource = (string) str($initialResource)
                ->trim('/')
                ->trim('\\')
                ->trim(' ')
                ->replace('/', '\\');

            if (class_exists($initialResource)) {
                return $initialResource;
            }

            $resourceNamespace = (string) str($initialResource)
                ->beforeLast('Resource')
                ->pluralStudly()
                ->replace('/', '\\')
                ->prepend("{$resourcesNamespace}\\");

            $resourceBasename = (string) str($initialResource)
                ->classBasename()
                ->beforeLast('Resource')
                ->singular()
                ->append('Resource');

            if (class_exists("{$resourceNamespace}\\{$resourceBasename}")) {
                return "{$resourceNamespace}\\{$resourceBasename}";
            }

            $resourceNamespace = (string) str($resourceNamespace)
                ->beforeLast('\\');

            if (class_exists("{$resourceNamespace}\\{$resourceBasename}")) {
                return "{$resourceNamespace}\\{$resourceBasename}";
            }
        }

        $resourceFqns = array_filter(
            array_values($this->panel->getResources()),
            fn (string $resource): bool => str($resource)->startsWith("{$resourcesNamespace}\\"),
        );

        if (! $resourceFqns) {
            return (string) str(text(
                label: "No resources were found within [{$resourcesNamespace}]. {$question}",
                placeholder: app()->getNamespace() . 'Filament\\Resources\\Posts\\PostResource',
                required: true,
                validate: function (string $value): ?string {
                    $value = (string) str($value)
                        ->trim('/')
                        ->trim('\\')
                        ->trim(' ')
                        ->replace('/', '\\');

                    return match (true) {
                        ! class_exists($value) => 'The resource class doesn\'t exist, please use the fully-qualified class name.',
                        ! is_subclass_of($value, Resource::class) => 'The resource class or one of its parents must extend [' . Resource::class . '].',
                        default => null,
                    };
                },
                hint: 'Please provide the fully-qualified class name of the resource.',
            ))
                ->trim('/')
                ->trim('\\')
                ->trim(' ')
                ->replace('/', '\\');
        }

        return search(
            label: $question,
            options: function (?string $search) use ($resourceFqns, $resourcesNamespace): array {
                $search = str($search)->trim()->replace(['\\', '/'], '');

                return collect($resourceFqns)
                    ->when(
                        filled($search = (string) str($search)->trim()->replace(['\\', '/'], '')),
                        fn (Collection $resourceFqns) => $resourceFqns->filter(fn (string $fqn): bool => str($fqn)->replace(['\\', '/'], '')->contains($search, ignoreCase: true)),
                    )
                    ->mapWithKeys(function (string $fqn) use ($resourcesNamespace): array {
                        $label = (string) str($fqn)->after("{$resourcesNamespace}\\");

                        if (str($label)->contains('\\')) {
                            $finalSegment = (string) str($label)->afterLast('\\');

                            $penultimateSegment = (string) str($label)->beforeLast('\\');

                            if (str($penultimateSegment)->contains('\\')) {
                                $penultimateSegment = (string) str($penultimateSegment)->afterLast('\\');
                            }

                            if (str($finalSegment)->endsWith('Resource') && ($finalSegment !== 'Resource')) {
                                $expectedPenultimateSegment = (string) str($finalSegment)
                                    ->beforeLast('Resource')
                                    ->pluralStudly();
                            }

                            if ($penultimateSegment === ($expectedPenultimateSegment ?? null)) {
                                $label = (string) str($label)->beforeLast('\\');
                            }
                        }

                        return [$fqn => $label];
                    })
                    ->all();
            },
        );
    }
}
