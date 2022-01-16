<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\ComponentContainer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class HasManyRepeater extends Repeater
{
    protected string | Closure | null $relationship = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (HasManyRepeater $component, ?array $state): void {
            if (count($state ?? [])) {
                return;
            }

            $component->fillFromRelationship();
        });

        $this->saveRelationshipsUsing(function (HasManyRepeater $component, ?array $state) {
            if (! is_array($state)) {
                $state = [];
            }

            $relationship = $component->getRelationship();
            $existingRecords = $relationship->get();

            $recordsToDelete = [];
            $localKeyName = $relationship->getLocalKeyName();

            foreach ($existingRecords->pluck($localKeyName) as $keyToCheckForDeletion) {
                if (array_key_exists($keyToCheckForDeletion, $state)) {
                    continue;
                }

                $recordsToDelete[] = $keyToCheckForDeletion;
            }

            $relationship->whereIn($localKeyName, $recordsToDelete)->delete();

            $childComponentContainers = $component->getChildComponentContainers();

            foreach ($childComponentContainers as $itemKey => $item) {
                $itemData = $item->getState();

                if ($record = ($existingRecords[$itemKey] ?? null)) {
                    $record->update($itemData);

                    continue;
                }

                $record = $relationship->create($itemData);
                $item->model($record)->saveRelationships();
            }

            $component->fillFromRelationship();
        });

        $this->dehydrated(false);

        $this->disableItemMovement();
    }

    public function relationship(string | Closure $name): static
    {
        $this->relationship = $name;

        return $this;
    }

    public function fillFromRelationship(): void
    {
        $relationship = $this->getRelationship();
        $relatedModels = $relationship->getResults();

        if (! $relatedModels) {
            return;
        }

        $this->state(
            $relatedModels->keyBy(
                $relationship->getLocalKeyName(),
            )->toArray(),
        );
    }

    public function getChildComponentContainers(bool $withHidden = false): array
    {
        $records = $this->getRelationship()->get();

        return collect($this->getState())
            ->map(function ($itemData, $itemKey) use ($records): ComponentContainer {
                return $this
                    ->getChildComponentContainer()
                    ->getClone()
                    ->statePath($itemKey)
                    ->model($records[$itemKey] ?? $this->getRelatedModel());
            })
            ->toArray();
    }

    public function getLabel(): string
    {
        if ($this->label === null) {
            return (string) Str::of($this->getRelationshipName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
        }

        return parent::getLabel();
    }

    public function getRelationship(): HasMany
    {
        return $this->getModelInstance()->{$this->getRelationshipName()}();
    }

    public function getRelationshipName(): string
    {
        return $this->evaluate($this->relationship);
    }

    protected function getRelatedModel(): string
    {
        return $this->getRelationship()->getModel()::class;
    }
}
