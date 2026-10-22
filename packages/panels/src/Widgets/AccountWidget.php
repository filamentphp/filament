<?php

namespace Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Support\Attributes\ServerOnly;

class AccountWidget extends Widget
{
    protected static ?int $sort = -3;

    protected static bool $isLazy = false;

    /**
     * @var view-string
     */
    protected string $view = 'filament-panels::widgets.account-widget';

    #[ServerOnly]
    public static function canView(): bool
    {
        return Filament::auth()->check();
    }
}
