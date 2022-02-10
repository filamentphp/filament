<?php

namespace Filament\Pages\Actions;

class ButtonAction extends Action
{
    use Concerns\CanBeOutlined;
    use Concerns\CanSubmitForm;
    use Concerns\HasIcon;

    protected string $view = 'filament::pages.actions.button-action';

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
