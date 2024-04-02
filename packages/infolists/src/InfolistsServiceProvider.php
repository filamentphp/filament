<?php

namespace Filament\Infolists;

use Filament\Infolists\Testing\TestsInfolistActions;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class InfolistsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-infolists')
            ->hasCommands($this->getCommands())
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        Testable::mixin(new TestsInfolistActions());
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        $commands = [
            Commands\MakeEntryCommand::class,
        ];

        $aliases = [];

        foreach ($commands as $command) {
            $class = 'Filament\\Infolists\\Commands\\Aliases\\' . class_basename($command);

            if (! class_exists($class)) {
                continue;
            }

            $aliases[] = $class;
        }

        return [
            ...$commands,
            ...$aliases,
        ];
    }
}
