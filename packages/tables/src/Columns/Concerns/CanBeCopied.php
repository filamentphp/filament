<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanBeCopied
{
    protected bool | Closure $isCopyable = false;

    protected string | Closure | null $copyMessage = null;

    protected int | Closure | null $copyMessageDuration = null;

    protected string | Closure | null $copyableState = null;

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

    public function copyableState(string | Closure | null $state): static
    {
        $this->copyableState = $state;

        return $this;
    }

    public function getCopyMessage(): string
    {
        return $this->evaluate($this->copyMessage) ?? __('tables::table.columns.messages.copied');
    }

    public function getCopyMessageDuration(): int
    {
        return $this->evaluate($this->copyMessageDuration) ?? 2000;
    }

    public function getCopyableState(): ?string
    {
        $state = $this->getState();

        return $this->evaluate($this->copyableState, [
            'state' => $state,
        ]) ?? $state;
    }

    public function isCopyable(): bool
    {
        return $this->evaluate($this->isCopyable);
    }

    public function isClickDisabled(): bool
    {
        return parent::isClickDisabled() || $this->isCopyable();
    }
}
