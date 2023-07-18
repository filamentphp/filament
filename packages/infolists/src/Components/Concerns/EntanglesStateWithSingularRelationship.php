<?php

namespace Filament\Infolists\Components\Concerns;

trait EntanglesStateWithSingularRelationship
{
    public function relationship(string $name): static
    {
        $this->statePath($name);

        return $this;
    }
}
