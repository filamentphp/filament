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

            $component->fillFromRelationship();
        });

        $this->saveRelationshipsUsing(function (HasManyRepeater $component, array $state) {
            $relationship = $component->getRelationship();

            foreach ($relationship->pluck($relationship->getLocalKeyName()) as $keyToCheckForDeletion) {
                if (array_key_exists($keyToCheckForDeletion, $state)) {
                    continue;
                }

                $relationship->find($keyToCheckForDeletion)?->delete();
            }

            $childComponentContainers = $component->getChildComponentContainers();

            foreach ($state as $itemKey => $itemData) {
                if ($record = $component->getRecordForItemKey($itemKey)) {
                    $record->update($itemData);

                    continue;
                }

                $record = $relationship->create($itemData);
                $childComponentContainers[$itemKey]->model($record)->saveRelationships();
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

    public function getChildComponentContainers(): array
    {
        return collect($this->getState())
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
