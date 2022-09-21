<?php

namespace Filament\Tables\Columns;

use Closure;

class ColorColumn extends Column
{
    protected string $view = 'tables::columns.color-column';

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
        return $this->evaluate($this->copyMessage) ?? __('tables::table.columns.color.messages.copied');
    }

    public function getCopyMessageDuration(): int
    {
        return $this->evaluate($this->copyMessageDuration) ?? 2000;
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
