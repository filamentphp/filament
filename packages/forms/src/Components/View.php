<?php

namespace Filament\Forms\Components;

class View extends Component
{
    public static function make($view)
    {
        return (new static())
            ->view($view);
    }
}
