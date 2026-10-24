<?php

namespace Filament\Tests\Fixtures\Schemas\Components;

use Filament\Infolists\Components\Entry;
use Filament\Support\Components\Attributes\Exposed;

class BladeMethodEntry extends Entry
{
    protected string $view = 'forms.components.blade-method-field';

    #[Exposed]
    public function replaceText(string $text): string
    {
        return $this->getStatePath() . ': ' . $this->getState() . ': ' . $text;
    }
}
