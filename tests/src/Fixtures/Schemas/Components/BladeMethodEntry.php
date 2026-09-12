<?php

namespace Filament\Tests\Fixtures\Schemas\Components;

use Filament\Infolists\Components\Entry;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;

class BladeMethodEntry extends Entry
{
    protected string $view = 'forms.components.blade-method-field';

    #[ExposedLivewireMethod]
    public function replaceText(string $text): string
    {
        return $this->getStatePath() . ': ' . $this->getState() . ': ' . $text;
    }
}
