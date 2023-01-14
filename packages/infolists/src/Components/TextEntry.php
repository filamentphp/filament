<?php

namespace Filament\Infolists\Components;

class TextEntry extends Entry
{
    use Concerns\CanFormatState;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.text-entry';
}
