<?php

namespace Filament\Actions\Concerns;

use Filament\Actions\ActionGroup;

trait BelongsToGroup
{
    protected ?ActionGroup $group = null;

    public function group(?ActionGroup $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function getGroup(): ?ActionGroup
    {
        return $this->group;
    }
}
