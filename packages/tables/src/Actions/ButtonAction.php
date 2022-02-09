<?php

namespace Filament\Tables\Actions;

use Closure;

class ButtonAction extends Action
{
    protected string $view = 'tables::actions.button-action';

    protected string | Closure | null $iconPosition = null;
    protected bool $isOnlyIcon = false;
    public function iconPosition(string | Closure | null $position): static
    {
        $this->iconPosition = $position;

        return $this;
    }

    public function getIconPosition(): ?string
    {
        return $this->evaluate($this->iconPosition);
    }
    
     public function onlyIcon(bool $condition = true): static
    {
        $this->isOnlyIcon = $condition;

        return $this;
    }
}
