<?php

namespace Filament\Actions\Concerns;

use Filament\Schema\Components\Component;

trait BelongsToComponent
{
    protected ?Component $component = null;

    public function component(?Component $component = null): static
    {
        $this->component = $component;

        return $this;
    }

    public function getComponent(): ?Component
    {
        return $this->component;
    }
}
