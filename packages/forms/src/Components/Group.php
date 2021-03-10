<?php

namespace Filament\Forms\Components;

class Group extends Component
{
    public static function make($schema = [])
    {
        return (new static())->schema($schema);
    }
}
