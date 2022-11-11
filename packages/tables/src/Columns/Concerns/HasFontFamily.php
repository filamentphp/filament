<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait HasFontFamily
{
    protected string | Closure | null $fontFamily = null;

    public function fontFamily(string | Closure | null $fontFamily): static
    {
        $this->fontFamily = $fontFamily;

        return $this;
    }

    public function getFontFamily(): ?string
    {
        return $this->evaluate($this->fontFamily);
    }
}
