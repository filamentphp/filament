<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;

trait HasPlaceholder
{
    protected string | Closure | null $placeholder = null;

    protected bool $shouldTranslatePlaceholder = false;

    public function placeholder(string | Closure | null $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function translatePlaceholder(bool $shouldTranslatePlaceholder = true): static
    {
        $this->shouldTranslatePlaceholder = $shouldTranslatePlaceholder;

        return $this;
    }

    public function getPlaceholder(): ?string
    {
        $placeholder = $this->evaluate($this->placeholder);

        return (is_string($placeholder) && $this->shouldTranslatePlaceholder) ?
            __($placeholder) :
            $placeholder;
    }
}
