<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasCaption
{
    protected string | Htmlable | Closure | null $caption = null;

    protected bool $shouldTranslateCaption = false;

    public function caption(string | Htmlable | Closure | null $caption): static
    {
        $this->caption = $caption;

        return $this;
    }

    public function translateCaption(bool $shouldTranslateCaption = true): static
    {
        $this->shouldTranslateCaption = $shouldTranslateCaption;

        return $this;
    }

    public function getCaption(): string | Htmlable | null
    {
        if ($this->caption === null) {
            return null;
        }

        $caption = $this->evaluate($this->caption);

        return $this->shouldTranslateCaption ? __($caption) : $caption;
    }
}
