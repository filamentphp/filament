<?php

namespace Filament\Forms\Components\Concerns;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

trait HasFallbackLabel
{
    public function getLabel(): string | Htmlable | null
    {
        $label = parent::getLabel() ?? (string) Str::of($this->getName())
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();

        return (is_string($label) && $this->shouldTranslateLabel) ?
            __($label) :
            $label;
    }
}
