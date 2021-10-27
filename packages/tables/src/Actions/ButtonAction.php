<?php

namespace Filament\Tables\Actions;

class ButtonAction extends Action
{
    use Concerns\HasIcon;

    protected string $view = 'tables::actions.button-action';

    protected ?string $iconPosition = null;

    public function iconPosition(string $position): static
    {
        $this->iconPosition = $position;

        return $this;
    }

    public function getIconPosition(): ?string
    {
        return $this->iconPosition;
    }
}
