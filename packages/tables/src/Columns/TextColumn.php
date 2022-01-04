<?php

namespace Filament\Tables\Columns;

class TextColumn extends Column
{
    use Concerns\CanFormatState;

    protected string $view = 'tables::columns.text-column';

    protected bool $canWrap = false;

    public function wrap(bool $condition = true): static
    {
        $this->canWrap = $condition;

        return $this;
    }

    public function canWrap(): bool
    {
        return $this->canWrap;
    }
}
