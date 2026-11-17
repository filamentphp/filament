<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;

class StandardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ]);
    }
}
