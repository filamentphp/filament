<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasSpaMode
{
    protected bool | Closure $hasSpaMode = false;

    /**
     * @var array<string> | Closure
     */
    protected array | Closure $spaModeUrlExceptions = [];

    public function spa(bool | Closure $condition = true): static
    {
        $this->hasSpaMode = $condition;

        return $this;
    }

    /**
     * @param  array<string>| Closure  $exceptions
     */
    public function spaUrlExceptions(array | Closure $exceptions): static
    {
        $this->spaModeUrlExceptions = $exceptions;

        return $this;
    }

    public function hasSpaMode(): bool
    {
        return (bool) $this->evaluate($this->hasSpaMode);
    }

    /**
     * @return array<string>
     */
    public function getSpaUrlExceptions(): array
    {
        return $this->evaluate($this->spaModeUrlExceptions);
    }
}
