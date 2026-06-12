<?php

namespace Filament\Support\Commands\Concerns;

use Illuminate\Database\Eloquent\Model;

use function Filament\Support\discover_app_classes;
use function Laravel\Prompts\info;
use function Laravel\Prompts\suggest;

trait CanAskForRelatedModel
{
    /**
     * @return ?class-string
     */
    protected function askForRelatedModel(string $relationship): ?string
    {
        $modelFqns = discover_app_classes(parentClass: Model::class);

        $modelFqns = array_combine(
            $modelFqns,
            $modelFqns,
        );

        info("Filament couldn't automatically find the related model for the [{$relationship}] relationship.");

        return suggest(
            label: 'What is the related model?',
            options: function (?string $search) use ($modelFqns): array {
                if (blank($search)) {
                    return $modelFqns;
                }

                $search = str($search)->trim()->replace(['\\', '/'], '');

                return array_filter($modelFqns, fn (string $modelFqn): bool => str($modelFqn)->replace(['\\', '/'], '')->contains($search, ignoreCase: true));
            },
            placeholder: app()->getNamespace() . 'Models\\User',
            hint: 'Please provide the fully-qualified class name.',
        );
    }
}
