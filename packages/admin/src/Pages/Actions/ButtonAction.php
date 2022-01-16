<?php

namespace Filament\Pages\Actions;

use Closure;

class ButtonAction extends Action
{
    use Concerns\CanBeMounted;
    use Concerns\CanOpenModal;
    use Concerns\CanOpenUrl;
    use Concerns\CanRequireConfirmation;
    use Concerns\CanSubmitForm;
    use Concerns\HasAction;
    use Concerns\HasColor;
    use Concerns\HasFormSchema;
    use Concerns\HasIcon;

    protected string $view = 'filament::components.actions.button-action';

    protected ?string $iconPosition = null;

    public function call(array $data = [])
    {
        if ($this->isHidden()) {
            return;
        }

        $action = $this->getAction();

        if (is_string($action)) {
            $action = Closure::fromCallable([$this->getLivewire(), $action]);
        }

        return app()->call($action, [
            'data' => $data,
        ]);
    }

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
