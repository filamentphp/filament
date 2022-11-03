<?php

namespace Filament\Tables\Columns;

use Closure;

class TextColumn extends Column
{
    use Concerns\CanFormatState;
    use Concerns\HasColor;
    use Concerns\HasDescription;
    use Concerns\HasIcon;
    use Concerns\HasSize;
    use Concerns\HasWeight;

    protected string $view = 'tables::columns.text-column';

    protected bool | Closure $canWrap = false;
    
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

    public function wrap(bool | Closure $condition = true): static
    {
        $this->canWrap = $condition;

        return $this;
    }

    public function canWrap(): bool
    {
        return $this->evaluate($this->canWrap);
    }

    protected function mutateArrayState(array $state): string
    {
        return implode(', ', $state);
    }
}
