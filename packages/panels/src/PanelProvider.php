<?php

namespace Filament;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

abstract class PanelProvider extends ServiceProvider
{
    abstract public function panel(Panel $panel): Panel;

    public function register(): void
    {
        Filament::registerPanel(
            $this->panel(Panel::make()),
        );
    }
}
