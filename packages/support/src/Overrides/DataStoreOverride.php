<?php

namespace Filament\Support\Overrides;

use Filament\Support\Partials\SupportPartials;
use Livewire\Livewire;
use Livewire\Mechanisms\DataStore;

class DataStoreOverride extends DataStore
{
    public function get($instance, $key, $default = null)
    {
        if (($key === 'skipRender') && Livewire::isLivewireRequest() && (! app()->runningUnitTests())) {
            $supportPartials = app(SupportPartials::class);
            $supportPartials->setComponent($instance);

            return parent::get($instance, $key, $default) || $supportPartials->shouldSkipRender();
        }

        return parent::get($instance, $key, $default);
    }
}
