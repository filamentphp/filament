<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Relations\Relation;

trait InteractsWithRelationship
{
    protected Relation | Closure | null $relationship = null;

    protected Relation | Closure | null $inverseRelationship = null;

    public function relationship(Relation | Closure | null $relationship = null): static
    {
        $this->relationship = $relationship;

        return $this;
    }

    public function inverseRelationship(Relation | Closure | null $relationship = null): static
    {
        $this->inverseRelationship = $relationship;

        return $this;
    }

    public function getRelationship(): ?Relation
    {
        return $this->evaluate($this->relationship);
    }

    public function getInverseRelationship(): ?Relation
    {
        return $this->evaluate($this->inverseRelationship);
    }

    public function hasRelationship(): bool
    {
        return (bool) $this->evaluate($this->relationship);
    }

    public function hasInverseRelationship(): bool
    {
        return (bool) $this->evaluate($this->inverseRelationship);
    }
}
