<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;

trait HasFontFamily
{
    protected string | Closure | null $fontFamily = null;

    public function fontFamily(string | Closure | null $family): static
    {
        $this->fontFamily = $family;

        return $this;
    }

    public function getFontFamily(mixed $state): ?string
    {
        return $this->evaluate($this->fontFamily, [
            'state' => $state,
        ]);
    }
}
