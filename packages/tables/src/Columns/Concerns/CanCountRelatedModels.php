<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanCountRelatedModels
{
    protected string | Closure | null $relationshipToCount = null;

    public function counts(string | Closure | null $relationship): static
    {
        $this->relationshipToCount = $relationship;

        return $this;
    }

    public function getRelationshipToCount(): ?string
    {
        return $this->evaluate($this->relationshipToCount);
    }
}
