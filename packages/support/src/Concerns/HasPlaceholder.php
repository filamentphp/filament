<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasPlaceholder
{
    protected string | Htmlable | Closure | null $placeholder = null;

    protected bool $shouldTranslatePlaceholder = false;

    public function placeholder(string | Htmlable | Closure | null $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function translatePlaceholder(bool $shouldTranslatePlaceholder = true): static
    {
        $this->shouldTranslatePlaceholder = $shouldTranslatePlaceholder;

        return $this;
    }

    public function getPlaceholder(): string | Htmlable | null
    {
        $placeholder = $this->evaluate($this->placeholder);

        return (is_string($placeholder) && $this->shouldTranslatePlaceholder) ?
            __($placeholder) :
            $placeholder;
    }
}
