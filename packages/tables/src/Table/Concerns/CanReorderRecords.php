<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait CanReorderRecords
{
    protected bool | Closure $isReorderable = true;

    protected string | Closure | null $reorderColumn = null;

    public function reorderable(string | Closure | null $column = null, bool | Closure | null $condition = null): static
    {
        $this->reorderColumn = $column;

        if ($condition !== null) {
            $this->isReorderable = $condition;
        }

        return $this;
    }

    public function getReorderColumn(): ?string
    {
        return $this->evaluate($this->reorderColumn);
    }

    public function isReorderable(): bool
    {
        return filled($this->getReorderColumn()) && $this->evaluate($this->isReorderable);
    }

    public function isReordering(): bool
    {
        return $this->getLivewire()->isTableReordering();
    }
}
