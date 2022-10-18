<?php

namespace Filament\Actions\Modal\Actions;

use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\CanOpenUrl;
use Filament\Actions\Concerns\CanSubmitForm;
use Filament\Actions\StaticAction;

class Action extends StaticAction
{
    use CanBeOutlined;
    use CanOpenUrl;
    use CanSubmitForm;
    use Concerns\CanCancelAction;
    use Concerns\HasAction;

    protected string $view = 'filament-actions::modal.actions.button-action';

    public function button(): static
    {
        $this->view('filament-actions::modal.actions.button-action');

        return $this;
    }
}
