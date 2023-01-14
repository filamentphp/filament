<?php

namespace Filament\Infolists\Components\Concerns;

trait EntanglesStateWithSingularRelationship
{
    public function relationship(string $relationshipName): static
    {
        $this->statePath($relationshipName);

        return $this;
    }
}
