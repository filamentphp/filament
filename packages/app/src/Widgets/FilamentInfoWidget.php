<?php

namespace Filament\Widgets;

use Filament\Pages\Auth\Register;

class FilamentInfoWidget extends Widget
{
    protected static ?int $sort = -2;

    /**
     * @var view-string $view
     */
    protected static string $view = 'filament::widgets.filament-info-widget';
}
