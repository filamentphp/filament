<?php

namespace Filament;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

abstract class ContextServiceProvider extends ServiceProvider
{
    abstract public function configureContext(Context $context): void;

    public function register()
    {
        $this->app->resolving('filament', function () {
            $context = new Context();
            $this->configureContext($context);

            Filament::registerContext($context);
        });
    }
}
