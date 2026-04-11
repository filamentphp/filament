<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanAllowHtml
{
    // Security: Enabling HTML rendering on form components means the content
    // will not be escaped. Only enable for trusted content you control —
    // never for raw user input without proper sanitization.

    protected bool | Closure $isHtmlAllowed = false;

    public function allowHtml(bool | Closure $condition = true): static
    {
        $this->isHtmlAllowed = $condition;

        return $this;
    }

    public function isHtmlAllowed(): bool
    {
        return (bool) $this->evaluate($this->isHtmlAllowed);
    }
}
