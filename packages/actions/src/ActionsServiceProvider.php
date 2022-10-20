<?php

namespace Filament\Actions;

use Filament\Actions\Testing\TestsActions;
use Filament\Support\PluginServiceProvider;
use Livewire\Testing\TestableLivewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ActionsServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-actions';

    public function packageBooted(): void
    {
        parent::packageBooted();

        TestableLivewire::mixin(new TestsActions());
    }
}
