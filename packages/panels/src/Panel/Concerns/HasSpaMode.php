<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasSpaMode
{
    protected bool | Closure $hasSpaMode = false;

    protected bool | Closure $hasSpaPrefetching = false;

    /**
     * @var array<string> | Closure
     */
    protected array | Closure $spaModeUrlExceptions = [];

    public function spa(bool | Closure $condition = true, bool | Closure $hasPrefetching = false): static
    {
        $this->hasSpaMode = $condition;
        $this->hasSpaPrefetching = $hasPrefetching;

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

    public function hasSpaPrefetching(): bool
    {
        return (bool) $this->evaluate($this->hasSpaPrefetching);
    }

    /**
     * @return array<string>
     */
    public function getSpaUrlExceptions(): array
    {
        return $this->evaluate($this->spaModeUrlExceptions);
    }
}
