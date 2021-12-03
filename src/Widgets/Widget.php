<?php

namespace Filament\Widgets;

use Livewire\Component;

class Widget extends Component
{
    public static $isHidden = false;

    public static $sort = 0;

    public static $view;

    public static function isVisible()
    {
        return ! static::$isHidden;
    }

    public function render()
    {
        return view(static::$view);
    }
}
