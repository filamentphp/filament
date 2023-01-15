<?php

namespace Filament\Infolists\Components;

use Closure;

class ProseEntry extends Entry
{
    use Concerns\HasSize;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.prose-entry';

    protected bool | Closure $isMarkdown = false;

    public function markdown(bool | Closure $condition = true): static
    {
        $this->isMarkdown = $condition;

        return $this;
    }

    public function isMarkdown(): bool
    {
        return (bool) $this->evaluate($this->isMarkdown);
    }
}
