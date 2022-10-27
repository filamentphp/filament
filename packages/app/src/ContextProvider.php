<?php

namespace Filament;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

abstract class ContextProvider extends ServiceProvider
{
    abstract public function context(Context $context): Context;

    public function register()
    {
        $this->app->resolving('filament', function () {
            Filament::registerContext(
                $this->context(new Context()),
            );
        });
    }
}
