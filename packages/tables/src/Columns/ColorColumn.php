<?php

namespace Filament\Tables\Columns;

class ColorColumn extends Column
{
    protected string $view = 'tables::columns.color-column';

    protected bool $copyable = false;

    protected ?string $copyMessage = null;

    protected int $copyMessageShowTimeMs = 2000;

    public function copyable(): self
    {
        $this->copyable = true;

        return $this;
    }

    public function copyMessage(string $message): self
    {
        $this->copyMessage = $message;

        return $this;
    }

    public function getCopyMessage(): string
    {
        return $this->copyMessage ?? trans('tables::table.columns.color.copied');
    }

    public function copyMessageShowTimeMs(int $timeInMs): self
    {
        $this->copyMessageShowTimeMs = $timeInMs;

        return $this;
    }

    public function getCopyMessageShowTimeMs(): int
    {
        return $this->copyMessageShowTimeMs;
    }

    public function isCopyable(): bool
    {
        return $this->copyable;
    }
}
