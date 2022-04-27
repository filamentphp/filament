<?php

namespace Filament\Pages\Actions;

use Filament\Support\Actions\Action as BaseAction;
use Filament\Support\Actions\Concerns\CanBeDisabled;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\CanOpenUrl;
use Filament\Support\Actions\Concerns\HasTooltip;
use Filament\Support\Actions\Concerns\CanSubmitForm;

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
        $this->button();

        $this->configure();
    }

    public function button(): static
    {
        $this->view('filament::pages.actions.button-action');

        return $this;
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
