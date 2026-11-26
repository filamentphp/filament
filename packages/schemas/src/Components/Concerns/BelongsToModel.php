<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Filament\Schemas\Schema;
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

    protected ?bool $cachedRelationshipSaveEligibility = null;

    /**
     * @var array<Schema> | null
     */
    protected ?array $cachedChildSchemasForRelationshipSaving = null;

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

        if (! $this->shouldSaveRelationships()) {
            return;
        }

        if (! ($this->getRecord()?->exists)) {
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

        if (! $this->shouldSaveRelationships()) {
            return;
        }

        if (! ($this->getRecord()?->exists)) {
            return;
        }

        $this->evaluate($callback);
    }

    /**
     * @return array<Schema>
     *
     * @internal
     */
    public function cacheRelationshipSaveEligibility(): array
    {
        $this->cachedRelationshipSaveEligibility = null;
        $this->cachedChildSchemasForRelationshipSaving = null;

        if ($this->saveRelationshipsUsing || $this->saveRelationshipsBeforeChildrenUsing) {
            $this->cachedRelationshipSaveEligibility = $this->shouldSaveRelationships();
        }

        $shouldSaveRelationshipsWhenDisabled = $this->shouldSaveRelationshipsWhenDisabled();

        return $this->cachedChildSchemasForRelationshipSaving = array_filter(
            $this->getChildSchemas(withHidden: $this->shouldSaveRelationshipsWhenHidden()),
            static fn (Schema $schema): bool => $shouldSaveRelationshipsWhenDisabled || (! $schema->isDisabled()),
        );
    }

    /**
     * @return array<Schema>
     *
     * @internal
     */
    public function getChildSchemasForRelationshipSaving(): array
    {
        $childSchemas = $this->getChildSchemas(withHidden: $this->shouldSaveRelationshipsWhenHidden());

        if ($this->cachedChildSchemasForRelationshipSaving === null) {
            return $childSchemas;
        }

        $cachedChildSchemas = [];

        foreach ($this->cachedChildSchemasForRelationshipSaving as $key => $childSchema) {
            if (! isset($childSchemas[$key])) {
                continue;
            }

            // A singular relationship can rebuild its schema after creating its record.
            // Keep the submitted components and their eligibility, but bind the new model.
            if ($childSchema !== $childSchemas[$key]) {
                $childSchema->model($childSchemas[$key]->model);

                // Array-backed schemas share components that may have been reparented.
                foreach ($childSchema->getComponents(withActions: false, withHidden: true) as $component) {
                    $component->container($childSchema);
                }
            }

            $cachedChildSchemas[$key] = $childSchema;
        }

        return $cachedChildSchemas;
    }

    /**
     * @internal
     */
    public function shouldSaveRelationships(): bool
    {
        if ($this->cachedRelationshipSaveEligibility === false) {
            return false;
        }

        $isDisabled = $this->isDisabled();
        $isHidden = $this->isHidden();
        $isSaved = $this->isSaved();

        if ($isDisabled && (! $this->shouldSaveRelationshipsWhenDisabled()) && (! $isSaved)) {
            return false;
        }

        if ($isHidden && (! $this->shouldSaveRelationshipsWhenHidden())) {
            return false;
        }

        return $isDisabled || $isHidden || $isSaved;
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
