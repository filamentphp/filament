<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use function Filament\Support\get_model_label;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

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

    public function getModel(): ?string
    {
        return $this->getLivewire()->getTableQuery()->getModel()::class;
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

    public function getModelLabel(): ?string
    {
        $label = $this->evaluate($this->modelLabel);

        if (filled($label)) {
            return $label;
        }

        $model = $this->getModel();

        if (! $model) {
            return null;
        }

        return get_model_label($model);
    }

    public function getPluralModelLabel(): ?string
    {
        $label = $this->evaluate($this->pluralModelLabel);

        if (filled($label)) {
            return $label;
        }

        $singularLabel = $this->getModelLabel();

        return filled($singularLabel) ? Str::plural($singularLabel) : null;
    }

    public function getRecords(): ?Collection
    {
        return $this->evaluate(
            $this->records,
            exceptParameters: ['records'],
        );
    }
}
