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

        $this->afterStateHydrated(function (HasManyRepeater $component): void {
            if (count($this->getState() ?? [])) {
                return;
            }

            $relationship = $component->getRelationship();
            $relatedModels = $relationship->getResults();

            if (! $relatedModels) {
                return;
            }

            $component->state(
                $relatedModels->keyBy(
                    $relationship->getLocalKeyName(),
                )->toArray(),
            );
        });

        $this->dehydrated(false);

        $this->disableItemMovement();
    }

    public function relationship(string | Closure $name): static
    {
        $this->relationship = $name;

        return $this;
    }

    public function saveRelationships(): void
    {
        if ($this->saveRelationshipsUsing) {
            parent::saveRelationships();

            return;
        }

        $relationship = $this->getRelationship();
        $state = $this->getState();

        foreach ($relationship->pluck($relationship->getLocalKeyName()) as $keyToCheckForDeletion) {
            if (array_key_exists($keyToCheckForDeletion, $state)) {
                continue;
            }

            $relationship->find($keyToCheckForDeletion)?->delete();
        }

        $childComponentContainers = $this->getChildComponentContainers();

        foreach ($state as $itemKey => $itemData) {
            if ($record = $this->getRecordForItemKey($itemKey)) {
                $record->update($itemData);

                continue;
            }

            $record = $relationship->create($itemData);
            $childComponentContainers[$itemKey]->model($record)->saveRelationships();
        }
    }

    public function getChildComponentContainers(): array
    {
        return collect($this->getNormalisedState())
            ->map(function ($itemData, $itemKey): ComponentContainer {
                return $this
                    ->getChildComponentContainer()
                    ->getClone()
                    ->statePath($itemKey)
                    ->model($this->getRecordForItemKey($itemKey) ?? $this->getRelatedModel());
            })->toArray();
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
        $model = $this->getModel();

        if (is_string($model)) {
            $model = new $model();
        }

        return $model->{$this->getRelationshipName()}();
    }

    public function getRelationshipName(): string
    {
        return $this->evaluate($this->relationship);
    }

    protected function getRecordForItemKey($key): ?Model
    {
        return $this->getRelationship()->find($key);
    }

    protected function getRelatedModel(): string
    {
        return $this->getRelationship()->getModel()::class;
    }
}
