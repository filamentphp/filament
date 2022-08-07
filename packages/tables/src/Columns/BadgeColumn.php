<?php

namespace Filament\Tables\Columns;

class BadgeColumn extends TextColumn
{
    use Concerns\CanFormatState;
    use Concerns\HasColors;
    use Concerns\HasIcons;

    protected string $view = 'tables::columns.badge-column';

    protected bool $inset = false;

    protected bool $inverseColor = false;

    public function inset(bool $condition = true): static
    {
        $this->inset = $condition;

        return $this;
    }

    public function inverseColor(bool $condition = true): static
    {
        $this->inverseColor = $condition;

        return $this;
    }

    public function getInset(): bool
    {
        return $this->inset;
    }

    public function getInverseColor(): bool
    {
        return $this->inverseColor;
    }
}
