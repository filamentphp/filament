<?php

namespace Filament\Forms\Components;

class Card extends Component
{
    public static function make($schema = [])
    {
        return (new static())->schema($schema);
    }
}
