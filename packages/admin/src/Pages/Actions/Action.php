<?php

namespace Filament\Pages\Actions;

use Filament\Pages\Actions\Modal\Actions\Action as ModalAction;
use Filament\Support\Actions\Action as BaseAction;
use Filament\Support\Actions\Concerns\CanBeDisabled;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\CanOpenUrl;
use Filament\Support\Actions\Concerns\CanSubmitForm;
use Filament\Support\Actions\Concerns\HasTooltip;

class Action extends BaseAction
{
    use CanBeDisabled;
    use CanBeOutlined;
    use CanOpenUrl;
    use CanSubmitForm;
    use Concerns\BelongsToLivewire;
    use HasTooltip;

    protected function setUp(): void
    {
        $this->view ?? $this->button();

        parent::setUp();
    }

    public function button(): static
    {
        $this->view('filament::pages.actions.button-action');

        return $this;
    }

    protected function getLivewireSubmitActionName(): string
    {
        return 'callMountedAction';
    }

    protected function getModalActionClass(): string
    {
        return ModalAction::class;
    }

    public function call(array $data = [])
    {
        if ($this->isHidden() || $this->isDisabled()) {
            return;
        }

        return app()->call($this->getAction(), [
            'data' => $data,
        ]);
    }
}
