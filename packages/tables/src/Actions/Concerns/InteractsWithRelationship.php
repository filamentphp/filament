<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

trait InteractsWithRelationship
{
    protected Relation | Closure | null $relationship = null;

    protected string | Closure | null $inverseRelationshipName = null;

    public function relationship(Relation | Closure | null $relationship = null): static
    {
        $this->relationship = $relationship;

        return $this;
    }

    public function inverseRelationshipName(string | Closure | null $relationship = null): static
    {
        $this->inverseRelationshipName = $relationship;

        return $this;
    }

    public function getRelationship(): Relation | Builder | null
    {
        $relationship = $this->evaluate($this->relationship);

        if ($relationship) {
            return $relationship;
        }

        $livewire = $this->getLivewire();

        if (! $livewire instanceof HasRelationshipTable) {
            return null;
        }

        return $livewire->getRelationship();
    }

    public function getInverseRelationshipName(): ?string
    {
        $name = $this->evaluate($this->inverseRelationshipName);

        if (filled($name)) {
            return $name;
        }

        $livewire = $this->getLivewire();

        if (! $livewire instanceof HasRelationshipTable) {
            return null;
        }

        return $livewire->getInverseRelationshipName();
    }

    public function getInverseRelationshipFor(Model $record): Relation | Builder
    {
        return $record->{$this->getInverseRelationshipName()}();
    }

    public function hasRelationship(): bool
    {
        return (bool) $this->evaluate($this->relationship);
    }
}
