<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait EntanglesStateWithSingularRelationship
{
    protected ?Model $cachedExistingRecord = null;

    protected string | null $relationship = null;

    public function relationship(string $relationshipName): static
    {
        $this->relationship = $relationshipName;
        $this->statePath($relationshipName);

        $this->loadStateFromRelationshipsUsing(static function (Component $component) {
            /** @var Component|CanEntangleWithSingularRelationships $component */

            $component->clearCachedExistingRecord();

            $component->fillFromRelationship();
        });

        $this->saveRelationshipsUsing(static function (Component $component, $state): void {
            /** @var Component|CanEntangleWithSingularRelationships $component */

            $record = $component->getCachedExistingRecord();

            if ($record) {
                $record->update($state);

                return;
            }

            $component->getRelationship()->create($state);
        });

        $this->dehydrated(false);

        return $this;
    }

    public function fillFromRelationship(): void
    {
        $this->getChildComponentContainer()->fill(
            $this->getCachedExistingRecord()?->toArray(),
        );
    }

    public function getChildComponentContainer(): ComponentContainer
    {
        $relationship = $this->getRelationship();

        if (! $relationship) {
            return parent::getChildComponentContainer();
        }

        return parent::getChildComponentContainer()
            ->model($this->getCachedExistingRecord() ?? $this->getRelatedModel());
    }

    public function getRelationship(): BelongsTo | HasOne | MorphOne | null
    {
        $name = $this->getRelationshipName();

        if (blank($name)) {
            return null;
        }

        return $this->getModelInstance()->{$name}();
    }

    public function getRelationshipName(): ?string
    {
        return $this->relationship;
    }

    public function getRelatedModel(): ?string
    {
        return $this->getRelationship()?->getModel()::class;
    }

    public function getCachedExistingRecord(): ?Model
    {
        if ($this->cachedExistingRecord) {
            return $this->cachedExistingRecord;
        }

        $record = $this->getRelationship()?->getResults();

        if (! $record?->exists) {
            return null;
        }

        return $this->cachedExistingRecord = $record;
    }

    public function clearCachedExistingRecord(): void
    {
        $this->cachedExistingRecord = null;
    }
}
