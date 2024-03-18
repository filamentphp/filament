<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait HasCaption
{
    protected string | Closure | null $caption = null;

    protected bool $shouldTranslateCaption = false;

    public function caption(string | Closure | null $caption): static
    {
        $this->caption = $caption;

        return $this;
    }

    public function translateCaption(bool $shouldTranslateCaption = true): static
    {
        $this->shouldTranslateCaption = $shouldTranslateCaption;

        return $this;
    }

    public function getCaption(): ?string
    {
        if ($this->caption === null) {
            return null;
        }

        $caption = $this->evaluate($this->caption);

        return $this->shouldTranslateCaption ? __($caption) : $caption;
    }
}
