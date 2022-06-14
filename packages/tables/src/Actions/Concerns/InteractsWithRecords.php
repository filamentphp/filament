<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Collection;

trait InteractsWithRecords
{
    protected string | Closure | null $modelLabel = null;

    protected string | Closure | null $pluralModelLabel = null;

    protected Collection | Closure | null $records = null;

    public function records(Collection | Closure | null $records): static
    {
        $this->records = $records;

        return $this;
    }

    public function modelLabel(string | Closure | null $label): static
    {
        $this->modelLabel = $label;

        return $this;
    }

    public function pluralModelLabel(string | Closure | null $label): static
    {
        $this->pluralModelLabel = $label;

        return $this;
    }

    public function getModel(): string
    {
        return $this->getLivewire()->getTableModel();
    }

    public function getModelLabel(): string
    {
        $label = $this->evaluate($this->modelLabel);

        if (filled($label)) {
            return $label;
        }

        return $this->getLivewire()->getTableModelLabel();
    }

    public function getPluralModelLabel(): string
    {
        $label = $this->evaluate($this->pluralModelLabel);

        if (filled($label)) {
            return $label;
        }

        return $this->getLivewire()->getTablePluralModelLabel();
    }

    public function getRecords(): ?Collection
    {
        return $this->evaluate(
            $this->records,
            exceptParameters: ['records'],
        );
    }
}
