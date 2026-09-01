<?php

namespace Filament\Support\Livewire\Partials;

use Livewire\Component;
use Livewire\Livewire;
use Livewire\Mechanisms\DataStore;

class DataStoreOverride extends DataStore
{
    /**
     * @param  Component  $instance
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($instance, $key, $default = null)
    {
        if ($key !== 'skipRender') {
            return parent::get($instance, $key, $default);
        }

        if (! Livewire::isLivewireRequest()) {
            return parent::get($instance, $key, $default);
        }

        if ($trueOrPlaceholderHtml = parent::get($instance, $key, $default)) {
            return $trueOrPlaceholderHtml;
        }

        $supportPartials = app(PartialsComponentHook::class);
        $supportPartials->setComponent($instance);

        return $supportPartials->shouldSkipRender();
    }
}
