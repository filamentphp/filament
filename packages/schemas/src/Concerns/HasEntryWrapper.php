<?php

namespace Filament\Schemas\Concerns;

use Closure;

trait HasEntryWrapper
{
    protected string | Closure | null $entryWrapperView = null;

    public function entryWrapperView(string | Closure | null $view): static
    {
        $this->entryWrapperView = $view;

        return $this;
    }

    public function getCustomEntryWrapperView(): ?string
    {
        return $this->evaluate($this->entryWrapperView) ??
            $this->getParentComponent()?->getCustomEntryWrapperView();
    }
}
