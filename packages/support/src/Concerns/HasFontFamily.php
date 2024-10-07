<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Enums\FontFamily;

trait HasFontFamily
{
    protected FontFamily | string | Closure | null $fontFamily = null;

    public function fontFamily(FontFamily | string | Closure | null $family): static
    {
        $this->fontFamily = $family;

        return $this;
    }

    public function getFontFamily(mixed $state = null): FontFamily | string | null
    {
        return $this->evaluate($this->fontFamily, [
            'state' => $state,
        ]);
    }
}
