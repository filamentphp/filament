<?php

namespace Filament\Widgets;

use Filament\Pages\Auth\Register;

class AccountWidget extends Widget
{
    protected static ?int $sort = -3;

    /**
     * @var view-string $view
     */
    protected static string $view = 'filament::widgets.account-widget';
}
