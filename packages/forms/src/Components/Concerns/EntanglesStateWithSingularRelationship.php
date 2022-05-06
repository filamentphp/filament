<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait EntanglesStateWithSingularRelationship
{
    protected string | null $relationship = null;

    public function relationship(string $relationshipName): static
    {
        $this->relationship = $relationshipName;
        $this->statePath($relationshipName);

        $this->afterStateHydrated(function (Component $component) {
            /** @var Component|CanEntangleWithSingularRelationships $component */

            $component->getChildComponentContainer()->fill(
                $component->getRelatedRecord()?->toArray(),
            );
        });

        $this->saveRelationshipsUsing(function (Component $component, $state) {
            /** @var Component|CanEntangleWithSingularRelationships $component */

            $relationship = $component->getRelationship();

            $relationship->exists()
                ? $component->getRelatedRecord()->update($state)
                : $relationship->create($state);
        });

        $this->dehydrated(false);

        return $this;
    }

    public function getChildComponentContainer(): ComponentContainer
    {
        $relationship = $this->getRelationship();

        if (! $relationship) {
            return parent::getChildComponentContainer();
        }

        return parent::getChildComponentContainer()
            ->model(
                $relationship->exists() ?
                    $relationship->getRelated() :
                    $relationship->getModel(),
            );
    }

    public function getRelationship(): ?HasOne
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

    public function getRelatedRecord(): ?Model
    {
        return $this->getRelationship()?->getResults();
    }
}
