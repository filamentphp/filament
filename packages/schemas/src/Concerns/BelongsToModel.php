<?php

namespace Filament\Schemas\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait BelongsToModel
{
    /**
     * @var Model | array<string, mixed> | class-string<Model> | Closure | null
     */
    public Model | array | string | Closure | null $model = null;

    /**
     * @param  Model | array<string, mixed> | class-string<Model> | Closure | null  $model
     */
    public function model(Model | array | string | Closure | null $model = null): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param  Model | array<string, mixed> | Closure | null  $record
     */
    public function record(Model | array | Closure | null $record): static
    {
        $this->model($record);

        return $this;
    }

    public function saveRelationships(): void
    {
        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            $component->saveRelationshipsBeforeChildren();

            $shouldSaveRelationshipsWhenDisabled = $component->shouldSaveRelationshipsWhenDisabled();

            foreach ($component->getChildSchemas(withHidden: $component->shouldSaveRelationshipsWhenHidden()) as $childSchema) {
                if ((! $shouldSaveRelationshipsWhenDisabled) && $childSchema->isDisabled()) {
                    continue;
                }

                $childSchema->saveRelationships();
            }

            $component->saveRelationships();
        }
    }

    public function loadStateFromRelationships(bool $shouldHydrate = false): void
    {
        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            $component->loadStateFromRelationships($shouldHydrate);

            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                $childSchema->loadStateFromRelationships($shouldHydrate);
            }
        }
    }

    /**
     * @return class-string<Model>|null
     */
    public function getModel(): ?string
    {
        $this->model = $this->evaluate($this->model);

        if ($this->model instanceof Model) {
            return $this->model::class;
        }

        if (is_array($this->model)) {
            return null;
        }

        if (filled($this->model)) {
            return $this->model;
        }

        return $this->getParentComponent()?->getModel();
    }

    /**
     * @return Model | array<string, mixed> | null
     */
    public function getRecord(bool $withParentComponentRecord = true): Model | array | null
    {
        $this->model = $this->evaluate($this->model);

        if (($this->model instanceof Model) || is_array($this->model)) {
            return $this->model;
        }

        if (is_string($this->model)) {
            return null;
        }

        if (! $withParentComponentRecord) {
            return null;
        }

        return $this->getParentComponent()?->getRecord();
    }

    public function getModelInstance(): ?Model
    {
        $this->model = $this->evaluate($this->model);

        if (($this->model === null) || is_array($this->model)) {
            return $this->getParentComponent()?->getModelInstance();
        }

        if ($this->model instanceof Model) {
            return $this->model;
        }

        return app($this->model);
    }
}
