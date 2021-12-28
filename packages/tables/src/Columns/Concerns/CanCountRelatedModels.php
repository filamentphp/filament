<?php

namespace Filament\Tables\Columns\Concerns;

trait CanCountRelatedModels
{
    protected ?string $relationshipToCount = null;

    public function counts(?string $relationship): static
    {
        $this->relationshipToCount = $relationship;

        return $this;
    }

    public function getRelationshipToCount(): ?string
    {
        return $this->relationshipToCount;
    }
}
