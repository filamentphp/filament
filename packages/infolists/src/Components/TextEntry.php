<?php

namespace Filament\Infolists\Components;

use Closure;

class TextEntry extends Entry
{
    use Concerns\CanBeCopied;
    use Concerns\CanFormatState;
    use Concerns\HasColor;
    use Concerns\HasFontFamily;
    use Concerns\HasIcon;
    use Concerns\HasSize;
    use Concerns\HasWeight;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.text-entry';

    protected bool | Closure $canWrap = false;

    protected bool | Closure $isBadge = false;

    protected bool | Closure $isBulleted = false;

    protected bool | Closure $isProse = false;

    protected bool | Closure $isListWithLineBreaks = false;

    protected int | Closure | null $listLimit = null;

    public function badge(bool | Closure $condition = true): static
    {
        $this->isBadge = $condition;

        return $this;
    }

    public function bulleted(bool | Closure $condition = true): static
    {
        $this->isBulleted = $condition;

        return $this;
    }

    public function listWithLineBreaks(bool | Closure $condition = true): static
    {
        $this->isListWithLineBreaks = $condition;

        return $this;
    }

    public function limitList(int | Closure | null $limit = 3): static
    {
        $this->listLimit = $limit;

        return $this;
    }

    public function wrap(bool | Closure $condition = true): static
    {
        $this->canWrap = $condition;

        return $this;
    }

    public function prose(bool | Closure $condition = true): static
    {
        $this->isProse = $condition;

        return $this;
    }

    public function canWrap(): bool
    {
        return (bool) $this->evaluate($this->canWrap);
    }

    public function isBadge(): bool
    {
        return (bool) $this->evaluate($this->isBadge);
    }

    public function isBulleted(): bool
    {
        return (bool) $this->evaluate($this->isBulleted);
    }

    public function isProse(): bool
    {
        return (bool) $this->evaluate($this->isProse);
    }

    public function isListWithLineBreaks(): bool
    {
        return $this->evaluate($this->isListWithLineBreaks) || $this->isBulleted();
    }

    public function getListLimit(): ?int
    {
        return $this->evaluate($this->listLimit);
    }
}
