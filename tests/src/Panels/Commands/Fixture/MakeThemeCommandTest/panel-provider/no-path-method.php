<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;

class NoPathMethodPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ]);
    }
}
