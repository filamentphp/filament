<?php

namespace Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Support\Components\Attributes\Unexposed;

class AccountWidget extends Widget
{
    protected static ?int $sort = -3;

    /**
     * @deprecated Use `$isLazyLoaded` instead.
     */
    protected static bool $isLazy = false;

    /**
     * @var view-string
     */
    protected string $view = 'filament-panels::widgets.account-widget';

    #[Unexposed]
    public static function canView(): bool
    {
        return Filament::auth()->check();
    }
}
