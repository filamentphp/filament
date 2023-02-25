<?php

namespace Filament\Infolists\Components;

class ColorEntry extends Entry
{
    use Concerns\CanBeCopied;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.color-entry';
}
