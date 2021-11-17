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
            if ($component->getModel()) {
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

    public function getModel(): Model | string | null
    {
        if ($model = $this->model) {
            return $model;
        }

        return $this->getParentComponent()?->getModel();
    }
}
