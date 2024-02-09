<?php

namespace Filament\Forms\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToModel
{
    public Model | string | null $model = null;

    public function model(Model | string | null $model = null): static
    {
        $this->model = $model;

        return $this;
    }

    public function syncRelationships($syncedRelationships = []): array
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            $component->saveRelationshipsBeforeChildren();

            $shouldSaveRelationshipsWhenDisabled = $component->shouldSaveRelationshipsWhenDisabled();

            foreach ($component->getChildComponentContainers(withHidden: $component->shouldSaveRelationshipsWhenHidden()) as $container) {
                if ((! $shouldSaveRelationshipsWhenDisabled) && $container->isDisabled()) {
                    continue;
                }

                $syncedRelationships = $container->syncRelationships($syncedRelationships);
            }
            if (
                method_exists($component, 'getRelationship')
                && method_exists($component, 'getName')
                && $component->getRelationship() instanceof BelongsTo
            ) {
                $component->saveRelationships(false);
                $syncedRelationships[$component->getName()] = $component->getRelationship();
            }
        }

        return $syncedRelationships;
    }

    public function saveRelationships(): void
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            $component->saveRelationshipsBeforeChildren();

            $shouldSaveRelationshipsWhenDisabled = $component->shouldSaveRelationshipsWhenDisabled();

            foreach ($component->getChildComponentContainers(withHidden: $component->shouldSaveRelationshipsWhenHidden()) as $container) {
                if ((! $shouldSaveRelationshipsWhenDisabled) && $container->isDisabled()) {
                    continue;
                }

                $container->saveRelationships();
            }

            $component->saveRelationships();
        }
    }

    public function getRelationships($relations = []): array
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            foreach ($component->getChildComponentContainers(withHidden: true) as $container) {
                $relations = $container->getRelationships($relations);
            }
            if (method_exists($component, 'getRelationship') && method_exists($component, 'getName')) {
                $relations[$component->getName()] = $component->getRelationship();
            }
        }

        return $relations;
    }

    public function loadStateFromRelationships(bool $andHydrate = false): void
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            $component->loadStateFromRelationships($andHydrate);

            foreach ($component->getChildComponentContainers(withHidden: true) as $container) {
                $container->loadStateFromRelationships($andHydrate);
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

        if (is_string($model)) {
            return null;
        }

        return $this->getParentComponent()?->getRecord();
    }

    public function getModelInstance(): ?Model
    {
        $model = $this->model;

        if ($model === null) {
            return $this->getParentComponent()?->getModelInstance();
        }

        if ($model instanceof Model) {
            return $model;
        }

        return app($model);
    }
}
