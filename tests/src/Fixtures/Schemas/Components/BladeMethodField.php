<?php

namespace Filament\Tests\Fixtures\Schemas\Components;

use Filament\Forms\Components\Field;
use Filament\Support\Components\Attributes\Exposed;

class BladeMethodField extends Field
{
    protected string $view = 'forms.components.blade-method-field';

    #[Exposed]
    public function replaceText(string $text): string
    {
        $this->state($text);

        return $this->getStatePath() . ': ' . $text;
    }
}
