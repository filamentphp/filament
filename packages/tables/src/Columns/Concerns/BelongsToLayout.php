<?php

namespace Filament\Tables\Columns\Concerns;

use Filament\Tables\Columns\Layout\Component;

trait BelongsToLayout
{
    protected ?Component $layout = null;

    public function layout(?Component $layout): static
    {
        $this->layout = $layout;

        return $this;
    }

    public function getLayout(): ?Component
    {
        return $this->layout;
    }
}
