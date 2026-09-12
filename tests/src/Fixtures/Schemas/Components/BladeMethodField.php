<?php

namespace Filament\Tests\Fixtures\Schemas\Components;

use Filament\Forms\Components\Field;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;

class BladeMethodField extends Field
{
    protected string $view = 'forms.components.blade-method-field';

    #[ExposedLivewireMethod]
    public function replaceText(string $text): string
    {
        $this->state($text);

        return $this->getStatePath() . ': ' . $text;
    }
}
