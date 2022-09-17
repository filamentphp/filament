<?php

namespace Filament\Tables\Columns;

use Closure;

class ColorColumn extends Column
{
    protected string $view = 'tables::columns.color-column';

    protected bool | Closure $isCopyable = false;

    protected string | Closure | null $copyMessage = null;

    protected int | Closure | null $copyMessageDuration = 2000;

    public function copyable(bool | Closure $copyable = true): self
    {
        $this->isCopyable = $copyable;

        return $this;
    }

    public function copyMessage(string | Closure | null $message): self
    {
        $this->copyMessage = $message;

        return $this;
    }

    public function getCopyMessage(): ?string
    {
        return $this->evaluate($this->copyMessage ?? __('tables::table.columns.color.copied'));
    }

    public function copyMessageDuration(int | Closure | null $duration): self
    {
        $this->copyMessageDuration = $duration;

        return $this;
    }

    public function getCopyMessageDuration(): ?int
    {
        return $this->evaluate($this->copyMessageDuration ?? 0);
    }

    public function isCopyable(): bool
    {
        return $this->evaluate($this->isCopyable);
    }
}
