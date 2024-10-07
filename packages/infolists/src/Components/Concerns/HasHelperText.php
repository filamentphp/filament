<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasHelperText
{
    public function helperText(string | Htmlable | Closure | null $text): static
    {
        $this->belowContent($text);

        return $this;
    }
}
