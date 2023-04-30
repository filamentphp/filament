<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Forms\Form;

trait CanToggleColumns
{
    /**
     * @var int | array<string, int | null> | Closure
     */
    protected int | array | Closure $columnToggleFormColumns = 1;

    protected string | Closure | null $columnToggleFormWidth = null;

    /**
     * @param  int | array<string, int | null> | Closure  $columns
     */
    public function columnToggleFormColumns(int | array | Closure $columns): static
    {
        $this->columnToggleFormColumns = $columns;

        return $this;
    }

    public function columnToggleFormWidth(string | Closure | null $width): static
    {
        $this->columnToggleFormWidth = $width;

        return $this;
    }

    public function getColumnToggleForm(): Form
    {
        return $this->getLivewire()->getTableColumnToggleForm();
    }

    /**
     * @return int | array<string, int | null>
     */
    public function getColumnToggleFormColumns(): int | array
    {
        return $this->evaluate($this->columnToggleFormColumns) ?? 1;
    }

    public function getColumnToggleFormWidth(): ?string
    {
        return $this->evaluate($this->columnToggleFormWidth) ?? match ($this->getColumnToggleFormColumns()) {
            2 => '2xl',
            3 => '4xl',
            4 => '6xl',
            default => null,
        };
    }

    public function hasToggleableColumns(): bool
    {
        foreach ($this->getColumns() as $column) {
            if (! $column->isToggleable()) {
                continue;
            }

            return true;
        }

        return false;
    }
}
