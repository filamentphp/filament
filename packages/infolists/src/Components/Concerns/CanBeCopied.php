<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;

trait CanBeCopied
{
    protected bool | Closure $isCopyable = false;

    protected string | Closure | null $copyMessage = null;

    protected int | Closure | null $copyMessageDuration = null;

    public function copyable(bool | Closure $condition = true): static
    {
        $this->isCopyable = $condition;

        return $this;
    }

    public function copyMessage(string | Closure | null $message): static
    {
        $this->copyMessage = $message;

        return $this;
    }

    public function copyMessageDuration(int | Closure | null $duration): static
    {
        $this->copyMessageDuration = $duration;

        return $this;
    }

    public function getCopyMessage(): string
    {
        return $this->evaluate($this->copyMessage) ?? __('filament-infolists::components.messages.copied');
    }

    public function getCopyMessageDuration(): int
    {
        return $this->evaluate($this->copyMessageDuration) ?? 2000;
    }

    public function isCopyable(): bool
    {
        return (bool) $this->evaluate($this->isCopyable);
    }
}
