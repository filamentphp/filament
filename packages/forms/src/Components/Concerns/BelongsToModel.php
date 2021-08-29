<?php

namespace Filament\Forms\Components\Concerns;

use Illuminate\Database\Eloquent\Model;

trait BelongsToModel
{
    protected $model = null;

    public function model(Model | string | callable $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function saveRelationships(): void
    {
    }

    public function getModel(): Model | string | null
    {
        if ($model = $this->evaluate($this->model)) {
            return $model;
        }

        return $this->getContainer()->getModel();
    }
}
