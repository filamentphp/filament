<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

trait HasLoadingPlaceholder
{
    protected View | Htmlable | Closure | null $loadingPlaceholder = null;

    public function loadingPlaceholder(View | Htmlable | Closure | null $loadingPlaceholder): static
    {
        $this->loadingPlaceholder = $loadingPlaceholder;

        return $this;
    }

    public function getLoadingPlaceholder(): View | Htmlable | null
    {
        return $this->evaluate($this->loadingPlaceholder);
    }
}
