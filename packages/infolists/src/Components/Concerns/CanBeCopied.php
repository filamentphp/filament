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

    public function getCopyMessage(mixed $state): string
    {
        return $this->evaluate($this->copyMessage, [
            'state' => $state,
        ]) ?? __('filament-infolists::components.messages.copied');
    }

    public function getCopyMessageDuration(mixed $state): int
    {
        return $this->evaluate($this->copyMessageDuration, [
            'state' => $state,
        ]) ?? 2000;
    }

    public function isCopyable(mixed $state): bool
    {
        return (bool) $this->evaluate($this->isCopyable, [
            'state' => $state,
        ]);
    }
}
