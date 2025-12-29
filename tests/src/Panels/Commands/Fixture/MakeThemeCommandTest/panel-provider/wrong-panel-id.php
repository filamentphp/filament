<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;

class WrongPanelIdPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ]);
    }
}
