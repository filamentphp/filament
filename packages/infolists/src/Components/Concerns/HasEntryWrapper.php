<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;

trait HasEntryWrapper
{
    protected string | Closure | null $entryWrapperView = null;

    public function entryWrapperView(string | Closure | null $view): static
    {
        $this->entryWrapperView = $view;

        return $this;
    }

    public function getEntryWrapperView(): string
    {
        if ($this->hasInlineLabel()) {
            return 'filament-infolists::entry-wrapper.inline';
        }

        return $this->getCustomEntryWrapperView() ??
            $this->getContainer()->getCustomEntryWrapperView() ??
            'filament-infolists::entry-wrapper';
    }

    public function getCustomEntryWrapperView(): ?string
    {
        return $this->evaluate($this->entryWrapperView);
    }
}
