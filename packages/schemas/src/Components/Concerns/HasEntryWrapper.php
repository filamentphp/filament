<?php

namespace Filament\Schemas\Components\Concerns;

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
        return $this->getCustomEntryWrapperView() ??
            $this->getContainer()->getCustomEntryWrapperView() ??
            'filament-infolists::entry-wrapper';
    }

    public function getEntryWrapperAbsoluteView(): string
    {
        $view = $this->getEntryWrapperView();

        if (str($view)->contains('::')) {
            return str($view)->replaceFirst('::', '::components.');
        }

        return "components.{$view}";
    }

    public function getCustomEntryWrapperView(): ?string
    {
        return $this->evaluate($this->entryWrapperView);
    }
}
