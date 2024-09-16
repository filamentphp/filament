<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait BelongsToModel
{
    protected Model | string | Closure | null $model = null;

    protected ?Closure $loadStateFromRelationshipsUsing = null;

    protected ?Closure $saveRelationshipsUsing = null;

    protected ?Closure $saveRelationshipsBeforeChildrenUsing = null;

    protected bool | Closure $shouldSaveRelationshipsWhenDisabled = false;

    protected bool | Closure $shouldSaveRelationshipsWhenHidden = false;

    public function model(Model | string | Closure | null $model = null): static
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

    public function loadStateFromRelationships(bool $andHydrate = false): void
    {
        $callback = $this->loadStateFromRelationshipsUsing;

        if (! $callback) {
            return;
        }

        if (! $this->getRecord()?->exists) {
            return;
        }

        $this->evaluate($callback);

        if ($andHydrate) {
            $this->callAfterStateHydrated();

            foreach ($this->getChildComponentContainers() as $container) {
                $container->callAfterStateHydrated();
            }

            $this->fillStateWithNull();
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

    public function getModel(): ?string
    {
        $model = $this->evaluate($this->model);

        if ($model instanceof Model) {
            return $model::class;
        }

        if (filled($model)) {
            return $model;
        }

        return $this->getContainer()->getModel();
    }

    public function getRecord(): ?Model
    {
        $model = $this->evaluate($this->model);

        if ($model instanceof Model) {
            return $model;
        }

        if (is_string($model)) {
            return null;
        }

        return $this->getContainer()->getRecord();
    }

    public function getModelInstance(): ?Model
    {
        $model = $this->evaluate($this->model);

        if ($model === null) {
            return $this->getContainer()->getModelInstance();
        }

        if ($model instanceof Model) {
            return $model;
        }

        return app($model);
    }
}
