<?php

namespace Filament\Support\Commands\Concerns;

use Filament\Support\Facades\FilamentCli;

use function Laravel\Prompts\select;

trait CanAskForLivewireComponentLocation
{
    /**
     * @return array{
     *     0: string,
     *     1: string,
     *     2: ?string,
     * }
     */
    protected function askForLivewireComponentLocation(string $question = 'Where would you like to create the Livewire component?'): array
    {
        $locations = FilamentCli::getLivewireComponentLocations();

        if (blank($locations)) {
            return [
                app()->getNamespace() . 'Livewire',
                app_path('Livewire'),
                '',
            ];
        }

        $options = [
            '' => app()->getNamespace() . 'Livewire',
            ...array_combine(
                array_keys($locations),
                array_keys($locations),
            ),
        ];

        $namespace = select(
            label: $question,
            options: $options,
        );

        if (blank($namespace)) {
            return [
                app()->getNamespace() . 'Livewire',
                app_path('Livewire'),
                '',
            ];
        }

        return [
            $namespace,
            $locations[$namespace]['path'],
            $locations[$namespace]['viewNamespace'] ?? null,
        ];
    }
}
