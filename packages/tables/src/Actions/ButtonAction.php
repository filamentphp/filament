<?php

namespace Filament\Tables\Actions;

use Closure;

class ButtonAction extends Action
{
    protected string | Closure | null $iconPosition = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->button();
    }

    public function iconPosition(string | Closure | null $position): static
    {
        $this->iconPosition = $position;

        return $this;
    }

    public function getIconPosition(): ?string
    {
        return $this->evaluate($this->iconPosition);
    }
}
