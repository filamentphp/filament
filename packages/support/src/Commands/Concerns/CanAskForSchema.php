<?php

namespace Filament\Support\Commands\Concerns;

use function Filament\Support\discover_app_classes;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\suggest;

trait CanAskForSchema
{
    /**
     * @return ?class-string
     */
    protected function askForSchema(string $intialQuestion, string $question, string $questionPlaceholder): ?string
    {
        if (! confirm(
            label: $intialQuestion,
            default: false,
        )) {
            return null;
        }

        $schemaFqns = array_filter(
            discover_app_classes(),
            fn (string $schemaFqn): bool => method_exists($schemaFqn, 'configure'),
        );

        return suggest(
            label: $question,
            options: function (?string $search) use ($schemaFqns): array {
                if (blank($search)) {
                    return $schemaFqns;
                }

                $search = str($search)->trim()->replace(['\\', '/'], '');

                return array_filter($schemaFqns, fn (string $schemaFqn): bool => str($schemaFqn)->replace(['\\', '/'], '')->contains($search, ignoreCase: true));
            },
            placeholder: $questionPlaceholder,
            hint: 'Please provide the fully-qualified class name.',
        );
    }
}
