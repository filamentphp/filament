<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait CanScrollToTop
{
    protected bool | Closure $shouldScrollToTopOnPageChange = false;

    public function scrollToTopOnPageChange(bool | Closure $condition = true): static
    {
        $this->shouldScrollToTopOnPageChange = $condition;

        return $this;
    }

    public function shouldScrollToTopOnPageChange(): bool
    {
        return (bool) $this->evaluate($this->shouldScrollToTopOnPageChange);
    }
}

