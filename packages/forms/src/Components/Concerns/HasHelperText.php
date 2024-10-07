<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasHelperText
{
    public function helperText(string | Htmlable | Closure | null $text): static
    {
        $this->belowErrorMessage($text);

        return $this;
    }
}
