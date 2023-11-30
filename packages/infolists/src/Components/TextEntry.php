<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Contracts\HasAffixActions;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Support\Concerns\CanBeCopied;

class TextEntry extends Entry implements HasAffixActions
{
    use CanBeCopied;
    use Concerns\CanFormatState;
    use Concerns\HasAffixes;
    use Concerns\HasColor;
    use Concerns\HasFontFamily;
    use Concerns\HasIcon;
    use Concerns\HasIconColor;
    use Concerns\HasWeight;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.text-entry';

    protected bool | Closure $isBadge = false;

    protected bool | Closure $isBulleted = false;

    protected bool | Closure $isProse = false;

    protected bool | Closure $isListWithLineBreaks = false;

    protected int | Closure | null $listLimit = null;

    protected TextEntrySize | string | Closure | null $size = null;

    protected bool | Closure $isLimitedListExpandable = false;

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

    public function prose(bool | Closure $condition = true): static
    {
        $this->isProse = $condition;

        return $this;
    }

    public function size(TextEntrySize | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(mixed $state): TextEntrySize | string | null
    {
        return $this->evaluate($this->size, [
            'state' => $state,
        ]);
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

    public function expandableLimitedList(bool | Closure $condition = true): static
    {
        $this->isLimitedListExpandable = $condition;

        return $this;
    }

    public function isLimitedListExpandable(): bool
    {
        return (bool) $this->evaluate($this->isLimitedListExpandable);
    }
}
