<?php

namespace Filament\Forms\Concerns;

use Illuminate\Database\Eloquent\Model;

trait BelongsToModel
{
    public Model | string | null $model = null;

    public function model(Model | string | null $model = null): static
    {
        $this->model = $model;

        return $this;
    }

    public function saveRelationships(): void
    {
        foreach ($this->getComponents() as $component) {
            if ($component->getRecord()) {
                $component->saveRelationships();
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                $container->saveRelationships();
            }
        }
    }

    public function getModel(): ?string
    {
        $model = $this->model;

        if ($model instanceof Model) {
            return $model::class;
        }

        if (filled($model)) {
            return $model;
        }

        return $this->getParentComponent()?->getModel();
    }

    public function getRecord(): ?Model
    {
        $model = $this->model;

        if ($model instanceof Model) {
            return $model;
        }

        return $this->getParentComponent()?->getRecord();
    }

    public function getModelInstance(): ?Model
    {
        if ($record = $this->getRecord()) {
            return $record;
        }

        $model = $this->getModel();

        if (! $model) {
            return null;
        }

        return new $model();
    }
}
