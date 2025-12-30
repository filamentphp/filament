<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait BelongsToModel
{
    /**
     * @var Model | array<string, mixed> | class-string<Model> | Closure | null
     */
    protected Model | array | string | Closure | null $model = null;

    protected ?Closure $loadStateFromRelationshipsUsing = null;

    protected ?Closure $saveRelationshipsUsing = null;

    protected ?Closure $saveRelationshipsBeforeChildrenUsing = null;

    protected bool | Closure $shouldSaveRelationshipsWhenDisabled = false;

    protected bool | Closure $shouldSaveRelationshipsWhenHidden = false;

    /**
     * @param  Model | array<string, mixed> | class-string<Model> | Closure | null  $model
     */
    public function model(Model | array | string | Closure | null $model = null): static
    {
        $this->model = $model;

        return $this;
    }

    public function saveRelationships(): void
    {
        $callback = $this->saveRelationshipsUsing;

        if (! $callback) {
            return;
        }

        if (! $this->isSaved()) {
            return;
        }

        if (! ($this->getRecord()?->exists)) {
            return;
        }

        if ((! $this->shouldSaveRelationshipsWhenDisabled()) && $this->isDisabled()) {
            return;
        }

        if ((! $this->shouldSaveRelationshipsWhenHidden()) && $this->isHidden()) {
            return;
        }

        $this->evaluate($callback);
    }

    public function saveRelationshipsBeforeChildren(): void
    {
        $callback = $this->saveRelationshipsBeforeChildrenUsing;

        if (! $callback) {
            return;
        }

        if (! $this->isSaved()) {
            return;
        }

        if (! ($this->getRecord()?->exists)) {
            return;
        }

        if ((! $this->shouldSaveRelationshipsWhenDisabled()) && $this->isDisabled()) {
            return;
        }

        if ((! $this->shouldSaveRelationshipsWhenHidden()) && $this->isHidden()) {
            return;
        }

        $this->evaluate($callback);
    }

    public function loadStateFromRelationships(bool $shouldHydrate = false): void
    {
        $callback = $this->loadStateFromRelationshipsUsing;

        if (! $callback) {
            return;
        }

        if (! $this->getRecord()?->exists) {
            return;
        }

        $this->evaluate($callback);

        if ($shouldHydrate) {
            $this->castStateAfterLoadingFromRelationships();

            $this->callAfterStateHydrated();

            foreach ($this->getChildSchemas(withHidden: true) as $childSchema) {
                $childSchema->callAfterStateHydrated();
            }

            $this->fillStateWithNull();
        }
    }

    public function castStateAfterLoadingFromRelationships(): void
    {
        foreach ($this->getChildSchemas(withHidden: true) as $childSchema) {
            foreach ($childSchema->getComponents(withActions: false, withHidden: true) as $component) {
                $component->castStateAfterLoadingFromRelationships();
            }
        }

        $rawState = $this->getRawState();
        $originalRawState = $rawState;

        foreach ($this->getStateCasts() as $stateCast) {
            $rawState = $stateCast->set($rawState);
        }

        if ($rawState !== $originalRawState) {
            $this->rawState($rawState);
        }
    }

    public function saveRelationshipsUsing(?Closure $callback): static
    {
        $this->saveRelationshipsUsing = $callback;

        return $this;
    }

    public function saveRelationshipsBeforeChildrenUsing(?Closure $callback): static
    {
        $this->saveRelationshipsBeforeChildrenUsing = $callback;

        return $this;
    }

    public function saveRelationshipsWhenDisabled(bool | Closure $condition = true): static
    {
        $this->shouldSaveRelationshipsWhenDisabled = $condition;

        return $this;
    }

    public function shouldSaveRelationshipsWhenDisabled(): bool
    {
        return (bool) $this->evaluate($this->shouldSaveRelationshipsWhenDisabled);
    }

    public function saveRelationshipsWhenHidden(bool | Closure $condition = true): static
    {
        $this->shouldSaveRelationshipsWhenHidden = $condition;

        return $this;
    }

    public function shouldSaveRelationshipsWhenHidden(): bool
    {
        return (bool) $this->evaluate($this->shouldSaveRelationshipsWhenHidden);
    }

    public function loadStateFromRelationshipsUsing(?Closure $callback): static
    {
        $this->loadStateFromRelationshipsUsing = $callback;

        return $this;
    }

    /**
     * @return class-string<Model>|null
     */
    public function getModel(): ?string
    {
        $model = $this->evaluate($this->model);

        if ($model instanceof Model) {
            return $model::class;
        }

        if (is_array($model)) {
            return null;
        }

        if (filled($model)) {
            return $model;
        }

        return $this->getContainer()->getModel();
    }

    /**
     * @return Model | array<string, mixed> | null
     */
    public function getRecord(bool $withContainerRecord = true): Model | array | null
    {
        $model = $this->evaluate($this->model);

        if (($model instanceof Model) || is_array($model)) {
            return $model;
        }

        if (is_string($model)) {
            return null;
        }

        if (! $withContainerRecord) {
            return null;
        }

        return $this->getContainer()->getRecord();
    }

    public function getModelInstance(): ?Model
    {
        $model = $this->evaluate($this->model);

        if (($model === null) || is_array($model)) {
            return $this->getContainer()->getModelInstance();
        }

        if ($model instanceof Model) {
            return $model;
        }

        return app($model);
    }
}
